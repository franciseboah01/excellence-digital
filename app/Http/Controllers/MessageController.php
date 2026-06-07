<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Mail\NouveauMessageMail;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    // ===== HELPER : construire les données de conversations =====
    private function construireConversations(int $userId): \Illuminate\Support\Collection
    {
        // Récupérer les IDs des interlocuteurs distincts
        $interlocuteursIds = Message::where('expediteur_id', $userId)
            ->orWhere('destinataire_id', $userId)
            ->get(['expediteur_id', 'destinataire_id'])
            ->flatMap(fn($m) => [$m->expediteur_id, $m->destinataire_id])
            ->unique()
            ->filter(fn($id) => $id !== $userId)
            ->values();

        // Charger tous les interlocuteurs en une seule requête
        $interlocuteurs = User::whereIn('id', $interlocuteursIds)
            ->get(['id', 'nom', 'prenom', 'avatar']);

        // Construire les données de chaque conversation
        return $interlocuteurs->map(function ($interlocuteur) use ($userId) {
            $dernierMessage = Message::where(function ($q) use ($userId, $interlocuteur) {
                $q->where('expediteur_id', $userId)
                  ->where('destinataire_id', $interlocuteur->id);
            })->orWhere(function ($q) use ($userId, $interlocuteur) {
                $q->where('expediteur_id', $interlocuteur->id)
                  ->where('destinataire_id', $userId);
            })->latest()->first();

            $nonLus = Message::where('expediteur_id', $interlocuteur->id)
                ->where('destinataire_id', $userId)
                ->where('lu', false)
                ->count();

            return [
                'interlocuteur'   => $interlocuteur,
                'dernier_message' => $dernierMessage,
                'non_lus'         => $nonLus,
            ];
        })
        ->sortByDesc(fn($c) => $c['dernier_message']?->created_at)
        ->values();
    }

    // ===== HELPER : contacts disponibles selon le rôle =====
    private function getContacts(): \Illuminate\Database\Eloquent\Collection
    {
        $user = auth()->user();

        $roles = match(true) {
            $user->hasRole('admin')      => ['client', 'enseignant'],
            $user->hasRole('enseignant') => ['client', 'admin'],
            default                      => ['admin', 'enseignant'],
        };

        return User::whereHas('roles', fn($q) => $q->whereIn('name', $roles))
            ->where('statut', 'actif')
            ->get(['id', 'nom', 'prenom', 'avatar']);
    }

    // ===== LISTE DES CONVERSATIONS =====
    public function index()
    {
        $userId           = auth()->id();
        $conversationsData = $this->construireConversations($userId);
        $contacts          = $this->getContacts();

        return view('messages.index', compact('conversationsData', 'contacts'));
    }

    // ===== CONVERSATION AVEC UN UTILISATEUR =====
    public function conversation(User $user)
    {
        $moi = auth()->id();

        // Marquer les messages reçus comme lus
        Message::where('expediteur_id', $user->id)
            ->where('destinataire_id', $moi)
            ->where('lu', false)
            ->update(['lu' => true, 'lu_le' => now()]);

        // Charger la conversation (eager loading)
        $messages = Message::where(function ($q) use ($moi, $user) {
                $q->where('expediteur_id', $moi)
                  ->where('destinataire_id', $user->id);
            })
            ->orWhere(function ($q) use ($moi, $user) {
                $q->where('expediteur_id', $user->id)
                  ->where('destinataire_id', $moi);
            })
            ->with(['expediteur:id,nom,prenom,avatar', 'destinataire:id,nom,prenom'])
            ->orderBy('created_at')
            ->get();

        $conversationsData = $this->construireConversations($moi);
        $contacts          = $this->getContacts();

        return view('messages.conversation', compact(
            'messages', 'user', 'contacts', 'conversationsData'
        ));
    }

    // ===== ENVOYER UN MESSAGE =====
    public function envoyer(StoreMessageRequest $request)
    {
        $destinataire = User::findOrFail($request->destinataire_id);
        $expediteur   = auth()->user();

        $message = Message::create([
            'expediteur_id'   => $expediteur->id,
            'destinataire_id' => $destinataire->id,
            'contenu'         => e($request->contenu),
        ]);

        // Notification interne
        Notification::create([
            'user_id' => $destinataire->id,
            'titre'   => "💬 Nouveau message de {$expediteur->prenom} {$expediteur->nom}",
            'message' => Str::limit($request->contenu, 80),
            'type'    => 'info',
            'data'    => [
                'message_id'     => $message->id,
                'expediteur_id'  => $expediteur->id,
                'expediteur_nom' => "{$expediteur->prenom} {$expediteur->nom}",
            ],
        ]);

        // Email de notification (silencieux si échec)
        try {
            Mail::to($destinataire->email)
                ->send(new NouveauMessageMail($expediteur, $destinataire, $request->contenu));
        } catch (\Throwable $e) {
            \Log::warning("Email message non envoyé : {$e->getMessage()}");
        }

        // Réponse AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id'         => $message->id,
                    'contenu'    => $message->contenu,
                    'created_at' => $message->created_at->format('H:i'),
                    'expediteur' => "{$expediteur->prenom} {$expediteur->nom}",
                ],
            ]);
        }

        return redirect()->route('messages.conversation', $destinataire)
            ->with('success', 'Message envoyé !');
    }

    // ===== COMPTER MESSAGES NON LUS (AJAX) =====
    public function compterNonLus()
    {
        $count = Message::where('destinataire_id', auth()->id())
            ->where('lu', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    // ===== SUPPRIMER UN MESSAGE =====
    public function destroy(Message $message)
    {
        abort_if($message->expediteur_id !== auth()->id(), 403);
        $message->delete();
        return back()->with('success', 'Message supprimé.');
    }
}
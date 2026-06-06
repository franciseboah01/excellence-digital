<?php

namespace App\Http\Controllers;

use App\Mail\NouveauMessageMail;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    // ===== LISTE DES CONVERSATIONS =====
    public function index()
    {
        $userId = auth()->id();

        // Récupérer tous les interlocuteurs distincts
        $conversations = Message::where('expediteur_id', $userId)
            ->orWhere('destinataire_id', $userId)
            ->with(['expediteur', 'destinataire'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($message) use ($userId) {
                // L'interlocuteur est l'autre personne
                return $message->expediteur_id === $userId
                    ? $message->destinataire
                    : $message->expediteur;
            })
            ->unique('id')
            ->values();

        // Pour chaque conversation, récupérer le dernier message et nb non lus
        $conversationsData = $conversations->map(function ($interlocuteur) use ($userId) {
            $dernierMessage = Message::where(function ($q) use ($userId, $interlocuteur) {
                    $q->where('expediteur_id', $userId)
                      ->where('destinataire_id', $interlocuteur->id);
                })->orWhere(function ($q) use ($userId, $interlocuteur) {
                    $q->where('expediteur_id', $interlocuteur->id)
                      ->where('destinataire_id', $userId);
                })
                ->latest()
                ->first();

            $nonLus = Message::where('expediteur_id', $interlocuteur->id)
                ->where('destinataire_id', $userId)
                ->where('lu', false)
                ->count();

            return [
                'interlocuteur'  => $interlocuteur,
                'dernier_message'=> $dernierMessage,
                'non_lus'        => $nonLus,
            ];
        })->sortByDesc(fn($c) => $c['dernier_message']?->created_at)->values();

        // Interlocuteurs disponibles selon le rôle
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            $contacts = User::whereHas('roles', fn($q) =>
                $q->whereIn('name', ['client', 'enseignant'])
            )->where('statut', 'actif')->get();
        } elseif ($user->hasRole('enseignant')) {
            // Enseignant peut écrire aux apprenants de ses formations + admin
            $contacts = User::whereHas('roles', fn($q) =>
                $q->whereIn('name', ['client', 'admin'])
            )->where('statut', 'actif')->get();
        } else {
            // Client peut écrire à l'admin et ses enseignants
            $contacts = User::whereHas('roles', fn($q) =>
                $q->whereIn('name', ['admin', 'enseignant'])
            )->where('statut', 'actif')->get();
        }

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

        // Charger la conversation
        $messages = Message::where(function ($q) use ($moi, $user) {
                $q->where('expediteur_id', $moi)
                  ->where('destinataire_id', $user->id);
            })->orWhere(function ($q) use ($moi, $user) {
                $q->where('expediteur_id', $user->id)
                  ->where('destinataire_id', $moi);
            })
            ->with(['expediteur', 'destinataire'])
            ->orderBy('created_at')
            ->get();

        // Contacts disponibles
        $authUser = auth()->user();
        if ($authUser->hasRole('admin')) {
            $contacts = User::whereHas('roles', fn($q) =>
                $q->whereIn('name', ['client', 'enseignant'])
            )->where('statut', 'actif')->get();
        } elseif ($authUser->hasRole('enseignant')) {
            $contacts = User::whereHas('roles', fn($q) =>
                $q->whereIn('name', ['client', 'admin'])
            )->where('statut', 'actif')->get();
        } else {
            $contacts = User::whereHas('roles', fn($q) =>
                $q->whereIn('name', ['admin', 'enseignant'])
            )->where('statut', 'actif')->get();
        }

        // Toutes les conversations
        $userId = $moi;
        $conversations = Message::where('expediteur_id', $userId)
            ->orWhere('destinataire_id', $userId)
            ->with(['expediteur', 'destinataire'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($message) use ($userId) {
                return $message->expediteur_id === $userId
                    ? $message->destinataire
                    : $message->expediteur;
            })
            ->unique('id')
            ->values();

        $conversationsData = $conversations->map(function ($interlocuteur) use ($userId) {
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
        })->sortByDesc(fn($c) => $c['dernier_message']?->created_at)->values();

        return view('messages.conversation', compact(
            'messages', 'user', 'contacts', 'conversationsData'
        ));
    }

    // ===== ENVOYER UN MESSAGE =====
    public function envoyer(Request $request)
    {
        $request->validate([
            'destinataire_id' => 'required|exists:users,id|different:' . auth()->id(),
            'contenu'         => 'required|string|max:1000',
        ]);

        $destinataire = User::findOrFail($request->destinataire_id);

        $message = Message::create([
            'expediteur_id'   => auth()->id(),
            'destinataire_id' => $destinataire->id,
            'contenu'         => e($request->contenu),
        ]);

        $expediteur = auth()->user();

        // Notification interne
        Notification::create([
            'user_id' => $destinataire->id,
            'titre'   => "💬 Nouveau message de {$expediteur->prenom} {$expediteur->nom}",
            'message' => \Str::limit($request->contenu, 80),
            'type'    => 'info',
            'data'    => [
                'message_id'     => $message->id,
                'expediteur_id'  => $expediteur->id,
                'expediteur_nom' => "{$expediteur->prenom} {$expediteur->nom}",
            ],
        ]);

        // Email de notification
        try {
            Mail::to($destinataire->email)
                ->send(new NouveauMessageMail($expediteur, $destinataire, $request->contenu));
        } catch (\Throwable $e) {
            // Silencieux — le message est quand même envoyé
        }

        // Si requête AJAX
        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => [
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
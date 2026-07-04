<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        
        $conversations = User::whereIn('id', function ($query) use ($userId) {
            $query->select('expediteur_id')
                ->from('messages')
                ->where('destinataire_id', $userId)
                ->union(
                    \DB::table('messages')
                        ->select('destinataire_id')
                        ->where('expediteur_id', $userId)
                );
        })
        ->where('id', '!=', $userId)
        ->get(['id', 'nom', 'prenom', 'avatar'])
        ->map(function ($user) use ($userId) {
            $lastMessage = Message::where(function ($q) use ($user, $userId) {
                $q->where('expediteur_id', $user->id)
                  ->where('destinataire_id', $userId);
            })->orWhere(function ($q) use ($user, $userId) {
                $q->where('expediteur_id', $userId)
                  ->where('destinataire_id', $user->id);
            })->latest()->first();

            return [
                'user' => $user,
                'last_message' => $lastMessage,
                'unread_count' => Message::where('expediteur_id', $user->id)
                    ->where('destinataire_id', $userId)
                    ->where('lu', false)
                    ->count()
            ];
        });

        return response()->json($conversations);
    }

    public function conversation(User $user, Request $request)
    {
        $messages = Message::where(function ($q) use ($user, $request) {
            $q->where('expediteur_id', $request->user()->id)
              ->where('destinataire_id', $user->id);
        })->orWhere(function ($q) use ($user, $request) {
            $q->where('expediteur_id', $user->id)
              ->where('destinataire_id', $request->user()->id);
        })
        ->latest()
        ->paginate(30);

        // Marquer comme lus
        Message::where('expediteur_id', $user->id)
            ->where('destinataire_id', $request->user()->id)
            ->where('lu', false)
            ->update(['lu' => true]);

        return response()->json($messages);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'destinataire_id' => 'required|exists:users,id',
            'contenu' => 'required|string',
        ]);

        $message = Message::create([
            'expediteur_id' => $request->user()->id,
            'destinataire_id' => $validated['destinataire_id'],
            'contenu' => $validated['contenu'],
        ]);

        return response()->json($message, 201);
    }

    public function destroy(Message $message, Request $request)
    {
        if ($message->expediteur_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $message->delete();

        return response()->json(['message' => 'Message supprimé.']);
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'count' => $request->user()->nb_messages_non_lus
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'message' => 'required|string',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'type' => 'nullable|in:info,succes,avertissement,erreur',
        ]);

        $notifications = [];
        foreach ($validated['user_ids'] as $userId) {
            $notifications[] = Notification::create([
                'user_id' => $userId,
                'titre' => $validated['titre'],
                'message' => $validated['message'],
                'type' => $validated['type'] ?? 'info',
            ]);
        }

        return response()->json([
            'message' => count($notifications) . ' notifications envoyées.',
            'count' => count($notifications)
        ]);
    }

    public function sendAll(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'message' => 'required|string',
            'role' => 'nullable|in:client,enseignant,admin',
            'type' => 'nullable|in:info,succes,avertissement,erreur',
        ]);

        $users = User::when($validated['role'] ?? null, fn($q) => $q->role($validated['role']))->get();
        
        $count = 0;
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'titre' => $validated['titre'],
                'message' => $validated['message'],
                'type' => $validated['type'] ?? 'info',
            ]);
            $count++;
        }

        return response()->json([
            'message' => "Notification envoyée à {$count} utilisateurs.",
            'count' => $count
        ]);
    }
}
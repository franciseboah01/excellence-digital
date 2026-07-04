<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'formations_actives' => $user->inscriptions()
                ->where('statut', 'actif')
                ->count(),
            'formations_terminees' => $user->inscriptions()
                ->where('statut', 'termine')
                ->count(),
            'qcms_passes' => $user->sessionsQcm()->count(),
            'certificats_obtenus' => $user->certificats()->count(),
            'messages_non_lus' => $user->nb_messages_non_lus,
            'dernieres_notifications' => $user->notifications()
                ->latest()
                ->take(5)
                ->get(),
            'prochaines_formations' => $user->inscriptions()
                ->with('formation:id,titre,image')
                ->where('statut', 'actif')
                ->latest()
                ->take(5)
                ->get()
                ->pluck('formation'),
        ]);
    }
}
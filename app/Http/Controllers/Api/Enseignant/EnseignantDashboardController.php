<?php

namespace App\Http\Controllers\Api\Enseignant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EnseignantDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'formations_assignees' => $user->formationsEnseignant()->count(),
            'ressources_creees' => $user->ressources()->count(),
            'qcms_creees' => \App\Models\Qcm::where('createur_id', $user->id)->count(),
            'etudiants_actifs' => \App\Models\InscriptionFormation::whereIn(
                'formation_id', 
                $user->formationsEnseignant()->pluck('formations.id')
            )->where('statut', 'actif')->distinct('user_id')->count(),
            'dernieres_activites' => \App\Models\InscriptionFormation::whereIn(
                'formation_id',
                $user->formationsEnseignant()->pluck('formations.id')
            )->with('user:id,nom,prenom,avatar', 'formation:id,titre')
            ->latest()
            ->take(10)
            ->get(),
            'notifications_non_lues' => $user->unreadNotifications()->count(),
        ]);
    }
}
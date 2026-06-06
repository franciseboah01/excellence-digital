<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\Notification;
use App\Models\Ressource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\MailService;

class EnseignantController extends Controller
{
        // ===== DASHBOARD =====
    public function dashboard()
    {
        $enseignant = auth()->user();

        $formations = Formation::whereHas('ressources', function ($q) use ($enseignant) {
            $q->where('enseignant_id', $enseignant->id);
        })->withCount([
            'inscriptions as total_apprenants' => fn($q) => $q->where('statut', 'valide'),
            'ressources as ressources_count'   => fn($q) => $q->where('enseignant_id', $enseignant->id),
        ])->get();

        $stats = [
            'formations'      => $formations->count(),
            'ressources'      => Ressource::where('enseignant_id', $enseignant->id)->count(),
            'apprenants'      => InscriptionFormation::whereIn(
                                    'formation_id',
                                    $formations->pluck('id')
                                )
                                ->where('statut', 'valide')
                                ->count(),
            'notifs_envoyees' => Notification::where(
                                    'data->expediteur_id',
                                    $enseignant->id
                                )->count(),
        ];

        // Répartition des ressources par type
        $repartitionTypes = Ressource::where('enseignant_id', $enseignant->id)
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        $dernieres_ressources = Ressource::where('enseignant_id', $enseignant->id)
            ->with(['formation', 'niveau'])
            ->latest()
            ->take(5)
            ->get();

        return view('enseignant.dashboard', compact(
            'stats',
            'formations',
            'dernieres_ressources',
            'repartitionTypes'
        ));
    }

    // ===== LISTE RESSOURCES =====
    public function ressourcesIndex()
    {
        $ressources = Ressource::where('enseignant_id', auth()->id())
            ->with(['formation', 'niveau'])
            ->latest()->paginate(12);

        $formations = Formation::whereHas('ressources', function ($q) {
            $q->where('enseignant_id', auth()->id());
        })->get();

        return view('enseignant.ressources.index', compact('ressources', 'formations'));
    }

    // ===== FORMULAIRE UPLOAD =====
    public function ressourcesCreate()
    {
        $formations = Formation::where('statut', 'publie')->with('niveaux')->get();
        return view('enseignant.ressources.create', compact('formations'));
    }

    // ===== ENREGISTRER RESSOURCE =====
    public function ressourcesStore(Request $request)
    {
        $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'niveau_id'    => 'nullable|exists:niveaux_formation,id',
            'type'         => 'required|in:pdf,ebook,lien,video,document',
            'titre'        => 'required|string|max:200',
            'description'  => 'nullable|string|max:500',
            'fichier'      => 'required_if:type,pdf,ebook,document|nullable|file|mimes:pdf,doc,docx,epub|max:20480',
            'lien_url'     => 'required_if:type,lien,video|nullable|url',
        ]);

        $fichier_path = null;

        // Upload du fichier
        if ($request->hasFile('fichier')) {
            $fichier_path = $request->file('fichier')->store(
                'ressources/' . $request->formation_id,
                'local'
            );
        }

        Ressource::create([
            'formation_id'  => $request->formation_id,
            'enseignant_id' => auth()->id(),
            'niveau_id'     => $request->niveau_id,
            'type'          => $request->type,
            'titre'         => $request->titre,
            'description'   => $request->description,
            'fichier_path'  => $fichier_path,
            'lien_url'      => $request->lien_url,
            'actif'         => true,
        ]);

        return redirect()->route('enseignant.ressources.index')
            ->with('success', 'Ressource ajoutée avec succès !');
    }

    // ===== MODIFIER RESSOURCE =====
    public function ressourcesEdit(Ressource $ressource)
    {
        // Vérifier que c'est bien sa ressource
        abort_if($ressource->enseignant_id !== auth()->id(), 403);

        $formations = Formation::where('statut', 'publie')->with('niveaux')->get();
        return view('enseignant.ressources.edit', compact('ressource', 'formations'));
    }

    // ===== METTRE À JOUR RESSOURCE =====
    public function ressourcesUpdate(Request $request, Ressource $ressource)
    {
        abort_if($ressource->enseignant_id !== auth()->id(), 403);

        $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'niveau_id'    => 'nullable|exists:niveaux_formation,id',
            'type'         => 'required|in:pdf,ebook,lien,video,document',
            'titre'        => 'required|string|max:200',
            'description'  => 'nullable|string|max:500',
            'fichier'      => 'nullable|file|mimes:pdf,doc,docx,epub|max:20480',
            'lien_url'     => 'nullable|url',
        ]);

        // Nouveau fichier uploadé
        if ($request->hasFile('fichier')) {
            if ($ressource->fichier_path) Storage::delete($ressource->fichier_path);
            $ressource->fichier_path = $request->file('fichier')->store(
                'ressources/' . $request->formation_id, 'local'
            );
        }

        $ressource->update([
            'formation_id' => $request->formation_id,
            'niveau_id'    => $request->niveau_id,
            'type'         => $request->type,
            'titre'        => $request->titre,
            'description'  => $request->description,
            'lien_url'     => $request->lien_url,
            'fichier_path' => $ressource->fichier_path,
        ]);

        return redirect()->route('enseignant.ressources.index')
            ->with('success', 'Ressource mise à jour avec succès !');
    }

    // ===== SUPPRIMER RESSOURCE =====
    public function ressourcesDestroy(Ressource $ressource)
    {
        abort_if($ressource->enseignant_id !== auth()->id(), 403);

        if ($ressource->fichier_path) {
            Storage::delete($ressource->fichier_path);
        }

        $ressource->delete();

        return back()->with('success', 'Ressource supprimée.');
    }

    // ===== FORMULAIRE NOTIFICATION =====
    public function notificationsForm()
    {
        $enseignant = auth()->user();

        $formations = Formation::whereHas('ressources', function ($q) use ($enseignant) {
            $q->where('enseignant_id', $enseignant->id);
        })->with(['inscriptions.user' => function ($q) {
            $q->where('statut', 'valide');
        }])->get();

        return view('enseignant.notifications', compact('formations'));
    }

    // ===== ENVOYER NOTIFICATION =====
    public function notificationsEnvoyer(Request $request)
    {
        $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'titre'        => 'required|string|max:150',
            'message'      => 'required|string|max:500',
            'type'         => 'required|in:info,success,warning',
        ]);

        $enseignant = auth()->user();

        // Vérifier que l'enseignant enseigne bien dans cette formation
        $aAcces = Ressource::where('enseignant_id', $enseignant->id)
            ->where('formation_id', $request->formation_id)
            ->exists();

        abort_if(!$aAcces, 403, 'Vous n\'enseignez pas dans cette formation.');

        // Récupérer les apprenants inscrits et validés
        $apprenants = InscriptionFormation::where('formation_id', $request->formation_id)
            ->where('statut', 'valide')
            ->with('user')
            ->get();

        $count = 0;
        foreach ($apprenants as $inscription) {
            Notification::create([
                'user_id' => $inscription->user_id,
                'titre'   => e($request->titre),
                'message' => e($request->message),
                'type'    => $request->type,
                'data'    => ['expediteur_id' => $enseignant->id, 'expediteur' => $enseignant->nom_complet],
            ]);
            $count++;
        }

        return back()->with('success', "✅ Notification envoyée à {$count} apprenant(s) !");
    }

    // ===== AJAX : niveaux par formation =====
    public function getNiveaux(Formation $formation)
    {
        $niveaux = $formation->niveaux()->orderBy('ordre')->get(['id', 'nom', 'ordre']);
        return response()->json($niveaux);
    }


// ===== EMAIL AUX APPRENANTS =====
    public function envoyerEmail(Request $request)
    {
        $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'sujet'        => 'required|string|max:200',
            'message'      => 'required|string|min:10',
        ]);

        $enseignant = auth()->user();

        abort_if(!$enseignant->hasRole('enseignant'), 403); // FIX 10

        $aAcces = \App\Models\Ressource::where('enseignant_id', $enseignant->id)
            ->where('formation_id', $request->formation_id)
            ->exists();

        abort_if(!$aAcces, 403, "Vous n'enseignez pas dans cette formation.");

        // FIX 5 : Récupérer directement les objets User sans requête supplémentaire
        $apprenants = InscriptionFormation::where('formation_id', $request->formation_id)
            ->where('statut', 'valide')
            ->with('user')
            ->get()
            ->map(fn($inscription) => $inscription->user)
            ->filter(); // Retirer les nulls éventuels

        $count = MailService::enseignantVersApprenants(
            $enseignant,
            $apprenants,
            $request->sujet,
            $request->message
        );

        return back()->with('success', "✅ Email envoyé à {$count} apprenant(s) !");
    }
}
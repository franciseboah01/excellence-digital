<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\NiveauFormation;
use App\Models\Notification;
use App\Models\Ressource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormationController extends Controller
{
    // ===== LISTE =====
    public function index()
    {
        $formations = Formation::withCount([
            'inscriptions',
            'inscriptions as inscrits_valides' => fn($q) => $q->where('statut', 'valide'),
            'ressources',
        ])->latest()->paginate(10);

        $stats = [
            'total'     => Formation::count(),
            'publiees'  => Formation::where('statut', 'publie')->count(),
            'brouillon' => Formation::where('statut', 'brouillon')->count(),
            'inscrits'  => InscriptionFormation::where('statut', 'valide')->count(),
        ];

        return view('admin.formations.index', compact('formations', 'stats'));
    }

    // ===== CRÉER =====
    public function create()
    {
        $enseignants = User::role('enseignant')->get();
        return view('admin.formations.create', compact('enseignants'));
    }

    // ===== ENREGISTRER =====
    public function store(Request $request)
    {
        $request->validate([
            'titre'       => 'required|string|max:200',
            'description' => 'required|string',
            'niveau'      => 'required|in:debutant,intermediaire,avance',
            'duree'       => 'nullable|string|max:50',
            'statut'      => 'required|in:publie,brouillon',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('formations', 'public');
        }

        $formation = Formation::create([
            'titre'       => $request->titre,
            'description' => $request->description,
            'niveau'      => $request->niveau,
            'duree'       => $request->duree,
            'statut'      => $request->statut,
            'image'       => $imagePath,
        ]);

        // Créer les niveaux par défaut
        $niveauxDefaut = [
            ['nom' => 'Introduction',    'ordre' => 1, 'description' => 'Bases et prise en main'],
            ['nom' => 'Développement',   'ordre' => 2, 'description' => 'Approfondissement'],
            ['nom' => 'Perfectionnement','ordre' => 3, 'description' => 'Maîtrise avancée'],
        ];

        foreach ($niveauxDefaut as $n) {
            NiveauFormation::create(array_merge(
                $n, ['formation_id' => $formation->id]
            ));
        }

        return redirect()->route('admin.formations.show', $formation)
            ->with('success', 'Formation créée avec succès !');
    }

    // ===== DÉTAIL =====
    public function show(Formation $formation)
    {
        $formation->load([
            'niveaux.ressources',
            'inscriptions.user',
        ]);

        $formation->loadCount([
            'inscriptions',
            'inscriptions as inscrits_valides' => fn($q) => $q->where('statut', 'valide'),
            'ressources',
        ]);

        $enseignants         = User::role('enseignant')->get();
        $enseignantsFormation = User::role('enseignant')
            ->whereHas('ressources', fn($q) => $q->where('formation_id', $formation->id))
            ->get();

        return view('admin.formations.show', compact(
            'formation', 'enseignants', 'enseignantsFormation'
        ));
    }

    // ===== MODIFIER =====
    public function edit(Formation $formation)
    {
        return view('admin.formations.edit', compact('formation'));
    }

    // ===== METTRE À JOUR =====
    public function update(Request $request, Formation $formation)
    {
        $request->validate([
            'titre'       => 'required|string|max:200',
            'description' => 'required|string',
            'niveau'      => 'required|in:debutant,intermediaire,avance',
            'duree'       => 'nullable|string|max:50',
            'statut'      => 'required|in:publie,brouillon',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($formation->image) Storage::disk('public')->delete($formation->image);
            $formation->image = $request->file('image')->store('formations', 'public');
        }

        $formation->update([
            'titre'       => $request->titre,
            'description' => $request->description,
            'niveau'      => $request->niveau,
            'duree'       => $request->duree,
            'statut'      => $request->statut,
            'image'       => $formation->image,
        ]);

        return redirect()->route('admin.formations.show', $formation)
            ->with('success', 'Formation mise à jour !');
    }

    // ===== SUPPRIMER =====
    public function destroy(Formation $formation)
    {
        if ($formation->image) Storage::disk('public')->delete($formation->image);
        $formation->delete();
        return redirect()->route('admin.formations.index')
            ->with('success', 'Formation supprimée.');
    }

    // ===== AJOUTER NIVEAU =====
    public function storeNiveau(Request $request, Formation $formation)
    {
        $request->validate([
            'nom'         => 'required|string|max:100',
            'description' => 'nullable|string|max:300',
        ]);

        $ordre = NiveauFormation::where('formation_id', $formation->id)->max('ordre') + 1;

        NiveauFormation::create([
            'formation_id' => $formation->id,
            'nom'          => $request->nom,
            'description'  => $request->description,
            'ordre'        => $ordre,
        ]);

        return back()->with('success', 'Niveau ajouté !');
    }

    // ===== SUPPRIMER NIVEAU =====
    public function destroyNiveau(NiveauFormation $niveau)
    {
        $niveau->delete();
        return back()->with('success', 'Niveau supprimé.');
    }

    // ===== VALIDER INSCRIPTION =====
    public function validerInscription(InscriptionFormation $inscription)
    {
        $inscription->update(['statut' => 'valide']);

        Notification::create([
            'user_id' => $inscription->user_id,
            'titre'   => '✅ Inscription validée !',
            'message' => "Votre inscription à \"{$inscription->formation->titre}\" est validée. Accédez aux ressources !",
            'type'    => 'success',
        ]);

        return back()->with('success', 'Inscription validée !');
    }

    // ===== REJETER INSCRIPTION =====
    public function rejeterInscription(InscriptionFormation $inscription)
    {
        $inscription->update(['statut' => 'refuse']);

        Notification::create([
            'user_id' => $inscription->user_id,
            'titre'   => '❌ Inscription refusée',
            'message' => "Votre inscription à \"{$inscription->formation->titre}\" n'a pas été acceptée.",
            'type'    => 'error',
        ]);

        return back()->with('success', 'Inscription refusée.');
    }

    // ===== DÉSINSCRIRE UN CLIENT =====
    public function desinscrire(InscriptionFormation $inscription)
    {
        $nom = $inscription->user->nom_complet;
        $inscription->delete();
        return back()->with('success', "{$nom} désinscrit avec succès.");
    }
}
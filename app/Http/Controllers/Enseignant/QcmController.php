<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\Qcm;
use App\Models\QuestionQcm;
use App\Models\ReponseQcm;
use Illuminate\Http\Request;

class QcmController extends Controller
{
    // ===== LISTE DES QCMs =====
    public function index()
    {
        $enseignant = auth()->user();

        $qcms = Qcm::where('cree_par', $enseignant->id)
            ->with(['formation', 'niveau'])
            ->withCount('questions')
            ->latest()->get();

        $formations = Formation::whereHas('ressources', fn($q) =>
            $q->where('enseignant_id', $enseignant->id)
        )->get();

        return view('enseignant.qcms.index', compact('qcms', 'formations'));
    }

    // ===== CRÉER QCM =====
    public function create()
    {
        $enseignant = auth()->user();

        $formations = Formation::whereHas('ressources', fn($q) =>
            $q->where('enseignant_id', $enseignant->id)
        )->with('niveaux')->get();

        return view('enseignant.qcms.create', compact('formations'));
    }

    // ===== ENREGISTRER QCM =====
    public function store(Request $request)
    {
        $request->validate([
            'formation_id'        => 'required|exists:formations,id',
            'niveau_id'           => 'nullable|exists:niveaux_formation,id',
            'titre'               => 'required|string|max:200',
            'description'         => 'nullable|string|max:500',
            'duree_par_question'  => 'required|integer|min:30|max:600',
            'note_minimale'       => 'required|integer|min:1|max:20',
            'tentatives_max'      => 'required|integer|min:1|max:5',
        ]);

        $qcm = Qcm::create([
            'formation_id'        => $request->formation_id,
            'niveau_id'           => $request->niveau_id,
            'cree_par'            => auth()->id(),
            'titre'               => $request->titre,
            'description'         => $request->description,
            'duree_par_question'  => $request->duree_par_question,
            'note_minimale'       => $request->note_minimale,
            'tentatives_max'      => $request->tentatives_max,
            'actif'               => false,
        ]);

        return redirect()->route('enseignant.qcms.questions', $qcm)
            ->with('success', 'QCM créé ! Ajoutez maintenant les questions.');
    }

    // ===== GÉRER LES QUESTIONS =====
    public function questions(Qcm $qcm)
    {
        abort_if($qcm->cree_par !== auth()->id(), 403);

        $qcm->load(['questions.reponses', 'formation', 'niveau']);

        return view('enseignant.qcms.questions', compact('qcm'));
    }

    // ===== AJOUTER UNE QUESTION =====
    public function storeQuestion(Request $request, Qcm $qcm)
    {
        abort_if($qcm->cree_par !== auth()->id(), 403);

        $request->validate([
            'question'    => 'required|string|max:500',
            'points'      => 'required|integer|min:1|max:5',
            'reponses'    => 'required|array|min:2|max:4',
            'reponses.*'  => 'nullable|string|max:200',
            // ✅ correctes peut être absent si aucune case cochée
            'correctes'   => 'nullable|array',
            'correctes.*' => 'integer',
        ]);

        // Vérifier max 10 questions
        if ($qcm->questions()->count() >= 10) {
            return back()->with('error', '❌ Maximum 10 questions atteint.');
        }

        // ✅ Filtrer les réponses vides
        $reponses = array_filter(
            $request->reponses ?? [],
            fn($r) => !empty(trim($r))
        );

        // Vérifier qu'il y a au moins 2 réponses non vides
        if (count($reponses) < 2) {
            return back()
                ->with('error', '❌ Ajoutez au moins 2 propositions de réponses.')
                ->withInput();
        }

        // ✅ Vérifier qu'au moins une bonne réponse est cochée
        $correctes = $request->correctes ?? [];

        if (empty($correctes)) {
            return back()
                ->with('error', '❌ Cochez au moins une bonne réponse.')
                ->withInput();
        }

        // Vérifier que les indices des correctes existent dans les réponses
        $indicesReponses = array_keys(array_filter(
            $request->reponses ?? [],
            fn($r) => !empty(trim($r))
        ));

        $correctesValides = array_filter(
            $correctes,
            fn($c) => in_array((int)$c, $indicesReponses)
        );

        if (empty($correctesValides)) {
            return back()
                ->with('error', '❌ La bonne réponse cochée correspond à une proposition vide.')
                ->withInput();
        }

        // Créer la question
        $ordre = $qcm->questions()->max('ordre') + 1;

        $question = QuestionQcm::create([
            'qcm_id'   => $qcm->id,
            'question' => trim($request->question),
            'ordre'    => $ordre,
            'points'   => $request->points,
        ]);

        // Créer les réponses (seulement les non vides)
        foreach ($request->reponses as $index => $contenu) {
            if (empty(trim($contenu))) continue;

            ReponseQcm::create([
                'question_id' => $question->id,
                'contenu'     => trim($contenu),
                'est_correcte'=> in_array((int)$index, array_map('intval', $correctes)),
                'ordre'       => $index,
            ]);
        }

        return back()->with('success', '✅ Question ajoutée !');
    }

    // ===== SUPPRIMER UNE QUESTION =====
    public function destroyQuestion(Qcm $qcm, QuestionQcm $question)
    {
        abort_if($qcm->cree_par !== auth()->id(), 403);
        $question->delete();
        return back()->with('success', 'Question supprimée.');
    }

    // ===== ACTIVER/DÉSACTIVER QCM =====
    public function toggleActif(Qcm $qcm)
    {
        abort_if($qcm->cree_par !== auth()->id(), 403);

        // Vérifier au moins 5 questions pour activer
        if (!$qcm->actif && $qcm->questions()->count() < 5) {
            return back()->with('error', '❌ Le QCM doit avoir au moins 5 questions pour être activé.');
        }

        $qcm->update(['actif' => !$qcm->actif]);

        return back()->with('success',
            $qcm->actif ? '✅ QCM activé !' : '⏸️ QCM désactivé.'
        );
    }

    // ===== SUPPRIMER QCM =====
    public function destroy(Qcm $qcm)
    {
        abort_if($qcm->cree_par !== auth()->id(), 403);
        $qcm->delete();
        return redirect()->route('enseignant.qcms.index')
            ->with('success', 'QCM supprimé.');
    }

    // ===== RÉSULTATS DES APPRENANTS =====
    public function resultats(Qcm $qcm)
    {
        abort_if($qcm->cree_par !== auth()->id(), 403);

        $sessions = $qcm->sessions()
            ->with('user')
            ->latest()
            ->get();

        $stats = [
            'total'    => $sessions->count(),
            'reussis'  => $sessions->where('reussi', true)->count(),
            'moyenne'  => $sessions->avg('note'),
        ];

        return view('enseignant.qcms.resultats', compact('qcm', 'sessions', 'stats'));
    }
}
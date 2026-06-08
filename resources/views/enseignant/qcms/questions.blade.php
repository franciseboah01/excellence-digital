@extends('layouts.enseignant')
@section('title', 'Questions — ' . $qcm->titre)

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
    <div>
        <a href="{{ route('enseignant.qcms.index') }}"
            class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
            style="color: var(--edc-primary-light);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retour aux QCMs</span>
        </a>
        <h1 class="text-xl sm:text-2xl font-extrabold mt-1" style="color: var(--edc-text-primary);">📝 {{ $qcm->titre }}</h1>
        <p class="text-sm mt-0.5" style="color: var(--edc-text-secondary);">
            🎓 {{ $qcm->formation->titre }}
            @if($qcm->niveau) — {{ $qcm->niveau->nom }} @endif
            • ⏱ {{ $qcm->duree_par_question }}s/question
            • 🎯 {{ $qcm->note_minimale }}/20
        </p>
    </div>
    <div class="flex-shrink-0">
        <form method="POST" action="{{ route('enseignant.qcms.toggle', $qcm) }}">
            @csrf
            <button type="submit"
                class="btn-sm rounded-lg text-sm font-bold transition"
                style="{{ $qcm->actif
                    ? 'background-color: rgba(245,158,11,0.12); color: #FBBF24;'
                    : 'background-color: rgba(16,185,129,0.12); color: #34D399;' }}">
                {{ $qcm->actif ? '⏸️ Désactiver' : '▶️ Activer le QCM' }}
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- FORMULAIRE AJOUT QUESTION --}}
    @if($qcm->questions->count() < 10)
    <div class="edc-card p-6">
        <h2 class="text-lg font-bold mb-1" style="color: var(--edc-text-primary);">➕ Ajouter une question</h2>
        <p class="text-xs mb-4" style="color: var(--edc-text-muted);">{{ $qcm->questions->count() }}/10 questions ajoutées</p>

        <form method="POST" action="{{ route('enseignant.qcms.questions.store', $qcm) }}" id="formQuestion" class="space-y-4">
            @csrf

            <div>
                <label class="edc-label">Question *</label>
                <textarea name="question" rows="3" required class="edc-input"
                    placeholder="Écrivez la question ici..."></textarea>
            </div>

            <div>
                <label class="edc-label">Points *</label>
                <select name="points" class="edc-select">
                    <option value="2" selected>2 points</option>
                    <option value="1">1 point</option>
                    <option value="3">3 points</option>
                </select>
            </div>

            <div>
                <label class="edc-label">
                    Propositions de réponses *
                    <span style="color: var(--edc-text-muted); font-weight: normal; font-size: 0.7rem;">
                        (2 à 4 propositions, cochez la ou les bonnes)
                    </span>
                </label>

                @for($i = 0; $i < 4; $i++)
                <div class="flex items-center space-x-2 mb-2">
                    <input type="checkbox" name="correctes[]" value="{{ $i }}"
                        class="w-4 h-4 rounded flex-shrink-0" style="accent-color: #10B981;"
                        title="Cocher si bonne réponse">
                    <input type="text" name="reponses[]"
                        class="edc-input flex-1" style="padding: 10px 14px;"
                        placeholder="Proposition {{ $i + 1 }}{{ $i < 2 ? ' *' : ' (optionnel)' }}"
                        {{ $i < 2 ? 'required' : '' }}>
                </div>
                @endfor

                <p class="text-xs mt-1" style="color: var(--edc-text-muted);">✅ = bonne réponse | Cases vides = ignorées</p>
            </div>

            <button type="submit" class="btn-primary w-full">
                ➕ Ajouter la question
            </button>
        </form>
    </div>
    @else
    <div class="edc-card p-6 text-center" style="background-color: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.25);">
        <p class="text-4xl mb-3">✅</p>
        <p class="font-bold" style="color: var(--edc-secondary);">10 questions atteintes !</p>
        <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">Le QCM est complet.</p>
        @if(!$qcm->actif)
        <form method="POST" action="{{ route('enseignant.qcms.toggle', $qcm) }}" class="mt-4">
            @csrf
            <button type="submit" class="btn-success">
                ▶️ Activer le QCM maintenant
            </button>
        </form>
        @endif
    </div>
    @endif

    {{-- LISTE DES QUESTIONS --}}
    <div>
        <h2 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">
            📋 Questions ({{ $qcm->questions->count() }}/10)
        </h2>

        @forelse($qcm->questions as $index => $question)
        <div class="edc-card p-4 mb-3" style="border-left: 4px solid var(--edc-primary);">
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                        style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                        {{ $index + 1 }}
                    </span>
                    <p class="font-medium text-sm" style="color: var(--edc-text-primary);">{{ $question->question }}</p>
                </div>
                <div class="flex items-center space-x-2 ml-3 flex-shrink-0">
                    <span class="badge badge-blue">{{ $question->points }} pt{{ $question->points > 1 ? 's' : '' }}</span>
                    <form method="POST" action="{{ route('enseignant.qcms.questions.destroy', [$qcm, $question]) }}"
                        onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs hover:underline" style="color: var(--edc-danger);">🗑️</button>
                    </form>
                </div>
            </div>

            <div class="space-y-1 ml-9">
                @foreach($question->reponses as $reponse)
                <div class="flex items-center space-x-2 text-xs">
                    <span style="color: {{ $reponse->est_correcte ? 'var(--edc-secondary)' : 'var(--edc-text-muted)' }};">
                        {{ $reponse->est_correcte ? '✅' : '○' }}
                    </span>
                    <span style="color: {{ $reponse->est_correcte ? 'var(--edc-text-primary)' : 'var(--edc-text-muted)' }}; {{ $reponse->est_correcte ? 'font-weight: 600;' : '' }}">
                        {{ $reponse->contenu }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="rounded-xl p-8 text-center" style="background-color: var(--edc-bg-base); color: var(--edc-text-muted);">
            <p class="text-3xl mb-2">📝</p>
            <p class="text-sm">Aucune question ajoutée.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
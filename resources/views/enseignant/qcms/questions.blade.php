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
            • 🎯 {{ $qcm->note_minimale }}/{{ $certificat->session->qcm->bareme ?? 20 }}
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
        <p class="text-xs mb-4" style="color: var(--edc-text-muted);">{{ $qcm->questions->count() }}/10 questions</p>

        @if(session('error'))
        <div class="alert alert-error mb-4">
            <span>❌</span><span>{{ session('error') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('enseignant.qcms.questions.store', $qcm) }}" class="space-y-4">
            @csrf

            {{-- Question --}}
            <div>
                <label class="edc-label">Question *</label>
                <textarea name="question" rows="3" required class="edc-input"
                    placeholder="Écrivez la question ici...">{{ old('question') }}</textarea>
                @error('question') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Points --}}
            <div>
                <label class="edc-label">Points *</label>
                <select name="points" class="edc-select">
                    <option value="2" {{ old('points', 2) == 2 ? 'selected' : '' }}>2 points</option>
                    <option value="1" {{ old('points') == 1 ? 'selected' : '' }}>1 point</option>
                    <option value="3" {{ old('points') == 3 ? 'selected' : '' }}>3 points</option>
                </select>
            </div>

            {{-- Propositions --}}
            <div>
                <label class="edc-label">
                    Propositions *
                    <span style="color: var(--edc-text-muted); font-weight: normal; font-size: 0.7rem;">
                        (2 à 4 • ✅ = bonne réponse)
                    </span>
                </label>

                <div class="space-y-2">
                    @for($i = 0; $i < 4; $i++)
                    <div class="flex items-center space-x-3 p-3 rounded-xl transition"
                        id="proposition-{{ $i }}"
                        style="border: 1px solid var(--edc-border);">

                        <input type="checkbox"
                            name="correctes[]"
                            value="{{ $i }}"
                            id="correct_{{ $i }}"
                            {{ in_array($i, old('correctes', [])) ? 'checked' : '' }}
                            class="w-5 h-5 rounded flex-shrink-0 cursor-pointer"
                            style="accent-color: #10B981;"
                            title="Cocher si bonne réponse"
                            onchange="togglePropositionStyle({{ $i }}, this.checked)">

                        <input type="text"
                            name="reponses[]"
                            value="{{ old('reponses.'.$i) }}"
                            class="flex-1 text-sm bg-transparent"
                            style="border: none; outline: none; color: var(--edc-text-primary);"
                            placeholder="{{ $i < 2 ? 'Proposition '.($i+1).' *' : 'Proposition '.($i+1).' (optionnel)' }}"
                            id="reponse_{{ $i }}">

                        <span id="label_{{ $i }}"
                            class="text-xs font-medium hidden"
                            style="color: var(--edc-secondary);">
                            ✅ Bonne
                        </span>
                    </div>
                    @endfor
                </div>

                <p class="text-xs mt-2" style="color: var(--edc-text-muted);">
                    💡 Cochez la case ✅ à gauche pour marquer la/les bonne(s) réponse(s)
                </p>
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
                <div class="flex items-center space-x-3 ml-3 flex-shrink-0">
                    <span class="badge badge-blue">{{ $question->points }} pt{{ $question->points > 1 ? 's' : '' }}</span>
                    <button type="button" onclick="ouvrirModaleModification({{ $question->id }})"
                        class="text-xs hover:underline" style="color: var(--edc-primary-light);" title="Modifier">
                        ✏️
                    </button>
                    <form method="POST" action="{{ route('enseignant.qcms.questions.destroy', [$qcm, $question]) }}"
                        onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs hover:underline" style="color: var(--edc-danger);" title="Supprimer">🗑️</button>
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

{{-- MODALE MODIFICATION QUESTION --}}
<div id="modaleModification" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background-color: rgba(0,0,0,0.7);">
    <div class="edc-card w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5">
            <h3 class="font-bold text-lg" style="color: var(--edc-text-primary);">✏️ Modifier la question</h3>
            <button onclick="fermerModaleModification()" class="text-xl transition"
                style="color: var(--edc-text-muted);"
                onmouseover="this.style.color='#EF4444'"
                onmouseout="this.style.color='#64748B'">✕</button>
        </div>
        <form id="formModification" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="edc-label">Question *</label>
                <textarea name="question" id="edit_question" rows="3" required class="edc-input"></textarea>
            </div>
            <div>
                <label class="edc-label">Points *</label>
                <select name="points" id="edit_points" class="edc-select">
                    <option value="1">1 point</option>
                    <option value="2">2 points</option>
                    <option value="3">3 points</option>
                </select>
            </div>
            <div>
                <label class="edc-label">Propositions</label>
                <div class="space-y-2" id="edit_propositions"></div>
            </div>
            <button type="submit" class="btn-primary w-full">
                💾 Enregistrer
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePropositionStyle(index, checked) {
    const div   = document.getElementById('proposition-' + index);
    const label = document.getElementById('label-' + index);

    if (checked) {
        div.style.borderColor = 'rgba(16,185,129,0.4)';
        div.style.backgroundColor = 'rgba(16,185,129,0.06)';
        if (label) label.classList.remove('hidden');
    } else {
        div.style.borderColor = 'var(--edc-border)';
        div.style.backgroundColor = 'transparent';
        if (label) label.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="correctes[]"]').forEach(cb => {
        if (cb.checked) {
            togglePropositionStyle(parseInt(cb.value), true);
        }
    });
});

// Modale modification question
function ouvrirModaleModification(questionId) {
    fetch(`/enseignant/qcms/question/${questionId}/data`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('edit_question').value = data.question;
            document.getElementById('edit_points').value = data.points;

            let html = '';
            data.reponses.forEach((r, i) => {
                html += `
                <div class="flex items-center space-x-3 p-3 rounded-xl" style="border: 1px solid var(--edc-border);">
                    <input type="checkbox" name="correctes[]" value="${i}" ${r.est_correcte ? 'checked' : ''}
                        class="w-5 h-5 rounded flex-shrink-0 cursor-pointer" style="accent-color: #10B981;">
                    <input type="text" name="reponses[]" value="${r.contenu.replace(/"/g, '&quot;')}"
                        class="flex-1 text-sm bg-transparent" style="border: none; outline: none; color: var(--edc-text-primary);">
                </div>`;
            });
            document.getElementById('edit_propositions').innerHTML = html;
            document.getElementById('formModification').action = `/enseignant/qcms/question/${questionId}`;
            document.getElementById('modaleModification').classList.remove('hidden');
        });
}

function fermerModaleModification() {
    document.getElementById('modaleModification').classList.add('hidden');
}
</script>
@endpush
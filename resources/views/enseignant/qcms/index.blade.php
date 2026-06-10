@extends('layouts.enseignant')
@section('title', 'Mes QCMs')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">📝 Mes QCMs</h1>
    <a href="{{ route('enseignant.qcms.create') }}" class="btn-primary btn-sm">
        ➕ Créer un QCM
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($qcms as $qcm)
    <div class="edc-card overflow-hidden">
        <div class="p-5">
            <div class="flex justify-between items-start mb-3">
                <span class="text-xs px-2 py-1 rounded-full font-medium"
                    style="{{ $qcm->actif
                        ? 'background-color: rgba(16,185,129,0.12); color: #34D399;'
                        : 'background-color: rgba(148,163,184,0.10); color: #94A3B8;' }}">
                    {{ $qcm->actif ? '✅ Actif' : '⏸️ Inactif' }}
                </span>
                <span class="text-xs" style="color: var(--edc-text-muted);">
                    {{ $qcm->questions_count }}/10 questions
                </span>
            </div>

            <h3 class="font-bold mb-1" style="color: var(--edc-text-primary);">{{ $qcm->titre }}</h3>
            <p class="text-xs" style="color: var(--edc-primary-light);">🎓 {{ $qcm->formation->titre }}</p>
            @if($qcm->niveau)
            <p class="text-xs mt-0.5" style="color: var(--edc-text-muted);">📂 {{ $qcm->niveau->nom }}</p>
            @endif

            <div class="flex items-center space-x-4 mt-3 text-xs" style="color: var(--edc-text-muted);">
                <span>⏱ {{ $qcm->duree_par_question }}s/q</span>
                <span>🎯 {{ $qcm->note_minimale }}{{ $qcm->bareme }}</span>
                <span>🔄 {{ $qcm->tentatives_max }} essais</span>
            </div>
        </div>

        <div class="px-5 py-3 flex justify-between items-center" style="border-top: 1px solid var(--edc-border); background-color: var(--edc-bg-base);">
            <div class="flex space-x-3">
                <a href="{{ route('enseignant.qcms.questions', $qcm) }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">
                    ✏️ Questions
                </a>
                <a href="{{ route('enseignant.qcms.resultats', $qcm) }}" class="text-xs font-medium hover:underline" style="color: #A78BFA;">
                    📊 Résultats
                </a>
            </div>
            <div class="flex space-x-2">
                <form method="POST" action="{{ route('enseignant.qcms.toggle', $qcm) }}">
                    @csrf
                    <button type="submit" class="text-xs font-medium hover:underline"
                        style="color: {{ $qcm->actif ? 'var(--edc-accent-gold)' : 'var(--edc-secondary)' }};">
                        {{ $qcm->actif ? '⏸️' : '▶️' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('enseignant.qcms.destroy', $qcm) }}"
                    onsubmit="return confirm('Supprimer ce QCM ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-medium hover:underline" style="color: var(--edc-danger);">🗑️</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 edc-card text-center py-16" style="color: var(--edc-text-muted);">
        <p class="text-5xl mb-4">📝</p>
        <p>Aucun QCM créé.</p>
        <a href="{{ route('enseignant.qcms.create') }}" class="btn-primary btn-sm mt-4 inline-block">
            Créer mon premier QCM
        </a>
    </div>
    @endforelse
</div>
@endsection
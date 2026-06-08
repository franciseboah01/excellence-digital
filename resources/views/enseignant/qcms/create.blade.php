@extends('layouts.enseignant')
@section('title', 'Créer un QCM')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('enseignant.qcms.index') }}"
        class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
        style="color: var(--edc-primary-light);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Retour</span>
    </a>
    <h1 class="text-xl sm:text-2xl font-extrabold mt-2 mb-6" style="color: var(--edc-text-primary);">➕ Créer un QCM</h1>

    <div class="edc-card p-6 sm:p-8">
        <form method="POST" action="{{ route('enseignant.qcms.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="edc-label">Formation *</label>
                <select name="formation_id" id="formation_id" required class="edc-select">
                    <option value="">-- Choisir --</option>
                    @foreach($formations as $formation)
                    <option value="{{ $formation->id }}">{{ $formation->titre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="edc-label">Niveau (optionnel)</label>
                <select name="niveau_id" id="niveau_id" class="edc-select">
                    <option value="">-- Général --</option>
                </select>
            </div>

            <div>
                <label class="edc-label">Titre du QCM *</label>
                <input type="text" name="titre" required class="edc-input"
                    placeholder="Ex : QCM Excel — Niveau Débutant">
            </div>

            <div>
                <label class="edc-label">Description</label>
                <textarea name="description" rows="2" class="edc-input"
                    placeholder="Description du QCM..."></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="edc-label">Durée/question (sec) *</label>
                    <input type="number" name="duree_par_question" value="120"
                        min="30" max="600" required class="edc-input">
                    <p class="text-xs mt-1" style="color: var(--edc-text-muted);">30s à 10min</p>
                </div>
                <div>
                    <label class="edc-label">Note minimale /20 *</label>
                    <input type="number" name="note_minimale" value="14"
                        min="1" max="20" required class="edc-input">
                    <p class="text-xs mt-1" style="color: var(--edc-text-muted);">Pour obtenir le cert.</p>
                </div>
                <div>
                    <label class="edc-label">Tentatives max *</label>
                    <input type="number" name="tentatives_max" value="3"
                        min="1" max="5" required class="edc-input">
                </div>
            </div>

            <button type="submit" class="btn-primary w-full">
                ➕ Créer et ajouter les questions →
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('formation_id').addEventListener('change', function() {
        const formationId = this.value;
        const niveauSelect = document.getElementById('niveau_id');
        niveauSelect.innerHTML = '<option value="">-- Général --</option>';
        if (!formationId) return;
        fetch(`/enseignant/formations/${formationId}/niveaux`)
            .then(r => r.json())
            .then(niveaux => niveaux.forEach(n => {
                const opt = document.createElement('option');
                opt.value = n.id;
                opt.textContent = `${n.nom}`;
                niveauSelect.appendChild(opt);
            }));
    });
</script>
@endpush
@endsection
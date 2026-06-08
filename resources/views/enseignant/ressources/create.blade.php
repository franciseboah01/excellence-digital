@extends('layouts.enseignant')
@section('title', 'Ajouter une ressource')

@section('content')
<div class="mb-6">
    <a href="{{ route('enseignant.ressources.index') }}"
        class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
        style="color: var(--edc-primary-light);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Retour</span>
    </a>
    <h1 class="text-xl sm:text-2xl font-extrabold mt-2" style="color: var(--edc-text-primary);">➕ Ajouter une ressource</h1>
</div>

<div class="edc-card p-6 sm:p-8 max-w-2xl">
    <form method="POST" action="{{ route('enseignant.ressources.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Formation --}}
        <div>
            <label class="edc-label">Formation *</label>
            <select name="formation_id" id="formation_id" required class="edc-select">
                <option value="">-- Choisir une formation --</option>
                @foreach($formations as $formation)
                <option value="{{ $formation->id }}"
                    {{ (old('formation_id') == $formation->id || request('formation') == $formation->id) ? 'selected' : '' }}>
                    {{ $formation->titre }}
                </option>
                @endforeach
            </select>
            @error('formation_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Niveau --}}
        <div>
            <label class="edc-label">Niveau (optionnel)</label>
            <select name="niveau_id" id="niveau_id" class="edc-select">
                <option value="">-- Général (tous niveaux) --</option>
            </select>
        </div>

        {{-- Type --}}
        <div>
            <label class="edc-label">Type de ressource *</label>
            <select name="type" id="type_ressource" required class="edc-select">
                <option value="">-- Choisir un type --</option>
                <option value="pdf"      {{ old('type') == 'pdf' ? 'selected' : '' }}>📄 PDF</option>
                <option value="ebook"    {{ old('type') == 'ebook' ? 'selected' : '' }}>📖 Ebook</option>
                <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>📝 Document Word</option>
                <option value="lien"     {{ old('type') == 'lien' ? 'selected' : '' }}>🔗 Lien externe</option>
                <option value="video"    {{ old('type') == 'video' ? 'selected' : '' }}>🎬 Vidéo YouTube/Drive</option>
            </select>
            @error('type') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Titre --}}
        <div>
            <label class="edc-label">Titre *</label>
            <input type="text" name="titre" value="{{ old('titre') }}" required class="edc-input"
                placeholder="Ex : Cours Excel Chapitre 1">
            @error('titre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="edc-label">Description (optionnel)</label>
            <textarea name="description" rows="3" class="edc-input"
                placeholder="Décrivez le contenu de cette ressource...">{{ old('description') }}</textarea>
        </div>

        {{-- Fichier --}}
        <div id="champ_fichier" class="hidden">
            <label class="edc-label">Fichier (PDF, DOC, EPUB — max 20MB) *</label>
            <input type="file" name="fichier" accept=".pdf,.doc,.docx,.epub" class="edc-input">
            @error('fichier') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Lien --}}
        <div id="champ_lien" class="hidden">
            <label class="edc-label">URL du lien / vidéo *</label>
            <input type="url" name="lien_url" value="{{ old('lien_url') }}" class="edc-input"
                placeholder="https://youtube.com/...">
            @error('lien_url') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary w-full">
            📤 Publier la ressource
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('type_ressource').addEventListener('change', function () {
        const type = this.value;
        const champFichier = document.getElementById('champ_fichier');
        const champLien    = document.getElementById('champ_lien');
        champFichier.classList.add('hidden');
        champLien.classList.add('hidden');
        if (['pdf', 'ebook', 'document'].includes(type)) champFichier.classList.remove('hidden');
        else if (['lien', 'video'].includes(type)) champLien.classList.remove('hidden');
    });

    document.getElementById('formation_id').addEventListener('change', function () {
        const formationId = this.value;
        const niveauSelect = document.getElementById('niveau_id');
        niveauSelect.innerHTML = '<option value="">-- Général (tous niveaux) --</option>';
        if (!formationId) return;
        fetch(`/enseignant/formations/${formationId}/niveaux`)
            .then(res => res.json())
            .then(niveaux => {
                niveaux.forEach(n => {
                    const opt = document.createElement('option');
                    opt.value = n.id;
                    opt.textContent = `Niveau ${n.ordre} — ${n.nom}`;
                    niveauSelect.appendChild(opt);
                });
            });
    });

    window.addEventListener('DOMContentLoaded', function () {
        const sel = document.getElementById('formation_id');
        if (sel.value) sel.dispatchEvent(new Event('change'));
    });
</script>
@endpush
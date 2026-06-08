@extends('layouts.enseignant')
@section('title', 'Modifier la ressource')

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
    <h1 class="text-xl sm:text-2xl font-extrabold mt-2" style="color: var(--edc-text-primary);">✏️ Modifier la ressource</h1>
</div>

<div class="edc-card p-6 sm:p-8 max-w-2xl">
    <form method="POST" action="{{ route('enseignant.ressources.update', $ressource) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="edc-label">Formation *</label>
            <select name="formation_id" id="formation_id" required class="edc-select">
                @foreach($formations as $formation)
                <option value="{{ $formation->id }}" {{ $ressource->formation_id == $formation->id ? 'selected' : '' }}>
                    {{ $formation->titre }}
                </option>
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
            <label class="edc-label">Type *</label>
            <select name="type" id="type_ressource" required class="edc-select">
                <option value="pdf"      {{ $ressource->type == 'pdf' ? 'selected' : '' }}>📄 PDF</option>
                <option value="ebook"    {{ $ressource->type == 'ebook' ? 'selected' : '' }}>📖 Ebook</option>
                <option value="document" {{ $ressource->type == 'document' ? 'selected' : '' }}>📝 Document</option>
                <option value="lien"     {{ $ressource->type == 'lien' ? 'selected' : '' }}>🔗 Lien</option>
                <option value="video"    {{ $ressource->type == 'video' ? 'selected' : '' }}>🎬 Vidéo</option>
            </select>
        </div>

        <div>
            <label class="edc-label">Titre *</label>
            <input type="text" name="titre" value="{{ old('titre', $ressource->titre) }}" required class="edc-input">
        </div>

        <div>
            <label class="edc-label">Description</label>
            <textarea name="description" rows="3" class="edc-input">{{ old('description', $ressource->description) }}</textarea>
        </div>

        <div id="champ_fichier" class="hidden">
            <label class="edc-label">Nouveau fichier (laisser vide pour conserver l'actuel)</label>
            @if($ressource->fichier_path)
            <p class="text-xs mb-2" style="color: var(--edc-secondary);">✅ Fichier actuel : {{ basename($ressource->fichier_path) }}</p>
            @endif
            <input type="file" name="fichier" accept=".pdf,.doc,.docx,.epub" class="edc-input">
        </div>

        <div id="champ_lien" class="hidden">
            <label class="edc-label">URL</label>
            <input type="url" name="lien_url" value="{{ old('lien_url', $ressource->lien_url) }}" class="edc-input">
        </div>

        <button type="submit" class="btn-primary w-full">
            💾 Enregistrer les modifications
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const typeActuel = "{{ $ressource->type }}";
    const niveauActuel = {{ $ressource->niveau_id ?? 'null' }};

    function toggleChamps(type) {
        const champFichier = document.getElementById('champ_fichier');
        const champLien    = document.getElementById('champ_lien');
        champFichier.classList.add('hidden');
        champLien.classList.add('hidden');
        if (['pdf', 'ebook', 'document'].includes(type)) champFichier.classList.remove('hidden');
        else if (['lien', 'video'].includes(type)) champLien.classList.remove('hidden');
    }

    document.getElementById('type_ressource').addEventListener('change', function () {
        toggleChamps(this.value);
    });

    document.getElementById('formation_id').addEventListener('change', function () {
        const formationId = this.value;
        const niveauSelect = document.getElementById('niveau_id');
        niveauSelect.innerHTML = '<option value="">-- Général --</option>';
        if (!formationId) return;
        fetch(`/enseignant/formations/${formationId}/niveaux`)
            .then(res => res.json())
            .then(niveaux => {
                niveaux.forEach(n => {
                    const opt = document.createElement('option');
                    opt.value = n.id;
                    opt.textContent = `Niveau ${n.ordre} — ${n.nom}`;
                    if (n.id === niveauActuel) opt.selected = true;
                    niveauSelect.appendChild(opt);
                });
            });
    });

    window.addEventListener('DOMContentLoaded', function () {
        toggleChamps(typeActuel);
        document.getElementById('formation_id').dispatchEvent(new Event('change'));
    });
</script>
@endpush
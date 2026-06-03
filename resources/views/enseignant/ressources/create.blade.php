@extends('layouts.enseignant')
@section('title', 'Ajouter une ressource')

@section('content')
<div class="mb-6">
    <a href="{{ route('enseignant.ressources.index') }}"
        class="text-blue-600 hover:underline text-sm">← Retour</a>
    <h1 class="text-2xl font-bold text-blue-900 mt-2">➕ Ajouter une ressource</h1>
</div>

<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('enseignant.ressources.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Formation --}}
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Formation *</label>
            <select name="formation_id" id="formation_id" required
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Choisir une formation --</option>
                @foreach($formations as $formation)
                <option value="{{ $formation->id }}"
                    {{ (old('formation_id') == $formation->id || request('formation') == $formation->id) ? 'selected' : '' }}>
                    {{ $formation->titre }}
                </option>
                @endforeach
            </select>
            @error('formation_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Niveau (chargé dynamiquement) --}}
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Niveau (optionnel)</label>
            <select name="niveau_id" id="niveau_id"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Général (tous niveaux) --</option>
            </select>
        </div>

        {{-- Type --}}
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Type de ressource *</label>
            <select name="type" id="type_ressource" required
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Choisir un type --</option>
                <option value="pdf"      {{ old('type') == 'pdf' ? 'selected' : '' }}>📄 PDF</option>
                <option value="ebook"    {{ old('type') == 'ebook' ? 'selected' : '' }}>📖 Ebook</option>
                <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>📝 Document Word</option>
                <option value="lien"     {{ old('type') == 'lien' ? 'selected' : '' }}>🔗 Lien externe</option>
                <option value="video"    {{ old('type') == 'video' ? 'selected' : '' }}>🎬 Vidéo YouTube/Drive</option>
            </select>
            @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Titre --}}
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Titre *</label>
            <input type="text" name="titre" value="{{ old('titre') }}" required
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Ex : Cours Excel Chapitre 1">
            @error('titre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Description --}}
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description (optionnel)</label>
            <textarea name="description" rows="3"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Décrivez le contenu de cette ressource...">{{ old('description') }}</textarea>
        </div>

        {{-- Fichier (PDF/Ebook/Document) --}}
        <div id="champ_fichier" class="mb-5 hidden">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Fichier (PDF, DOC, EPUB — max 20MB) *
            </label>
            <input type="file" name="fichier" accept=".pdf,.doc,.docx,.epub"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('fichier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Lien URL (lien/vidéo) --}}
        <div id="champ_lien" class="mb-5 hidden">
            <label class="block text-sm font-semibold text-gray-700 mb-1">URL du lien / vidéo *</label>
            <input type="url" name="lien_url" value="{{ old('lien_url') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="https://youtube.com/...">
            @error('lien_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
            class="w-full bg-blue-800 text-white py-4 rounded-xl font-bold hover:bg-blue-900 transition">
            📤 Publier la ressource
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Afficher champ fichier ou lien selon le type
    document.getElementById('type_ressource').addEventListener('change', function () {
        const type = this.value;
        const champFichier = document.getElementById('champ_fichier');
        const champLien    = document.getElementById('champ_lien');

        champFichier.classList.add('hidden');
        champLien.classList.add('hidden');

        if (['pdf', 'ebook', 'document'].includes(type)) {
            champFichier.classList.remove('hidden');
        } else if (['lien', 'video'].includes(type)) {
            champLien.classList.remove('hidden');
        }
    });

    // Charger les niveaux selon la formation choisie
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

    // Déclencher si une formation est pré-sélectionnée
    window.addEventListener('DOMContentLoaded', function () {
        const sel = document.getElementById('formation_id');
        if (sel.value) sel.dispatchEvent(new Event('change'));
    });
</script>
@endpush
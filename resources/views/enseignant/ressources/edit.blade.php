@extends('layouts.enseignant')
@section('title', 'Modifier la ressource')

@section('content')
<div class="mb-6">
    <a href="{{ route('enseignant.ressources.index') }}"
        class="text-blue-600 hover:underline text-sm">← Retour</a>
    <h1 class="text-2xl font-bold text-blue-900 mt-2">✏️ Modifier la ressource</h1>
</div>

<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('enseignant.ressources.update', $ressource) }}"
        enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Formation *</label>
            <select name="formation_id" id="formation_id" required
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach($formations as $formation)
                <option value="{{ $formation->id }}"
                    {{ $ressource->formation_id == $formation->id ? 'selected' : '' }}>
                    {{ $formation->titre }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Niveau (optionnel)</label>
            <select name="niveau_id" id="niveau_id"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Général --</option>
            </select>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Type *</label>
            <select name="type" id="type_ressource" required
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="pdf"      {{ $ressource->type == 'pdf' ? 'selected' : '' }}>📄 PDF</option>
                <option value="ebook"    {{ $ressource->type == 'ebook' ? 'selected' : '' }}>📖 Ebook</option>
                <option value="document" {{ $ressource->type == 'document' ? 'selected' : '' }}>📝 Document</option>
                <option value="lien"     {{ $ressource->type == 'lien' ? 'selected' : '' }}>🔗 Lien</option>
                <option value="video"    {{ $ressource->type == 'video' ? 'selected' : '' }}>🎬 Vidéo</option>
            </select>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Titre *</label>
            <input type="text" name="titre" value="{{ old('titre', $ressource->titre) }}" required
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $ressource->description) }}</textarea>
        </div>

        <div id="champ_fichier" class="mb-5 hidden">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Nouveau fichier (laisser vide pour conserver l'actuel)
            </label>
            @if($ressource->fichier_path)
            <p class="text-xs text-green-600 mb-2">✅ Fichier actuel : {{ basename($ressource->fichier_path) }}</p>
            @endif
            <input type="file" name="fichier" accept=".pdf,.doc,.docx,.epub"
                class="w-full border border-gray-300 rounded-lg px-4 py-3">
        </div>

        <div id="champ_lien" class="mb-5 hidden">
            <label class="block text-sm font-semibold text-gray-700 mb-1">URL</label>
            <input type="url" name="lien_url" value="{{ old('lien_url', $ressource->lien_url) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <button type="submit"
            class="w-full bg-blue-800 text-white py-4 rounded-xl font-bold hover:bg-blue-900 transition">
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
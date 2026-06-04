{{-- Image --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Image de couverture</label>
    @if(isset($formation) && $formation->image)
    <div class="mb-2">
        <img src="{{ asset('storage/' . $formation->image) }}"
            class="w-32 h-20 object-cover rounded-lg border">
        <p class="text-xs text-gray-400 mt-1">Image actuelle</p>
    </div>
    @endif
    <input type="file" name="image" accept="image/*"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP — max 2MB</p>
    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Titre --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Titre *</label>
    <input type="text" name="titre"
        value="{{ old('titre', $formation->titre ?? '') }}" required
        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="Ex : Formation Excel Avancé">
    @error('titre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Description --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Description *</label>
    <textarea name="description" rows="4" required
        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="Décrivez cette formation...">{{ old('description', $formation->description ?? '') }}</textarea>
    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Niveau + Durée --}}
<div class="grid grid-cols-2 gap-4 mb-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Niveau *</label>
        <select name="niveau" required
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="debutant"
                {{ old('niveau', $formation->niveau ?? '') == 'debutant' ? 'selected' : '' }}>
                🟢 Débutant
            </option>
            <option value="intermediaire"
                {{ old('niveau', $formation->niveau ?? '') == 'intermediaire' ? 'selected' : '' }}>
                🟡 Intermédiaire
            </option>
            <option value="avance"
                {{ old('niveau', $formation->niveau ?? '') == 'avance' ? 'selected' : '' }}>
                🔴 Avancé
            </option>
        </select>
        @error('niveau') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Durée</label>
        <input type="text" name="duree"
            value="{{ old('duree', $formation->duree ?? '') }}"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Ex : 3 semaines">
    </div>
</div>

{{-- Statut --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Statut *</label>
    <select name="statut" required
        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="brouillon"
            {{ old('statut', $formation->statut ?? 'brouillon') == 'brouillon' ? 'selected' : '' }}>
            📝 Brouillon (non visible)
        </option>
        <option value="publie"
            {{ old('statut', $formation->statut ?? '') == 'publie' ? 'selected' : '' }}>
            ✅ Publié (visible sur le site)
        </option>
    </select>
    @error('statut') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>
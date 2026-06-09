{{-- Image --}}
<div class="mb-5">
    <label class="edc-label">Image de couverture</label>
    @if(isset($formation) && $formation->image)
    <div class="mb-2">
        <img src="{{ asset('storage/' . $formation->image) }}"
            class="w-32 h-20 object-cover rounded-lg border" style="border-color: var(--edc-border);">
        <p class="text-xs mt-1" style="color: var(--edc-text-muted);">Image actuelle</p>
    </div>
    @endif
    <input type="file" name="image" accept="image/*" class="edc-input">
    <p class="text-xs mt-1" style="color: var(--edc-text-muted);">JPG, PNG, WebP — max 2MB</p>
    @error('image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Titre --}}
<div class="mb-5">
    <label class="edc-label">Titre *</label>
    <input type="text" name="titre"
        value="{{ old('titre', $formation->titre ?? '') }}" required
        class="edc-input" placeholder="Ex : Formation Excel Avancé">
    @error('titre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Description --}}
<div class="mb-5">
    <label class="edc-label">Description *</label>
    <textarea name="description" rows="4" required class="edc-input"
        placeholder="Décrivez cette formation...">{{ old('description', $formation->description ?? '') }}</textarea>
    @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Niveau + Durée --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
    <div>
        <label class="edc-label">Niveau *</label>
        <select name="niveau" required class="edc-select">
            <option value="debutant" {{ old('niveau', $formation->niveau ?? '') == 'debutant' ? 'selected' : '' }}>🟢 Débutant</option>
            <option value="intermediaire" {{ old('niveau', $formation->niveau ?? '') == 'intermediaire' ? 'selected' : '' }}>🟡 Intermédiaire</option>
            <option value="avance" {{ old('niveau', $formation->niveau ?? '') == 'avance' ? 'selected' : '' }}>🔴 Avancé</option>
        </select>
        @error('niveau') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="edc-label">Durée</label>
        <input type="text" name="duree" value="{{ old('duree', $formation->duree ?? '') }}"
            class="edc-input" placeholder="Ex : 3 semaines">
    </div>
</div>

{{-- Statut --}}
<div class="mb-5">
    <label class="edc-label">Statut *</label>
    <select name="statut" required class="edc-select">
        <option value="brouillon" {{ old('statut', $formation->statut ?? 'brouillon') == 'brouillon' ? 'selected' : '' }}>📝 Brouillon (non visible)</option>
        <option value="publie" {{ old('statut', $formation->statut ?? '') == 'publie' ? 'selected' : '' }}>✅ Publié (visible sur le site)</option>
    </select>
    @error('statut') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
</div>
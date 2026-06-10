{{-- Icône --}}
<div class="mb-5">
    <label class="edc-label">Icône (emoji)</label>
    <input type="text" name="icone" value="{{ old('icone', $service->icone ?? '⚙️') }}"
        class="edc-input w-24 text-2xl text-center" placeholder="⚙️" maxlength="5">
    <p class="text-xs mt-1" style="color: var(--edc-text-muted);">Copiez-collez un emoji</p>
</div>

{{-- Catégorie --}}
<div class="mb-5">
    <label class="edc-label">Catégorie *</label>
    <select name="categorie_id" required class="edc-select">
        <option value="">-- Choisir --</option>
        @foreach($categories as $categorie)
        <option value="{{ $categorie->id }}"
            {{ old('categorie_id', $service->categorie_id ?? '') == $categorie->id ? 'selected' : '' }}>
            {{ $categorie->icone }} {{ $categorie->nom }}
        </option>
        @endforeach
    </select>
    @error('categorie_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Titre --}}
<div class="mb-5">
    <label class="edc-label">Titre *</label>
    <input type="text" name="titre" value="{{ old('titre', $service->titre ?? '') }}" required
        class="edc-input" placeholder="Ex : Création de CV professionnel">
    @error('titre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Description --}}
<div class="mb-5">
    <label class="edc-label">Description *</label>
    <textarea name="description" rows="4" required class="edc-input"
        placeholder="Décrivez ce service en détail...">{{ old('description', $service->description ?? '') }}</textarea>
    @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Prix --}}
<div class="mb-5">
    <label class="edc-label">Prix (FCFA) — laisser vide si variable</label>
    <input type="number" name="prix" value="{{ old('prix', $service->prix ?? '') }}" min="0"
        class="edc-input" placeholder="Ex : 5000">
    @error('prix') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
</div>
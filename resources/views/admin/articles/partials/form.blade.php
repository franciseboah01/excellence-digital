{{-- Image --}}
<div class="mb-5">
    <label class="edc-label">Image de couverture</label>
    @if(isset($article) && $article->image)
    <img src="{{ asset('storage/' . $article->image) }}"
        class="w-32 h-20 object-cover rounded-lg mb-2 border" style="border-color: var(--edc-border);">
    @endif
    <input type="file" name="image" accept="image/*" class="edc-input">
    @error('image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Titre --}}
<div class="mb-5">
    <label class="edc-label">Titre *</label>
    <input type="text" name="titre"
        value="{{ old('titre', $article->titre ?? '') }}" required
        class="edc-input" placeholder="Titre de l'article">
    @error('titre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Extrait --}}
<div class="mb-5">
    <label class="edc-label">Extrait (résumé)</label>
    <textarea name="extrait" rows="2" class="edc-input"
        placeholder="Résumé court de l'article...">{{ old('extrait', $article->extrait ?? '') }}</textarea>
</div>

{{-- Contenu --}}
<div class="mb-5">
    <label class="edc-label">Contenu *</label>
    <textarea name="contenu" rows="12" required class="edc-input"
        style="font-family: monospace;"
        placeholder="Contenu de l'article...">{{ old('contenu', $article->contenu ?? '') }}</textarea>
    @error('contenu') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Catégorie + Statut --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
    <div>
        <label class="edc-label">Catégorie *</label>
        <input type="text" name="categorie"
            value="{{ old('categorie', $article->categorie ?? '') }}" required
            class="edc-input" placeholder="Ex : actualite, conseil, tutoriel">
        @error('categorie') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="edc-label">Statut *</label>
        <select name="statut" required class="edc-select">
            <option value="brouillon"
                {{ old('statut', $article->statut ?? 'brouillon') == 'brouillon' ? 'selected' : '' }}>
                📝 Brouillon
            </option>
            <option value="publie"
                {{ old('statut', $article->statut ?? '') == 'publie' ? 'selected' : '' }}>
                ✅ Publié
            </option>
        </select>
    </div>
</div>
{{-- Image --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Image de couverture</label>
    @if(isset($article) && $article->image)
    <img src="{{ asset('storage/' . $article->image) }}"
        class="w-32 h-20 object-cover rounded-lg mb-2 border">
    @endif
    <input type="file" name="image" accept="image/*"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Titre --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Titre *</label>
    <input type="text" name="titre"
        value="{{ old('titre', $article->titre ?? '') }}" required
        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="Titre de l'article">
    @error('titre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Extrait --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Extrait (résumé)</label>
    <textarea name="extrait" rows="2"
        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="Résumé court de l'article...">{{ old('extrait', $article->extrait ?? '') }}</textarea>
</div>

{{-- Contenu --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Contenu *</label>
    <textarea name="contenu" rows="12" required
        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
        placeholder="Contenu de l'article...">{{ old('contenu', $article->contenu ?? '') }}</textarea>
    @error('contenu') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Catégorie + Statut --}}
<div class="grid grid-cols-2 gap-4 mb-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Catégorie *</label>
        <input type="text" name="categorie"
            value="{{ old('categorie', $article->categorie ?? '') }}" required
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Ex : actualite, conseil, tutoriel">
        @error('categorie') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Statut *</label>
        <select name="statut" required
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
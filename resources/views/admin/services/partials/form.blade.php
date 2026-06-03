{{-- Icône --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Icône (emoji)</label>
    <input type="text" name="icone" value="{{ old('icone', $service->icone ?? '⚙️') }}"
        class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-2xl text-center focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="⚙️" maxlength="5">
    <p class="text-xs text-gray-400 mt-1">Copiez-collez un emoji</p>
</div>

{{-- Titre --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Titre *</label>
    <input type="text" name="titre" value="{{ old('titre', $service->titre ?? '') }}" required
        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="Ex : Création de CV professionnel">
    @error('titre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Catégorie --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Catégorie *</label>
    <select name="categorie" required
        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">-- Choisir --</option>
        <option value="bureautique"
            {{ old('categorie', $service->categorie ?? '') == 'bureautique' ? 'selected' : '' }}>
            💼 Bureautique
        </option>
        <option value="design"
            {{ old('categorie', $service->categorie ?? '') == 'design' ? 'selected' : '' }}>
            🌐 Digital & Design
        </option>
        <option value="web_mobile"
            {{ old('categorie', $service->categorie ?? '') == 'web_mobile' ? 'selected' : '' }}>
            💻 Web & Mobile
        </option>
    </select>
    @error('categorie') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Description --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Description *</label>
    <textarea name="description" rows="4" required
        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="Décrivez ce service en détail...">{{ old('description', $service->description ?? '') }}</textarea>
    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Prix --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">
        Prix (FCFA) — laisser vide si variable
    </label>
    <input type="number" name="prix" value="{{ old('prix', $service->prix ?? '') }}" min="0"
        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="Ex : 5000">
    @error('prix') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>
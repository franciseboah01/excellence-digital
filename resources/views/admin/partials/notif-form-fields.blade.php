{{-- Type --}}
<div class="mb-4">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Type *</label>
    <select name="type" required
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="info">📢 Information</option>
        <option value="success">✅ Bonne nouvelle</option>
        <option value="warning">⚠️ Avertissement</option>
        <option value="error">❌ Alerte</option>
    </select>
</div>

{{-- Titre --}}
<div class="mb-4">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Titre *</label>
    <input type="text" name="titre" required
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="Ex : Nouveau cours disponible !">
    @error('titre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Message --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Message *</label>
    <textarea name="message" rows="3" required
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="Contenu de la notification..."></textarea>
    @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>
{{-- Type --}}
<div class="mb-4">
    <label class="edc-label">Type *</label>
    <select name="type" required class="edc-select">
        <option value="info">📢 Information</option>
        <option value="success">✅ Bonne nouvelle</option>
        <option value="warning">⚠️ Avertissement</option>
        <option value="error">❌ Alerte</option>
    </select>
</div>

{{-- Titre --}}
<div class="mb-4">
    <label class="edc-label">Titre *</label>
    <input type="text" name="titre" required class="edc-input"
        placeholder="Ex : Nouveau cours disponible !">
    @error('titre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
</div>

{{-- Message --}}
<div class="mb-5">
    <label class="edc-label">Message *</label>
    <textarea name="message" rows="3" required class="edc-input"
        placeholder="Contenu de la notification..."></textarea>
    @error('message') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
</div>
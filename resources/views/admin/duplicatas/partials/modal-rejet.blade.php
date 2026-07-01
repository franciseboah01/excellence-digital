{{-- resources/views/admin/duplicatas/partials/modal-rejet.blade.php --}}
<div id="rejetModal{{ $demande->id }}"
     class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-slate-900 rounded-xl p-6 max-w-md w-full border border-slate-800">
        <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">
            ❌ Rejeter la demande
        </h3>
        <p class="text-sm mb-4" style="color: var(--edc-text-secondary);">
            Veuillez indiquer le motif du rejet pour la demande de
            <strong>{{ $demande->user?->prenom }} {{ $demande->user?->nom }}</strong>.
        </p>
        <form action="{{ route('admin.duplicatas.rejeter', $demande) }}" method="POST" id="rejetForm{{ $demande->id }}">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="text-xs font-semibold text-slate-400 block mb-2">Motif du rejet</label>
                <textarea name="motif"
                          class="edc-input w-full"
                          rows="3"
                          placeholder="Ex: Paiement non confirmé, certificat original non valide..."
                          required></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button"
                        class="btn-secondary btn-sm flex-1"
                        onclick="document.getElementById('rejetModal{{ $demande->id }}').classList.add('hidden');">
                    Annuler
                </button>
                <button type="submit"
                        class="btn-danger btn-sm flex-1"
                        onclick="return confirm('⚠️ Confirmer le rejet de cette demande ?');">
                    Confirmer le rejet
                </button>
            </div>
        </form>
    </div>
</div>
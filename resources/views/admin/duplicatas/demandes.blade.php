@extends('layouts.admin')

@section('title', 'Demandes de duplicata')
@section('page_title', '📄 Demandes de duplicata')
@section('page_subtitle', 'Gérez les demandes de duplicata des apprenants')

@section('content')

<div class="grid grid-cols-5 gap-4 mt-6">
    @foreach([
        ['total', '📊 Total demandes', 'var(--edc-primary)'],
        ['en_attente', '⏳ En attente de paiement', 'var(--edc-accent-gold)'],
        ['paye', '💰 Payées', 'var(--edc-secondary)'],
        ['valide', '✅ Validées', 'var(--edc-secondary)'],
        ['rejete', '❌ Rejetées', 'var(--edc-danger)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">
            {{ $stats[$stat[0]] ?? 0 }}
        </p>
        <p class="stat-label">
            {{ $stat[1] }}
        </p>
    </div>
    @endforeach
</div>

<div class="edc-card mt-5 overflow-hidden">
    <div class="table-responsive">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Apprenant</th>
                    <th>Certificat original</th>
                    <th>Formation</th>
                    <th>Montant payé</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($demandes as $demande)
                <tr>
                    <td class="text-xs font-mono" style="color: var(--edc-text-muted);">
                        #{{ $demande->id }}
                    </td>

                    <td class="font-medium" style="color: var(--edc-text-primary);">
                        {{ $demande->user?->prenom ?? '—' }} {{ $demande->user?->nom ?? '' }}
                        <br>
                        <span class="text-xs" style="color: var(--edc-text-muted);">
                            {{ $demande->user?->email ?? '—' }}
                        </span>
                    </td>

                    <td class="text-xs font-mono" style="color: var(--edc-primary-light);">
                        {{ $demande->certificat?->numero_certificat ?? '—' }}
                    </td>

                    <td class="text-xs" style="color: var(--edc-text-secondary);">
                        {{ $demande->certificat?->formation?->titre ?? '—' }}
                    </td>

                    <td>
                        <span class="font-bold" style="color: var(--edc-secondary);">
                            {{ number_format($demande->montant_paye ?? 0, 0, ',', ' ') }} FCFA
                        </span>
                    </td>

                    <td>
                        @if($demande->statut === 'en_attente')
                            <span class="badge badge-warning text-xs" style="background: #F59E0B; color: #1a1a1a; padding: 2px 12px; border-radius: 9999px; font-weight: 600;">
                                ⏳ En attente de paiement
                            </span>
                        @elseif($demande->statut === 'paye')
                            <span class="badge badge-success text-xs" style="background: #10B981; color: white; padding: 2px 12px; border-radius: 9999px; font-weight: 600;">
                                💰 Payé
                            </span>
                        @elseif($demande->statut === 'valide')
                            <span class="badge badge-success text-xs" style="background: #10B981; color: white; padding: 2px 12px; border-radius: 9999px; font-weight: 600;">
                                ✅ Validé
                            </span>
                        @elseif($demande->statut === 'rejete')
                            <span class="badge badge-danger text-xs" style="background: #EF4444; color: white; padding: 2px 12px; border-radius: 9999px; font-weight: 600;">
                                ❌ Rejeté
                            </span>
                        @endif
                    </td>

                    <td class="text-xs" style="color: var(--edc-text-muted);">
                        {{ $demande->created_at ? $demande->created_at->format('d/m/Y H:i') : '—' }}
                        @if($demande->valide_le)
                            <br>
                            <span class="text-[10px]">Validé le {{ $demande->valide_le->format('d/m/Y') }}</span>
                        @endif
                        @if($demande->motif_rejet)
                            <br>
                            <span class="text-[10px] text-red-400">Motif : {{ $demande->motif_rejet }}</span>
                        @endif
                    </td>

                    <td>
                        <div class="flex items-center gap-2 whitespace-nowrap flex-wrap">

                            {{-- ===== CAS 1 : PAYÉ → Le bouton Valider n'existe QUE dans ce cas ===== --}}
                            @if($demande->statut === 'paye')
                                <form action="{{ route('admin.duplicatas.valider', $demande) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="text-xs font-medium hover:underline text-emerald-500 hover:text-emerald-600"
                                            style="cursor:pointer; background:none; border:none; padding:0;"
                                            onclick="return confirm('✅ Confirmer la validation de cette demande ? Le duplicata sera généré automatiquement.');">
                                        ✅ Valider
                                    </button>
                                </form>

                                <button type="button"
                                        class="text-xs font-medium hover:underline text-red-500 hover:text-red-600"
                                        style="cursor:pointer; background:none; border:none; padding:0;"
                                        onclick="document.getElementById('rejetModal{{ $demande->id }}').classList.remove('hidden');">
                                    ❌ Rejeter
                                </button>

                                @include('admin.duplicatas.partials.modal-rejet', ['demande' => $demande])

                            {{-- ===== CAS 2 : EN ATTENTE DE PAIEMENT → Pas de bouton Valider, juste Rejeter ===== --}}
                            @elseif($demande->statut === 'en_attente')
                                <span class="text-xs" style="color: var(--edc-text-muted);">⏳ En attente du paiement client</span>

                                <button type="button"
                                        class="text-xs font-medium hover:underline text-red-500 hover:text-red-600"
                                        style="cursor:pointer; background:none; border:none; padding:0;"
                                        onclick="document.getElementById('rejetModal{{ $demande->id }}').classList.remove('hidden');">
                                    ❌ Rejeter
                                </button>

                                @include('admin.duplicatas.partials.modal-rejet', ['demande' => $demande])

                            {{-- ===== CAS 3 : DÉJÀ TRAITÉE ===== --}}
                            @elseif($demande->statut === 'valide')
                                <span class="text-xs text-emerald-500">✅ Traitée</span>
                            @elseif($demande->statut === 'rejete')
                                <span class="text-xs text-red-500">❌ Rejetée</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center" style="color: var(--edc-text-muted);">
                        <p class="text-5xl mb-4">📭</p>
                        <p>Aucune demande de duplicata trouvée.</p>
                        <p class="text-sm mt-1">Les demandes des apprenants apparaîtront ici.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4" style="border-top: 1px solid var(--edc-border);">
        {{ $demandes->links() }}
    </div>
</div>

<script>
    // Fermer les modals en cliquant à l'extérieur
    document.querySelectorAll('[id^="rejetModal"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    });
</script>

@endsection
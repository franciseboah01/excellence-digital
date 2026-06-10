@extends('layouts.client')
@section('title', 'Mes Paiements')

@section('content')
<div class="mb-6">
    <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">💰 Mes Paiements</h1>
    <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">Historique de vos paiements</p>
</div>

<div class="edc-card overflow-hidden">
    <div class="table-responsive">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Objet</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paiements as $paiement)
                <tr>
                    <td class="font-mono text-xs" style="color: var(--edc-primary-light);">{{ $paiement->reference }}</td>
                    <td style="color: var(--edc-text-primary);">
                        @if($paiement->formation) 🎓 {{ $paiement->formation->titre }}
                        @elseif($paiement->service) 💼 {{ $paiement->service->titre }}
                        @else — @endif
                    </td>
                    <td style="color: var(--edc-text-primary);">{{ number_format($paiement->montant_paye, 0, ',', ' ') }} / {{ number_format($paiement->montant_total, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @php
                            $s = match($paiement->statut) {
                                'complete' => 'background-color: rgba(16,185,129,0.12); color: #34D399;',
                                'partiel'  => 'background-color: rgba(59,130,246,0.12); color: #60A5FA;',
                                default    => 'background-color: rgba(245,158,11,0.12); color: #FBBF24;',
                            };
                        @endphp
                        <span class="badge text-xs" style="{{ $s }}">{{ ucfirst($paiement->statut) }}</span>
                    </td>
                    <td class="text-xs" style="color: var(--edc-text-muted);">{{ $paiement->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-12 text-center" style="color: var(--edc-text-muted);">Aucun paiement.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4" style="border-top: 1px solid var(--edc-border);">{{ $paiements->links() }}</div>
</div>
@endsection
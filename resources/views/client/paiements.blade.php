@extends('layouts.client')
@section('title', 'Mes Paiements')

@section('content')
<div class="mb-6">
    <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">💰 Mes Paiements</h1>
    <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">Historique de vos paiements</p>
</div>

{{-- HISTORIQUE DES PAIEMENTS --}}
@if($paiements->count())
<div class="edc-card overflow-hidden mb-6">
    <div class="table-responsive">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Objet</th>
                    <th>Montant</th>
                    <th>Mode</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paiements as $p)
                <tr>
                    <td class="font-mono text-xs" style="color: var(--edc-primary-light);">{{ $p->reference }}</td>
                    <td style="color: var(--edc-text-primary);">
                        @if($p->formation) 🎓 {{ $p->formation->titre }}
                        @elseif($p->service) 💼 {{ $p->service->titre }}
                        @else — @endif
                    </td>
                    <td class="font-semibold" style="color: var(--edc-text-primary);">{{ number_format($p->montant_paye, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @php
                            $modeIcons = [
                                'orange_money' => '🟠 Orange',
                                'mtn_money'    => '🟡 MTN',
                                'moov_money'   => '🔵 Moov',
                                'visa'         => '💳 Visa',
                                'mastercard'   => '🔴 Mastercard',
                            ];
                        @endphp
                        <span class="text-xs">{{ $modeIcons[$p->mode_paiement] ?? $p->mode_paiement }}</span>
                    </td>
                    <td>
                        <span class="badge text-xs" style="background-color: rgba(16,185,129,0.12); color: #34D399;">✅ Payé</span>
                    </td>
                    <td class="text-xs" style="color: var(--edc-text-muted);">{{ $p->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4" style="border-top: 1px solid var(--edc-border);">{{ $paiements->links() }}</div>
</div>
@endif

{{-- FORMATIONS À PAYER --}}
@php
    $formationsAPayer = \App\Models\InscriptionFormation::with('formation')
        ->where('user_id', auth()->id())
        ->where('statut', 'valide')
        ->get();
@endphp

@if($formationsAPayer->count())
<div class="edc-card p-6 mb-6">
    <h3 class="font-bold mb-4" style="color: var(--edc-text-primary);">🎓 Formations</h3>
    @foreach($formationsAPayer as $inscription)
    <div class="flex items-center justify-between py-3" style="border-bottom: 1px solid var(--edc-border);">
        <span style="color: var(--edc-text-primary);">{{ $inscription->formation->titre }}</span>
        <a href="{{ route('client.paiement.form', ['formation', $inscription->formation->id]) }}" class="btn-primary btn-xs">💳 Payer</a>
    </div>
    @endforeach
</div>
@endif

{{-- SERVICES À PAYER --}}
@php
    $servicesAPayer = \App\Models\DemandeService::with('service')
        ->where('user_id', auth()->id())
        ->where('statut', 'termine')
        ->get();
@endphp

@if($servicesAPayer->count())
<div class="edc-card p-6 mb-6">
    <h3 class="font-bold mb-4" style="color: var(--edc-text-primary);">💼 Services terminés</h3>
    @foreach($servicesAPayer as $demande)
    <div class="flex items-center justify-between py-3" style="border-bottom: 1px solid var(--edc-border);">
        <span style="color: var(--edc-text-primary);">{{ $demande->service->titre }}</span>
        <a href="{{ route('client.paiement.form', ['service', $demande->service->id]) }}" class="btn-primary btn-xs">💳 Payer</a>
    </div>
    @endforeach
</div>
@endif

{{-- AUCUN PAIEMENT NI ÉLÉMENT À PAYER --}}
@if($paiements->isEmpty() && $formationsAPayer->isEmpty() && $servicesAPayer->isEmpty())
<div class="edc-card text-center py-16" style="color: var(--edc-text-muted);">
    <p class="text-5xl mb-4">💰</p>
    <p class="font-medium">Aucun paiement ni élément à payer pour le moment.</p>
</div>
@endif

@endsection
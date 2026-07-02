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
                        @if($p->formation)
                            🎓 {{ $p->formation->titre }}
                        @elseif($p->service)
                            💼 {{ $p->service->titre }}
                        @elseif($p->certificat)
                            🔄 Duplicata Certificat ({{ $p->certificat->formation?->titre ?? 'N° ' . $p->certificat->numero_certificat }})
                        @else
                            —
                        @endif
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
    // ✅ CORRECTION : on exclut désormais les formations gratuites (prix null/0).
    // Avant cette correction, une formation gratuite n'ayant jamais de paiement
    // associé (normal, puisqu'elle est gratuite) était systématiquement listée
    // comme "à payer", proposant au client de payer 0 FCFA pour rien.
    $formationsAPayer = \App\Models\InscriptionFormation::with('formation')
        ->where('user_id', auth()->id())
        ->where('statut', 'valide')
        ->whereHas('formation', function($q) {
            $q->where('prix', '>', 0);
        })
        ->whereDoesntHave('formation.paiements', function($q) {
            $q->where('user_id', auth()->id())->where('statut', 'complete');
        })
        ->get();
@endphp

@if($formationsAPayer->count())
<div class="edc-card p-6 mb-6">
    <h3 class="font-bold mb-4" style="color: var(--edc-text-primary);">🎓 Formations en attente de paiement</h3>
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
    // ✅ CHANGEMENT DE RÈGLE MÉTIER : le paiement (partiel ou total) est
    // désormais requis AVANT que l'admin démarre le service ("en_cours"),
    // pas après qu'il soit "termine". On liste donc les demandes encore
    // "en_attente", pour un service payant, sans paiement enregistré.
    $servicesAPayer = \App\Models\DemandeService::with('service')
        ->where('user_id', auth()->id())
        ->where('statut', 'en_attente')
        ->whereHas('service', function($q) {
            $q->where('prix', '>', 0);
        })
        ->whereNotIn('id', \App\Models\Paiement::whereNotNull('demande_id')
            ->where('montant_paye', '>', 0)
            ->pluck('demande_id')
        )
        ->get();
@endphp

@if($servicesAPayer->count())
<div class="edc-card p-6 mb-6">
    <h3 class="font-bold mb-4" style="color: var(--edc-text-primary);">💼 Services en attente de paiement</h3>
    <p class="text-xs mb-4" style="color: var(--edc-text-muted);">Un paiement (partiel ou total) est nécessaire avant que notre équipe puisse démarrer ces services.</p>
    @foreach($servicesAPayer as $demande)
    <div class="flex items-center justify-between py-3" style="border-bottom: 1px solid var(--edc-border);">
        <div>
            <span style="color: var(--edc-text-primary);">{{ $demande->service->titre }}</span>
            <span class="text-xs block" style="color: var(--edc-text-muted);">{{ number_format($demande->service->prix, 0, ',', ' ') }} FCFA</span>
        </div>
        <a href="{{ route('client.paiement.form', ['type' => 'service', 'id' => $demande->id]) }}" class="btn-primary btn-xs">💳 Payer</a>
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
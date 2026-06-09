@extends('layouts.admin')
@section('title', 'Paiement ' . $paiement->reference)
@section('page_title', '💳 Détail du Paiement')

@section('content')
<div class="mt-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
    <a href="{{ route('admin.paiements.index') }}"
        class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
        style="color: var(--edc-primary-light);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Retour</span>
    </a>
    <a href="{{ route('admin.paiements.recu', $paiement) }}" class="btn-success btn-sm">📄 Télécharger le reçu PDF</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">

    {{-- INFOS --}}
    <div class="lg:col-span-2 space-y-5">

        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">📋 Informations du paiement</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p style="color: var(--edc-text-muted);">Référence</p>
                    <p class="font-mono font-bold" style="color: var(--edc-primary-light);">{{ $paiement->reference }}</p>
                </div>
                <div>
                    <p style="color: var(--edc-text-muted);">Date</p>
                    <p class="font-semibold" style="color: var(--edc-text-primary);">{{ $paiement->date_paiement?->format('d/m/Y') ?? now()->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p style="color: var(--edc-text-muted);">Client</p>
                    <p class="font-semibold" style="color: var(--edc-text-primary);">{{ $paiement->user->prenom }} {{ $paiement->user->nom }}</p>
                </div>
                <div>
                    <p style="color: var(--edc-text-muted);">Mode</p>
                    <p class="font-semibold" style="color: var(--edc-text-primary);">{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</p>
                </div>
                <div>
                    <p style="color: var(--edc-text-muted);">Objet</p>
                    <p class="font-semibold" style="color: var(--edc-text-primary);">
                        @if($paiement->formation) 🎓 {{ $paiement->formation->titre }}
                        @elseif($paiement->service) 💼 {{ $paiement->service->titre }}
                        @else — @endif
                    </p>
                </div>
                <div>
                    <p style="color: var(--edc-text-muted);">Enregistré par</p>
                    <p class="font-semibold" style="color: var(--edc-text-primary);">{{ $paiement->enregistrePar?->prenom ?? '—' }}</p>
                </div>
            </div>
            @if($paiement->notes)
            <div class="mt-4 rounded-lg p-3 text-sm" style="background-color: var(--edc-bg-base); color: var(--edc-text-secondary);">
                📝 {{ $paiement->notes }}
            </div>
            @endif
        </div>

        {{-- Mise à jour --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">🔄 Mettre à jour le paiement</h3>
            <form method="POST" action="{{ route('admin.paiements.update', $paiement) }}" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="edc-label">Montant payé (FCFA)</label>
                        <input type="number" name="montant_paye" value="{{ $paiement->montant_paye }}"
                            min="0" max="{{ $paiement->montant_total }}" step="100" required class="edc-input">
                    </div>
                    <div>
                        <label class="edc-label">Mode de paiement</label>
                        <select name="mode_paiement" required class="edc-select">
                            <option value="especes"      {{ $paiement->mode_paiement == 'especes' ? 'selected' : '' }}>💵 Espèces</option>
                            <option value="mobile_money" {{ $paiement->mode_paiement == 'mobile_money' ? 'selected' : '' }}>📱 Mobile Money</option>
                            <option value="virement"     {{ $paiement->mode_paiement == 'virement' ? 'selected' : '' }}>🏦 Virement</option>
                            <option value="autre"        {{ $paiement->mode_paiement == 'autre' ? 'selected' : '' }}>🔄 Autre</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="edc-label">Notes</label>
                    <textarea name="notes" rows="2" class="edc-input">{{ $paiement->notes }}</textarea>
                </div>
                <button type="submit" class="btn-primary w-full">💾 Mettre à jour</button>
            </form>
        </div>
    </div>

    {{-- PANEL STATUT --}}
    <div class="space-y-5">
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">💰 Récapitulatif</h3>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span style="color: var(--edc-text-muted);">Total</span>
                    <span class="font-bold" style="color: var(--edc-text-primary);">{{ number_format($paiement->montant_total, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between">
                    <span style="color: var(--edc-text-muted);">Payé</span>
                    <span class="font-bold" style="color: var(--edc-secondary);">{{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</span>
                </div>
                @if($paiement->montant_restant > 0)
                <div class="flex justify-between">
                    <span style="color: var(--edc-text-muted);">Restant</span>
                    <span class="font-bold" style="color: var(--edc-danger);">{{ number_format($paiement->montant_restant, 0, ',', ' ') }} FCFA</span>
                </div>
                @endif
            </div>

            <div class="mt-4">
                <div class="flex justify-between text-xs mb-1" style="color: var(--edc-text-muted);">
                    <span>Progression</span>
                    <span>{{ $paiement->pourcentage }}%</span>
                </div>
                <div class="w-full rounded-full h-3" style="background-color: var(--edc-bg-elevated);">
                    <div class="h-3 rounded-full transition-all"
                        style="width:{{ $paiement->pourcentage }}%;
                        background-color: {{ $paiement->pourcentage == 100 ? 'var(--edc-secondary)' : 'var(--edc-primary)' }};">
                    </div>
                </div>
            </div>

            <div class="mt-4 text-center">
                @php
                    $badge = match($paiement->statut) {
                        'complete'   => 'background-color: rgba(16,185,129,0.12); color: #34D399; border-color: rgba(16,185,129,0.30);',
                        'partiel'    => 'background-color: rgba(59,130,246,0.12); color: #60A5FA; border-color: rgba(59,130,246,0.30);',
                        default      => 'background-color: rgba(245,158,11,0.12); color: #FBBF24; border-color: rgba(245,158,11,0.30);',
                    };
                    $label = match($paiement->statut) {
                        'complete'   => '✅ Paiement complet',
                        'partiel'    => '⚠️ Paiement partiel',
                        default      => '⏳ En attente',
                    };
                @endphp
                <span class="inline-block rounded-xl px-4 py-2 text-sm font-bold" style="{{ $badge }}; border: 2px solid;">
                    {{ $label }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
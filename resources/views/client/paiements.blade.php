@extends('layouts.client')
@section('title', 'Mes Paiements')

@section('content')

{{-- HEADER - Optimisé mobile --}}
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">
                💰 Mes Paiements
            </h1>
            <p class="text-xs sm:text-sm mt-0.5" style="color: var(--edc-text-secondary);">
                Historique et paiements en attente
            </p>
        </div>
        
        {{-- Stats rapides --}}
        @if($paiements->count())
        <div class="flex items-center gap-2">
            <span class="text-xs px-3 py-1.5 rounded-full flex items-center gap-1.5"
                  style="background-color: rgba(16,185,129,0.1); color: #34D399;">
                <span>✅</span>
                <span>{{ $paiements->total() }} paiement(s)</span>
            </span>
        </div>
        @endif
    </div>
</div>

{{-- PAIEMENTS EN ATTENTE (priorité) --}}
@if($formationsAPayer->count() || $servicesAPayer->count())
<div class="mb-6">
    <div class="flex items-center gap-2 mb-3">
        <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: var(--edc-accent-gold);"></span>
        <h2 class="text-sm font-bold uppercase tracking-wider" style="color: var(--edc-accent-gold);">
            En attente de paiement
        </h2>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
        {{-- Formations à payer --}}
        @foreach($formationsAPayer as $inscription)
        <div class="edc-card p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
             style="border-left: 3px solid var(--edc-accent-gold);">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="badge text-[10px]" style="background-color: rgba(139,92,246,0.12); color: #C084FC;">
                        🎓 Formation
                    </span>
                </div>
                <p class="text-sm font-semibold line-clamp-2" style="color: var(--edc-text-primary);">
                    {{ $inscription->formation->titre }}
                </p>
                <p class="text-lg font-bold mt-1" style="color: var(--edc-primary-light);">
                    {{ number_format($inscription->formation->prix, 0, ',', ' ') }} FCFA
                </p>
            </div>
            <a href="{{ route('client.paiement.form', ['formation', $inscription->formation->id]) }}" 
               class="btn-primary btn-touch btn-sm flex-shrink-0 w-full sm:w-auto justify-center">
                <span>💳</span><span>Payer</span>
            </a>
        </div>
        @endforeach
        
        {{-- Services à payer --}}
        @foreach($servicesAPayer as $demande)
        <div class="edc-card p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
             style="border-left: 3px solid var(--edc-accent-gold);">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="badge text-[10px]" style="background-color: rgba(59,130,246,0.12); color: #60A5FA;">
                        💼 Service
                    </span>
                    <span class="badge text-[10px]" style="background-color: rgba(245,158,11,0.12); color: #F59E0B;">
                        ⏳ En attente
                    </span>
                </div>
                <p class="text-sm font-semibold line-clamp-2" style="color: var(--edc-text-primary);">
                    {{ $demande->service->titre }}
                </p>
                <p class="text-lg font-bold mt-1" style="color: var(--edc-primary-light);">
                    {{ number_format($demande->service->prix, 0, ',', ' ') }} FCFA
                </p>
            </div>
            <a href="{{ route('client.paiement.form', ['type' => 'service', 'id' => $demande->id]) }}" 
               class="btn-primary btn-touch btn-sm flex-shrink-0 w-full sm:w-auto justify-center">
                <span>💳</span><span>Payer</span>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- HISTORIQUE DES PAIEMENTS --}}
@if($paiements->count())
<div class="mb-6">
    <div class="flex items-center gap-2 mb-3">
        <span class="w-2 h-2 rounded-full" style="background-color: var(--edc-secondary);"></span>
        <h2 class="text-sm font-bold uppercase tracking-wider" style="color: var(--edc-text-secondary);">
            Historique des paiements
        </h2>
    </div>

    {{-- Version Desktop : Tableau --}}
    <div class="hidden lg:block edc-card overflow-hidden">
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
                    <tr class="transition-colors hover:bg-white/[0.02]">
                        <td class="font-mono text-xs" style="color: var(--edc-primary-light);">
                            #{{ $p->reference }}
                        </td>
                        <td style="color: var(--edc-text-primary);">
                            <div class="flex items-center gap-1.5">
                                @if($p->formation)
                                    <span>🎓</span>
                                    <span class="truncate max-w-[200px]">{{ $p->formation->titre }}</span>
                                @elseif($p->service)
                                    <span>💼</span>
                                    <span class="truncate max-w-[200px]">{{ $p->service->titre }}</span>
                                @elseif($p->certificat)
                                    <span>🔄</span>
                                    <span class="truncate max-w-[200px]">Duplicata certificat</span>
                                @else
                                    —
                                @endif
                            </div>
                        </td>
                        <td class="font-semibold" style="color: var(--edc-text-primary);">
                            {{ number_format($p->montant_paye, 0, ',', ' ') }} FCFA
                        </td>
                        <td>
                            @php
                                $modeIcons = [
                                    'orange_money' => ['🟠', 'Orange'],
                                    'mtn_money'    => ['🟡', 'MTN'],
                                    'moov_money'   => ['🔵', 'Moov'],
                                    'visa'         => ['💳', 'Visa'],
                                    'mastercard'   => ['🔴', 'Mastercard'],
                                ];
                            @endphp
                            <span class="text-xs">{{ ($modeIcons[$p->mode_paiement][0] ?? '') . ' ' . ($modeIcons[$p->mode_paiement][1] ?? $p->mode_paiement) }}</span>
                        </td>
                        <td>
                            <span class="badge text-xs flex items-center gap-1" 
                                  style="background-color: rgba(16,185,129,0.12); color: #34D399;">
                                <span>✅</span><span>Payé</span>
                            </span>
                        </td>
                        <td class="text-xs" style="color: var(--edc-text-muted);">
                            {{ $p->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4" style="border-top: 1px solid var(--edc-border);">
            {{ $paiements->links() }}
        </div>
    </div>

    {{-- Version Mobile : Cartes --}}
    <div class="lg:hidden space-y-3">
        @foreach($paiements as $p)
        <div class="edc-card p-4 transition-all duration-200 hover:shadow-hover">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2">
                    @if($p->formation)
                        <span class="text-lg">🎓</span>
                    @elseif($p->service)
                        <span class="text-lg">💼</span>
                    @elseif($p->certificat)
                        <span class="text-lg">🔄</span>
                    @else
                        <span class="text-lg">💰</span>
                    @endif
                    <div>
                        <p class="text-sm font-semibold line-clamp-1" style="color: var(--edc-text-primary);">
                            @if($p->formation)
                                {{ $p->formation->titre }}
                            @elseif($p->service)
                                {{ $p->service->titre }}
                            @elseif($p->certificat)
                                Duplicata certificat
                            @else
                                —
                            @endif
                        </p>
                        <p class="font-mono text-[10px] mt-0.5" style="color: var(--edc-primary-light);">
                            #{{ $p->reference }}
                        </p>
                    </div>
                </div>
                <span class="badge text-[10px] flex-shrink-0 flex items-center gap-1"
                      style="background-color: rgba(16,185,129,0.12); color: #34D399;">
                    <span>✅</span><span>Payé</span>
                </span>
            </div>
            
            <div class="flex items-center justify-between pt-3" style="border-top: 1px solid var(--edc-border);">
                <span class="text-base sm:text-lg font-bold" style="color: var(--edc-text-primary);">
                    {{ number_format($p->montant_paye, 0, ',', ' ') }} FCFA
                </span>
                <div class="flex items-center gap-3 text-xs" style="color: var(--edc-text-muted);">
                    @php
                        $modeIcons = [
                            'orange_money' => '🟠',
                            'mtn_money'    => '🟡',
                            'moov_money'   => '🔵',
                            'visa'         => '💳',
                            'mastercard'   => '🔴',
                        ];
                    @endphp
                    <span>{{ $modeIcons[$p->mode_paiement] ?? '' }}</span>
                    <span>{{ $p->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
        @endforeach
        
        {{-- Pagination mobile --}}
        @if($paiements->hasPages())
        <div class="mt-4">
            {{ $paiements->onEachSide(1)->links() }}
        </div>
        @endif
    </div>
</div>
@endif

{{-- ÉTAT VIDE --}}
@if($paiements->isEmpty() && $formationsAPayer->isEmpty() && $servicesAPayer->isEmpty())
<div class="edc-card text-center py-16 sm:py-20 px-4" style="color: var(--edc-text-muted);">
    <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-3xl sm:text-4xl"
         style="background-color: var(--edc-bg-base);">
        💰
    </div>
    <p class="font-semibold text-sm sm:text-base mb-2" style="color: var(--edc-text-secondary);">
        Aucun paiement pour le moment
    </p>
    <p class="text-xs sm:text-sm">
        Vos paiements apparaîtront ici lorsque vous effectuerez un achat.
    </p>
</div>
@endif

{{-- Message d'aide --}}
@if($paiements->count() || $formationsAPayer->count() || $servicesAPayer->count())
<div class="mt-6 text-center">
    <p class="text-xs" style="color: var(--edc-text-muted);">
        💡 Besoin d'une facture ? <a href="{{ route('contact') }}" class="underline" style="color: var(--edc-primary-light);">Contactez-nous</a>
    </p>
</div>
@endif

@endsection
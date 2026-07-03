@extends('layouts.client')
@section('title', 'Mes Demandes')

@section('content')

{{-- HEADER - Optimisé mobile --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">
            📋 Mes Demandes de Service
        </h1>
        <p class="text-xs sm:text-sm mt-0.5" style="color: var(--edc-text-secondary);">
            {{ $demandes->total() }} demande(s) au total
        </p>
    </div>
    <a href="{{ route('client.demande.form') }}" 
       class="btn-primary btn-touch w-full sm:w-auto justify-center">
        <span>➕</span><span>Nouvelle demande</span>
    </a>
</div>

{{-- FILTRES RAPIDES (optionnel, à activer si tu as assez de demandes) --}}
@if($demandes->total() > 3)
<div class="flex gap-2 mb-4 overflow-x-auto pb-2 scrollbar-hide">
    @php
        $filtres = [
            'tous' => ['📋', 'Toutes', null],
            'en_attente' => ['⏳', 'En attente', 'var(--edc-accent-gold)'],
            'en_cours' => ['🔄', 'En cours', 'var(--edc-secondary)'],
            'termine' => ['✅', 'Terminées', '#10B981'],
            'annule' => ['❌', 'Annulées', 'var(--edc-danger)'],
        ];
        $currentFilter = request('statut', 'tous');
    @endphp
    
    @foreach($filtres as $key => $filtre)
    <a href="{{ route('client.demandes', $key !== 'tous' ? ['statut' => $key] : []) }}" 
       class="flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition flex-shrink-0"
       style="{{ $currentFilter === $key 
           ? 'background-color: '.($filtre[2] ?? 'var(--edc-primary)').'; color: white;' 
           : 'background-color: var(--edc-bg-card); color: var(--edc-text-secondary); border: 1px solid var(--edc-border);' }}">
        <span>{{ $filtre[0] }}</span>
        <span>{{ $filtre[1] }}</span>
    </a>
    @endforeach
</div>
@endif

{{-- LISTE DES DEMANDES --}}
<div class="edc-card overflow-hidden">
    @forelse($demandes as $demande)
    <div class="p-4 sm:p-6 transition-colors hover:bg-white/[0.01]" 
         style="border-bottom: 1px solid var(--edc-border);">

        {{-- En-tête demande --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div class="flex items-start gap-3 min-w-0">
                {{-- Icône du service --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                     style="background-color: var(--edc-bg-base);">
                    {{ $demande->service->icone ?? '💼' }}
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-sm sm:text-base truncate" style="color: var(--edc-text-primary);">
                        {{ $demande->service->titre }}
                    </h3>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <p class="text-xs" style="color: var(--edc-text-muted);">
                            #{{ $demande->id }} • {{ $demande->created_at->format('d/m/Y') }}
                        </p>
                        <span class="text-xs hidden sm:inline" style="color: var(--edc-text-muted);">•</span>
                        <p class="text-xs hidden sm:inline" style="color: var(--edc-text-muted);">
                            {{ $demande->created_at->format('H:i') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 sm:flex-shrink-0">
                @include('client.partials.statut-badge', ['statut' => $demande->statut])
            </div>
        </div>

        {{-- TIMELINE STATUT - Version mobile améliorée --}}
        <div class="mt-4">
            <div class="flex items-center justify-between overflow-x-auto pb-2 scrollbar-hide">
                @php
                    $etapes = [
                        'en_attente' => ['⏳', 'En attente', 'Votre demande a été envoyée'],
                        'en_cours'   => ['🔄', 'En cours', 'Nous traitons votre demande'],
                        'termine'    => ['✅', 'Terminé', 'Service livré avec succès'],
                    ];
                    
                    if($demande->statut === 'annule') {
                        $etapes = [
                            'annule' => ['❌', 'Annulée', 'Cette demande a été annulée'],
                        ];
                    }
                    
                    $statuts = array_keys($etapes);
                    $indexActuel = array_search($demande->statut, $statuts);
                @endphp

                @foreach($etapes as $key => $etape)
                @php $index = array_search($key, $statuts); @endphp
                <div class="flex items-center flex-1 min-w-0">
                    {{-- Étape --}}
                    <div class="flex flex-col items-center min-w-[70px] sm:min-w-[80px]">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-sm sm:text-base font-bold transition-all duration-300 relative"
                            style="{{ $index <= $indexActuel
                                ? 'background: linear-gradient(135deg, #3B82F6, #1D4ED8); color: #fff; box-shadow: 0 4px 12px rgba(59,130,246,0.3);'
                                : 'background-color: var(--edc-bg-elevated); color: var(--edc-text-muted);' }}">
                            {{ $etape[0] }}
                            
                            {{-- Indicateur actif --}}
                            @if($index === $indexActuel && $demande->statut !== 'annule')
                            <div class="absolute -bottom-1 w-2 h-2 rounded-full animate-pulse"
                                 style="background-color: var(--edc-primary);">
                            </div>
                            @endif
                        </div>
                        <p class="text-[10px] sm:text-xs mt-1.5 font-semibold text-center"
                           style="{{ $index <= $indexActuel ? 'color: var(--edc-primary-light);' : 'color: var(--edc-text-muted);' }}">
                            {{ $etape[1] }}
                        </p>
                        {{-- Tooltip sur desktop --}}
                        <p class="text-[9px] mt-0.5 text-center hidden sm:block"
                           style="color: var(--edc-text-muted);">
                            {{ $etape[2] }}
                        </p>
                    </div>
                    
                    {{-- Barre de progression --}}
                    @if(!$loop->last && $demande->statut !== 'annule')
                    <div class="flex-1 h-1 mx-1 sm:mx-2 rounded-full transition-all duration-500 min-w-[20px]"
                        style="{{ $index < $indexActuel 
                            ? 'background: linear-gradient(90deg, #3B82F6, #1D4ED8);' 
                            : 'background-color: var(--edc-bg-elevated);' }}">
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Message / Description --}}
        @if($demande->message)
        <div class="mt-4 text-sm rounded-xl p-3 sm:p-4 transition-colors hover:bg-opacity-80"
            style="background-color: var(--edc-bg-base); color: var(--edc-text-secondary); border: 1px solid var(--edc-border);">
            <div class="flex items-start gap-2">
                <span class="text-lg flex-shrink-0">💬</span>
                <div class="min-w-0">
                    <p class="text-xs font-semibold mb-1" style="color: var(--edc-text-muted);">Votre message :</p>
                    <p class="text-sm whitespace-pre-line">{{ $demande->message }}</p>
                </div>
            </div>
        </div>
        @endif
        
        {{-- Actions rapides (optionnel) --}}
        @if($demande->statut === 'termine')
        <div class="mt-3 flex justify-end">
            <a href="{{ route('client.demande.form') }}" 
               class="text-xs font-medium hover:underline" 
               style="color: var(--edc-primary-light);">
                Faire une nouvelle demande similaire →
            </a>
        </div>
        @endif
    </div>
    @empty
    <div class="text-center py-16 px-4" style="color: var(--edc-text-muted);">
        <p class="text-5xl sm:text-6xl mb-4">📋</p>
        <p class="font-semibold text-sm sm:text-base mb-2" style="color: var(--edc-text-secondary);">
            Aucune demande pour le moment
        </p>
        <p class="text-xs sm:text-sm mb-6">
            Commencez par créer votre première demande de service
        </p>
        <a href="{{ route('client.demande.form') }}" 
           class="btn-primary btn-touch inline-flex items-center gap-2">
            <span>➕</span><span>Créer ma première demande</span>
        </a>
    </div>
    @endforelse
</div>

{{-- PAGINATION - Optimisée mobile --}}
@if($demandes->hasPages())
<div class="mt-4 flex justify-center">
    {{ $demandes->onEachSide(1)->links() }}
</div>
@endif

{{-- Message d'aide en bas si beaucoup de demandes --}}
@if($demandes->total() > 5)
<div class="mt-6 text-center">
    <p class="text-xs" style="color: var(--edc-text-muted);">
        💡 Vous avez {{ $demandes->total() }} demandes. 
        Utilisez les filtres ci-dessus pour les retrouver plus facilement.
    </p>
</div>
@endif

@endsection
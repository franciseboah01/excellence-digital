@extends('layouts.client')
@section('title', 'Ressources — ' . $formation->titre)

@section('content')
<div class="max-w-4xl mx-auto">
    
    {{-- FIL D'ARIANE --}}
    <nav class="mb-4 sm:mb-6">
        <a href="{{ route('client.formations') }}" 
           class="inline-flex items-center space-x-1.5 text-sm font-medium hover:underline group transition"
           style="color: var(--edc-primary-light);">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retour aux formations</span>
        </a>
    </nav>

    {{-- EN-TÊTE AVEC IMAGE --}}
    <div class="edc-card overflow-hidden mb-6">
        {{-- Image avec ratio adaptatif --}}
        @if($formation->image)
        <div class="relative aspect-[16/9] sm:aspect-[21/9] overflow-hidden">
            <img src="{{ asset('storage/' . $formation->image) }}" 
                 alt="{{ $formation->titre }}"
                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                 loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-[#0B0F1A] via-transparent to-transparent opacity-60"></div>
        </div>
        @else
        <div class="w-full aspect-[16/9] sm:aspect-[21/9] flex items-center justify-center relative overflow-hidden"
            style="background: linear-gradient(135deg, #1e3a8a, #2563eb);">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-1/4 left-1/4 w-32 h-32 rounded-full bg-white"></div>
                <div class="absolute bottom-1/4 right-1/4 w-48 h-48 rounded-full bg-white"></div>
            </div>
            <span class="text-6xl sm:text-7xl relative z-10">🎓</span>
        </div>
        @endif
        
        {{-- Contenu --}}
        <div class="p-4 sm:p-6">
            {{-- Badges - Scrollable sur mobile --}}
            <div class="flex flex-wrap items-center gap-2 mb-3 overflow-x-auto pb-1 scrollbar-hide">
                <span class="badge text-xs flex-shrink-0" 
                      style="background-color: rgba(59,130,246,0.12); color: #60A5FA;">
                    {{ $formation->module->icone ?? '📚' }} {{ $formation->module->nom ?? 'Module' }}
                </span>
                
                @if($formation->duree)
                <span class="badge text-xs flex-shrink-0" 
                      style="background-color: rgba(148,163,184,0.10); color: #94A3B8;">
                    ⏱ {{ $formation->duree }}
                </span>
                @endif
                
                @if($formation->prix && $formation->prix > 0)
                <span class="badge text-xs flex-shrink-0" 
                      style="background-color: rgba(16,185,129,0.12); color: #34D399;">
                    💰 {{ number_format($formation->prix, 0, ',', ' ') }} FCFA
                </span>
                @endif
                
                {{-- Compteur de ressources --}}
                @php
                    $totalRessources = $ressources_generales->count() + $niveaux->sum(fn($n) => $n->ressources->count());
                @endphp
                <span class="badge text-xs flex-shrink-0" 
                      style="background-color: rgba(139,92,246,0.12); color: #C084FC;">
                    📦 {{ $totalRessources }} ressource(s)
                </span>
            </div>
            
            {{-- Titre --}}
            <h1 class="text-xl sm:text-2xl font-extrabold leading-tight" 
                style="color: var(--edc-text-primary);">
                📚 {{ $formation->titre }}
            </h1>

            {{-- Description --}}
            <div class="mt-4 rounded-xl p-4 transition-colors"
                style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
                <div class="flex items-start gap-2">
                    <span class="text-lg flex-shrink-0 mt-0.5">📝</span>
                    <div>
                        <p class="text-sm font-semibold mb-1.5" style="color: var(--edc-text-primary);">
                            Description
                        </p>
                        <p class="text-sm leading-relaxed" style="color: var(--edc-text-secondary);">
                            {{ $formation->description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ressources générales --}}
    @if($ressources_generales->count())
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base sm:text-lg font-bold flex items-center gap-2" 
                style="color: var(--edc-text-primary);">
                <span>📁</span> Ressources générales
            </h2>
            <span class="text-xs px-2 py-0.5 rounded-full" 
                  style="background-color: rgba(59,130,246,0.1); color: var(--edc-primary-light);">
                {{ $ressources_generales->count() }}
            </span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            @foreach($ressources_generales as $ressource)
                @include('client.partials.ressource-card', ['ressource' => $ressource])
            @endforeach
        </div>
    </div>
    @endif

    {{-- Ressources par niveau - Accordéon mobile --}}
    @forelse($niveaux as $niveau)
    <div class="edc-card mb-4 sm:mb-6 overflow-hidden transition-all duration-300">
        
        {{-- En-tête du niveau (cliquable sur mobile) --}}
        <button onclick="toggleNiveau(this)" 
                class="w-full px-4 sm:px-6 py-4 flex items-center justify-between gap-3 transition-colors hover:bg-white/[0.02] text-left"
                style="background-color: rgba(59,130,246,0.06); border-left: 4px solid var(--edc-primary);">
            <div class="flex items-center gap-3 min-w-0">
                {{-- Numéro du niveau --}}
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0 text-white"
                     style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                    {{ $niveau->ordre }}
                </div>
                
                <div class="min-w-0">
                    <h2 class="text-sm sm:text-base font-bold flex items-center gap-2 truncate" 
                        style="color: var(--edc-text-primary);">
                        <span>📂</span>
                        <span>{{ $niveau->nom }}</span>
                    </h2>
                    @if($niveau->description)
                    <p class="text-xs mt-0.5 truncate" style="color: var(--edc-text-secondary);">
                        {{ $niveau->description }}
                    </p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                {{-- Compteur de ressources --}}
                <span class="text-xs px-2 py-0.5 rounded-full hidden sm:inline"
                      style="background-color: rgba(59,130,246,0.1); color: var(--edc-primary-light);">
                    {{ $niveau->ressources->count() }}
                </span>
                
                {{-- Flèche toggle --}}
                <svg class="w-5 h-5 transition-transform duration-300 transform niveau-chevron" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: var(--edc-text-muted);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>

        {{-- Contenu du niveau (collapsible sur mobile) --}}
        <div class="niveau-content transition-all duration-300">
            <div class="p-4 sm:p-6">
                @if($niveau->ressources->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    @foreach($niveau->ressources as $ressource)
                        @include('client.partials.ressource-card', ['ressource' => $ressource])
                    @endforeach
                </div>
                @else
                <div class="text-center py-8" style="color: var(--edc-text-muted);">
                    <p class="text-3xl mb-2">📭</p>
                    <p class="text-sm">Aucune ressource disponible pour ce niveau.</p>
                    <p class="text-xs mt-1">Revenez plus tard.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="edc-card text-center py-16 sm:py-20 px-4" style="color: var(--edc-text-muted);">
        <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-3xl sm:text-4xl"
             style="background-color: var(--edc-bg-base);">
            📭
        </div>
        <p class="font-semibold text-sm sm:text-base mb-2" style="color: var(--edc-text-secondary);">
            Aucun contenu disponible
        </p>
        <p class="text-xs sm:text-sm">
            L'enseignant ajoutera bientôt des ressources pour cette formation.
        </p>
    </div>
    @endforelse
    
    {{-- Message d'aide --}}
    @if($totalRessources > 0)
    <div class="mt-6 text-center">
        <p class="text-xs" style="color: var(--edc-text-muted);">
            💡 Cliquez sur une ressource pour la consulter ou la télécharger.
        </p>
    </div>
    @endif
</div>

{{-- Script pour le toggle des niveaux --}}
<script>
function toggleNiveau(button) {
    const content = button.nextElementSibling;
    const chevron = button.querySelector('.niveau-chevron');
    
    if (content.style.maxHeight) {
        // Fermer
        content.style.maxHeight = null;
        chevron.style.transform = 'rotate(0deg)';
    } else {
        // Ouvrir
        content.style.maxHeight = content.scrollHeight + 'px';
        chevron.style.transform = 'rotate(180deg)';
    }
}

// Ouvrir le premier niveau par défaut sur desktop
document.addEventListener('DOMContentLoaded', function() {
    const firstNiveau = document.querySelector('.niveau-content');
    if (firstNiveau) {
        firstNiveau.style.maxHeight = firstNiveau.scrollHeight + 'px';
        const chevron = firstNiveau.closest('.edc-card').querySelector('.niveau-chevron');
        if (chevron) chevron.style.transform = 'rotate(180deg)';
    }
});
</script>

<style>
.niveau-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
}

/* Sur desktop, on ouvre tout par défaut */
@media (min-width: 1024px) {
    .niveau-content {
        max-height: none !important;
        overflow: visible !important;
    }
    
    .niveau-chevron {
        display: none;
    }
}
</style>
@endsection
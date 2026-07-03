@extends('layouts.client')
@section('title', $formation->titre ?? 'Formation')

@section('content')
<div class="max-w-4xl mx-auto">
    
    {{-- FIL D'ARIANE - Optimisé mobile --}}
    <nav class="mb-4 sm:mb-6">
        <a href="{{ route('client.formations') }}" 
           class="inline-flex items-center space-x-1.5 text-sm font-medium hover:underline group transition"
           style="color: var(--edc-primary-light);">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retour à mes formations</span>
        </a>
    </nav>

    {{-- EN-TÊTE AVEC IMAGE - Optimisé mobile --}}
    <div class="edc-card overflow-hidden mb-6">
        {{-- Image avec ratio adaptatif --}}
        @if($formation->image)
        <div class="relative aspect-[16/9] sm:aspect-[21/9] overflow-hidden">
            <img src="{{ asset('storage/' . $formation->image) }}" 
                 alt="{{ $formation->titre }}"
                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                 loading="lazy">
            {{-- Overlay gradient pour lisibilité --}}
            <div class="absolute inset-0 bg-gradient-to-t from-[#0B0F1A] via-transparent to-transparent opacity-60"></div>
        </div>
        @else
        <div class="w-full aspect-[16/9] sm:aspect-[21/9] flex items-center justify-center relative overflow-hidden"
            style="background: linear-gradient(135deg, #1e3a8a, #2563eb);">
            {{-- Cercles décoratifs --}}
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-1/4 left-1/4 w-32 h-32 rounded-full bg-white"></div>
                <div class="absolute bottom-1/4 right-1/4 w-48 h-48 rounded-full bg-white"></div>
            </div>
            <span class="text-6xl sm:text-7xl relative z-10">🎓</span>
        </div>
        @endif
        
        {{-- Contenu de l'en-tête --}}
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
                
                @if($formation->est_payante)
                    <span class="badge text-xs flex-shrink-0" 
                          style="background-color: rgba(16,185,129,0.12); color: #34D399;">
                        💰 {{ number_format($formation->prix, 0, ',', ' ') }} FCFA
                    </span>
                @else
                    <span class="badge text-xs flex-shrink-0" 
                          style="background-color: rgba(16,185,129,0.12); color: #34D399;">
                        🆓 Gratuit
                    </span>
                @endif
                
                {{-- Badge progression --}}
                @php
                    $niveauxTotal = $niveaux->count();
                    $niveauxValides = $niveaux->where('est_valide', true)->count();
                    $progression = $niveauxTotal > 0 ? round(($niveauxValides / $niveauxTotal) * 100) : 0;
                @endphp
                @if($niveauxTotal > 0)
                <span class="badge text-xs flex-shrink-0" 
                      style="background-color: rgba(139,92,246,0.12); color: #C084FC;">
                    📊 {{ $progression }}% complété
                </span>
                @endif
            </div>
            
            {{-- Titre --}}
            <h1 class="text-xl sm:text-2xl font-extrabold leading-tight" 
                style="color: var(--edc-text-primary);">
                📚 {{ $formation->titre }}
            </h1>

            {{-- Description --}}
            <div class="mt-4 rounded-xl p-4 transition-colors hover:bg-opacity-80"
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

            {{-- Info formation gratuite --}}
            @unless($formation->est_payante)
            <div class="mt-3 rounded-xl p-3 sm:p-4 flex items-start gap-2"
                style="background-color: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.25);">
                <span class="text-lg flex-shrink-0">ℹ️</span>
                <p class="text-xs sm:text-sm" style="color: var(--edc-accent-gold);">
                    Cette formation est gratuite : la réussite du QCM final ne donne pas droit à un certificat.
                </p>
            </div>
            @endunless
            
            {{-- Barre de progression globale (mobile) --}}
            @if($niveauxTotal > 0)
            <div class="mt-4 sm:hidden">
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-xs font-semibold" style="color: var(--edc-text-secondary);">
                        Progression globale
                    </span>
                    <span class="text-xs font-bold" style="color: var(--edc-primary-light);">
                        {{ $niveauxValides }}/{{ $niveauxTotal }} niveaux
                    </span>
                </div>
                <div class="w-full h-2 rounded-full overflow-hidden" 
                     style="background-color: var(--edc-bg-elevated);">
                    <div class="h-full rounded-full transition-all duration-500"
                        style="width: {{ $progression }}%; background: linear-gradient(90deg, #3B82F6, #8B5CF6);">
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Ressources générales --}}
    @if($ressources_generales->count())
    <div class="edc-card mb-6 p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base sm:text-lg font-bold flex items-center gap-2" 
                style="color: var(--edc-text-primary);">
                <span>📁</span> Ressources générales
            </h2>
            <span class="text-xs px-2 py-0.5 rounded-full" 
                  style="background-color: rgba(59,130,246,0.1); color: var(--edc-primary-light);">
                {{ $ressources_generales->count() }} fichier(s)
            </span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            @foreach($ressources_generales as $ressource)
                @include('client.partials.ressource-card', ['ressource' => $ressource])
            @endforeach
        </div>
    </div>
    @endif

    {{-- Ressources par niveau - Optimisé mobile --}}
    @forelse($niveaux as $niveau)
    <div class="edc-card mb-4 sm:mb-6 overflow-hidden transition-all duration-300" 
         style="{{ !$niveau->est_accessible ? 'opacity: 0.7;' : '' }}">
        
        {{-- En-tête du niveau --}}
        <button onclick="toggleNiveau(this)" 
                class="w-full px-4 sm:px-6 py-4 flex items-center justify-between gap-3 transition-colors hover:bg-white/[0.02]"
                style="background-color: {{ $niveau->est_accessible ? 'rgba(59,130,246,0.06)' : 'rgba(148,163,184,0.06)' }}; 
                       border-left: 4px solid {{ $niveau->est_accessible ? 'var(--edc-primary)' : '#94A3B8' }};">
            <div class="flex items-center gap-3 min-w-0">
                {{-- Numéro du niveau --}}
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0"
                    style="{{ $niveau->est_valide 
                        ? 'background: linear-gradient(135deg, #10B981, #059669); color: white;' 
                        : ($niveau->est_accessible 
                            ? 'background: linear-gradient(135deg, #3B82F6, #1D4ED8); color: white;' 
                            : 'background-color: var(--edc-bg-elevated); color: var(--edc-text-muted);') }}">
                    {{ $niveau->ordre }}
                </div>
                
                <div class="min-w-0">
                    <h2 class="text-sm sm:text-base font-bold flex items-center gap-2 truncate" 
                        style="color: var(--edc-text-primary);">
                        @if(!$niveau->est_accessible)
                            🔒
                        @elseif($niveau->est_valide)
                            ✅
                        @else
                            📂
                        @endif
                        {{ $niveau->nom }}
                    </h2>
                    @if($niveau->description)
                    <p class="text-xs mt-0.5 truncate" style="color: var(--edc-text-secondary);">
                        {{ $niveau->description }}
                    </p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                @if($niveau->est_valide)
                    <span class="badge text-[10px] sm:text-xs hidden sm:inline-flex" 
                          style="background-color: rgba(16,185,129,0.12); color: #34D399;">
                        ✅ Validé
                    </span>
                @elseif(!$niveau->est_accessible)
                    <span class="badge text-[10px] sm:text-xs hidden sm:inline-flex" 
                          style="background-color: rgba(148,163,184,0.12); color: #94A3B8;">
                        🔒 Verrouillé
                    </span>
                @else
                    <span class="badge text-[10px] sm:text-xs hidden sm:inline-flex" 
                          style="background-color: rgba(59,130,246,0.12); color: #60A5FA;">
                        📂 Ouvert
                    </span>
                @endif
                
                {{-- Flèche toggle --}}
                <svg class="w-5 h-5 transition-transform duration-300 transform niveau-chevron" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: var(--edc-text-muted);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>

        {{-- Contenu du niveau (collapsible) --}}
        <div class="niveau-content transition-all duration-300">
            <div class="p-4 sm:p-6">
                @if(!$niveau->est_accessible)
                    <div class="text-center py-6 sm:py-8" style="color: var(--edc-text-muted);">
                        <p class="text-3xl sm:text-4xl mb-3">🔒</p>
                        <p class="text-sm font-medium mb-1">Contenu verrouillé</p>
                        <p class="text-xs sm:text-sm">
                            Terminez et validez le niveau précédent pour débloquer ce contenu.
                        </p>
                        @if($niveau->ordre > 1)
                        <p class="text-xs mt-2" style="color: var(--edc-text-muted);">
                            Niveau requis : {{ $niveau->ordre - 1 }}
                        </p>
                        @endif
                    </div>
                @elseif($niveau->ressources->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @foreach($niveau->ressources as $ressource)
                            @include('client.partials.ressource-card', ['ressource' => $ressource])
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-center py-6" style="color: var(--edc-text-muted);">
                        📭 Aucune ressource disponible pour ce niveau.
                    </p>
                @endif

                {{-- Bouton QCM --}}
                @if($niveau->est_accessible && !$niveau->est_valide)
                    <div class="mt-5 text-center">
                        <a href="{{ route('client.qcms.index') }}" 
                           class="btn-primary btn-touch inline-flex items-center gap-2">
                            <span>📝</span>
                            <span>Passer le QCM de ce niveau</span>
                        </a>
                        <p class="text-xs mt-2" style="color: var(--edc-text-muted);">
                            Validez vos connaissances pour débloquer la suite
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="edc-card text-center py-12 sm:py-16 px-4" style="color: var(--edc-text-muted);">
        <p class="text-4xl sm:text-5xl mb-4">📭</p>
        <p class="font-semibold text-sm sm:text-base mb-2" style="color: var(--edc-text-secondary);">
            Aucun contenu disponible pour le moment
        </p>
        <p class="text-xs sm:text-sm">
            L'enseignant ajoutera bientôt des ressources pour cette formation.
        </p>
    </div>
    @endforelse
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

// Ouvrir le premier niveau accessible par défaut
document.addEventListener('DOMContentLoaded', function() {
    const firstAccessible = document.querySelector('.niveau-content');
    if (firstAccessible && !firstAccessible.closest('.edc-card').querySelector('[style*="opacity: 0.7"]')) {
        firstAccessible.style.maxHeight = firstAccessible.scrollHeight + 'px';
        const chevron = firstAccessible.closest('.edc-card').querySelector('.niveau-chevron');
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
</style>
@endsection
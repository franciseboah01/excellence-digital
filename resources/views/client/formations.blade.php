@extends('layouts.client')
@section('title', 'Mes Formations')

@section('content')
<div class="max-w-4xl mx-auto">
    
    {{-- HEADER - Optimisé mobile --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">
                📚 Mes Formations
            </h1>
            <p class="text-xs sm:text-sm mt-0.5" style="color: var(--edc-text-secondary);">
                {{ $inscriptions->count() }} formation(s)
            </p>
        </div>
        <a href="{{ route('client.formations.disponibles') }}" 
           class="btn-primary btn-touch w-full sm:w-auto justify-center text-sm">
            <span>🔍</span>
            <span>Voir les formations disponibles</span>
        </a>
    </div>

    {{-- LISTE DES FORMATIONS --}}
    @forelse($inscriptions as $inscription)
    <div class="edc-card mb-4 p-4 sm:p-5 transition-all duration-300 group">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            
            {{-- Infos formation --}}
            <div class="flex items-start gap-3 sm:gap-4 min-w-0 flex-1">
                {{-- Icône/Image --}}
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl flex items-center justify-center text-xl sm:text-2xl flex-shrink-0 overflow-hidden"
                     style="background: {{ $inscription->formation->image 
                         ? 'transparent' 
                         : 'linear-gradient(135deg, #1e3a8a, #3B82F6)' }};">
                    @if($inscription->formation->image)
                        <img src="{{ asset('storage/' . $inscription->formation->image) }}" 
                             alt="{{ $inscription->formation->titre }}"
                             class="w-full h-full object-cover"
                             loading="lazy">
                    @else
                        <span>🎓</span>
                    @endif
                </div>
                
                {{-- Détails --}}
                <div class="min-w-0 flex-1">
                    <h2 class="text-base sm:text-lg font-bold line-clamp-2" 
                        style="color: var(--edc-text-primary);">
                        {{ $inscription->formation->titre }}
                    </h2>
                    
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        {{-- Module --}}
                        <span class="text-xs sm:text-sm flex items-center gap-1" 
                              style="color: var(--edc-text-secondary);">
                            <span>{{ $inscription->formation->module->icone ?? '📚' }}</span>
                            <span class="truncate max-w-[120px] sm:max-w-[200px]">
                                {{ $inscription->formation->module->nom ?? 'Module' }}
                            </span>
                        </span>
                        
                        {{-- Statut --}}
                        <span class="badge text-xs flex-shrink-0"
                            style="background-color: {{ $inscription->statut == 'valide' ? 'rgba(16,185,129,0.12)' : 'rgba(245,158,11,0.12)' }}; 
                                   color: {{ $inscription->statut == 'valide' ? '#34D399' : '#F59E0B' }};">
                            {{ $inscription->statut == 'valide' ? '✅ Validé' : '⏳ En attente' }}
                        </span>
                        
                        {{-- Durée --}}
                        @if($inscription->formation->duree)
                        <span class="text-xs flex items-center gap-1 flex-shrink-0" 
                              style="color: var(--edc-text-muted);">
                            <span>⏱</span>
                            <span>{{ $inscription->formation->duree }}</span>
                        </span>
                        @endif
                    </div>
                    
                    {{-- Date d'inscription --}}
                    <p class="text-[10px] sm:text-xs mt-1.5" style="color: var(--edc-text-muted);">
                        📅 Inscrit le {{ $inscription->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            
            {{-- Actions --}}
            <div class="flex items-center gap-2 sm:flex-col sm:items-stretch flex-shrink-0">
                @if($inscription->statut == 'valide')
                    <a href="{{ route('client.formation.show', $inscription->formation->id) }}" 
                       class="btn-primary btn-touch text-sm inline-flex items-center justify-center gap-2 w-full sm:w-auto transition-transform duration-300 hover:scale-105">
                        <span>Accéder</span>
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                @else
                    {{-- Message d'attente --}}
                    <div class="text-center px-3 py-2 rounded-xl" 
                         style="background-color: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2);">
                        <p class="text-xs font-medium" style="color: var(--edc-accent-gold);">
                            ⏳ En attente de validation
                        </p>
                        <p class="text-[10px] mt-0.5" style="color: var(--edc-text-muted);">
                            Par l'administrateur
                        </p>
                    </div>
                @endif
            </div>
        </div>
        
        {{-- Barre de progression (si validé) --}}
        @if($inscription->statut == 'valide')
        <div class="mt-4 pt-3" style="border-top: 1px solid var(--edc-border);">
            <div class="flex justify-between items-center mb-1.5">
                <span class="text-[10px] sm:text-xs font-medium" style="color: var(--edc-text-secondary);">
                    📊 Progression
                </span>
                <span class="text-[10px] sm:text-xs font-bold" style="color: var(--edc-primary-light);">
                    {{ $inscription->progression ?? 0 }}%
                </span>
            </div>
            <div class="w-full h-1.5 sm:h-2 rounded-full overflow-hidden" 
                 style="background-color: var(--edc-bg-elevated);">
                <div class="h-full rounded-full transition-all duration-500 progress-bar"
                     style="width: {{ $inscription->progression ?? 0 }}%; 
                            background: linear-gradient(90deg, #3B82F6, #8B5CF6);">
                </div>
            </div>
        </div>
        @endif
    </div>
    @empty
    {{-- État vide - Optimisé --}}
    <div class="edc-card text-center py-12 sm:py-16 px-4" style="color: var(--edc-text-muted);">
        <p class="text-5xl sm:text-6xl mb-4">📭</p>
        <p class="font-semibold text-sm sm:text-base mb-2" style="color: var(--edc-text-secondary);">
            Vous n'êtes inscrit à aucune formation
        </p>
        <p class="text-xs sm:text-sm mb-6">
            Découvrez notre catalogue et commencez votre apprentissage
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('client.formations.disponibles') }}" 
               class="btn-primary btn-touch inline-flex items-center justify-center gap-2">
                <span>🔍</span>
                <span>Voir les formations disponibles</span>
            </a>
        </div>
    </div>
    @endforelse
    
    {{-- Message d'aide --}}
    @if($inscriptions->count() > 0)
    <div class="mt-6 text-center">
        <p class="text-xs" style="color: var(--edc-text-muted);">
            💡 Cliquez sur "Accéder" pour consulter le contenu de votre formation.
        </p>
    </div>
    @endif
</div>

{{-- Animation des barres de progression au chargement --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const targetWidth = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = targetWidth;
        }, 200);
    });
});
</script>
@endsection
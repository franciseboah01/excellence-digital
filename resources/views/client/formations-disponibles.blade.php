@extends('layouts.client')
@section('title', 'Formations disponibles')

@section('content')
<div class="max-w-5xl mx-auto">
    
    {{-- HEADER - Optimisé mobile --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">
                    🎓 Formations disponibles
                </h1>
                <p class="text-xs sm:text-sm mt-0.5" style="color: var(--edc-text-secondary);">
                    Découvrez notre catalogue et inscrivez-vous
                </p>
            </div>
            
            {{-- Retour --}}
            <a href="{{ route('client.formations') }}" 
               class="text-xs font-medium hover:underline flex-shrink-0 self-start sm:self-auto"
               style="color: var(--edc-primary-light);">
                ← Mes formations
            </a>
        </div>
        
        {{-- Compteur --}}
        <div class="flex items-center gap-2 mt-3">
            <span class="text-xs px-3 py-1 rounded-full" 
                  style="background-color: rgba(59,130,246,0.1); color: var(--edc-primary-light);">
                {{ $formations->count() }} formation(s) disponible(s)
            </span>
        </div>
    </div>

    {{-- GRILLE FORMATIONS - Optimisée mobile --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        @forelse($formations->sortByDesc('created_at') as $formation)
        <div class="edc-card overflow-hidden group transition-all duration-300 hover:shadow-glow">
            
            {{-- Image avec overlay --}}
            <div class="relative aspect-[16/10] sm:aspect-[4/3] overflow-hidden">
                @if($formation->image)
                <img src="{{ asset('storage/' . $formation->image) }}" 
                     alt="{{ $formation->titre }}"
                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                     loading="lazy">
                @else
                <div class="w-full h-full flex items-center justify-center relative"
                     style="background: linear-gradient(135deg, #1e3a8a, #3B82F6);">
                    <span class="text-5xl sm:text-6xl relative z-10">🎓</span>
                    {{-- Motif décoratif --}}
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full bg-white"></div>
                        <div class="absolute -bottom-4 -left-4 w-32 h-32 rounded-full bg-white"></div>
                    </div>
                </div>
                @endif
                
                {{-- Overlay gradient au hover --}}
                <div class="absolute inset-0 bg-gradient-to-t from-[#0B0F1A] via-transparent to-transparent opacity-0 group-hover:opacity-60 transition-opacity duration-300"></div>
                
                {{-- Badge prix en haut à droite --}}
                <div class="absolute top-3 right-3 z-10">
                    @if($formation->est_payante)
                        <span class="badge text-xs font-bold px-3 py-1.5 backdrop-blur-sm"
                              style="background-color: rgba(16,185,129,0.9); color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                            {{ number_format($formation->prix, 0, ',', ' ') }} FCFA
                        </span>
                    @else
                        <span class="badge text-xs font-bold px-3 py-1.5 backdrop-blur-sm"
                              style="background-color: rgba(16,185,129,0.9); color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                            🆓 Gratuit
                        </span>
                    @endif
                </div>
            </div>
            
            {{-- Contenu de la carte --}}
            <div class="p-4 sm:p-5">
                {{-- Module et durée --}}
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="badge text-[10px] sm:text-xs flex-shrink-0" 
                          style="background-color: rgba(59,130,246,0.12); color: #60A5FA;">
                        {{ $formation->module->icone ?? '📚' }} {{ $formation->module->nom ?? 'Module' }}
                    </span>
                    
                    @if($formation->duree)
                    <span class="text-[10px] sm:text-xs flex-shrink-0 flex items-center gap-1" 
                          style="color: var(--edc-text-muted);">
                        <span>⏱</span>
                        <span>{{ $formation->duree }}</span>
                    </span>
                    @endif
                </div>
                
                {{-- Titre --}}
                <h3 class="font-bold text-sm sm:text-base mt-2 line-clamp-2" 
                    style="color: var(--edc-text-primary);">
                    {{ $formation->titre }}
                </h3>
                
                {{-- Description --}}
                <p class="text-xs mt-2 line-clamp-2 sm:line-clamp-3" 
                   style="color: var(--edc-text-secondary);">
                    {{ $formation->description ? Str::limit($formation->description, 80) : 'Aucune description disponible.' }}
                </p>
                
                {{-- Info certification --}}
                @if($formation->est_payante)
                <div class="flex items-center gap-1.5 mt-3 text-[10px] sm:text-xs" 
                     style="color: var(--edc-accent-gold);">
                    <span>🏆</span>
                    <span>Certificat inclus</span>
                </div>
                @else
                <div class="flex items-center gap-1.5 mt-3 text-[10px] sm:text-xs" 
                     style="color: var(--edc-text-muted);">
                    <span>ℹ️</span>
                    <span>Sans certificat</span>
                </div>
                @endif
                
                {{-- Bouton d'inscription --}}
                <form method="POST" action="{{ route('client.formations.inscrire', $formation) }}" class="mt-4">
                    @csrf
                    <button type="submit" 
                            class="btn-primary btn-touch w-full text-sm font-bold group/btn transition-all duration-300"
                            onclick="return confirm('🎓 Vous allez vous inscrire à la formation :\n\n{{ addslashes($formation->titre) }}\n\nContinuer ?')">
                        <span class="flex items-center justify-center gap-2">
                            <span class="transition-transform duration-300 group-hover/btn:scale-125">➕</span>
                            <span>S'inscrire</span>
                        </span>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="edc-card text-center py-16 sm:py-20 px-4" style="color: var(--edc-text-muted);">
                <p class="text-5xl sm:text-6xl mb-4">🎓</p>
                <p class="font-semibold text-sm sm:text-base mb-2" style="color: var(--edc-text-secondary);">
                    Aucune formation disponible pour le moment
                </p>
                <p class="text-xs sm:text-sm mb-6">
                    Revenez bientôt, de nouvelles formations seront ajoutées !
                </p>
                <a href="{{ route('client.formations') }}" 
                   class="btn-tertiary btn-sm inline-flex items-center gap-2">
                    <span>←</span>
                    <span>Retour à mes formations</span>
                </a>
            </div>
        </div>
        @endforelse
    </div>
    
    {{-- Message d'aide en bas --}}
    @if($formations->count() > 0)
    <div class="mt-8 text-center">
        <p class="text-xs" style="color: var(--edc-text-muted);">
            💡 Vous pouvez vous inscrire à plusieurs formations simultanément.
        </p>
    </div>
    @endif
</div>
@endsection
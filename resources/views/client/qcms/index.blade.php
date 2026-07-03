@extends('layouts.client')
@section('title', 'Mes QCMs')

@section('content')
<div class="max-w-3xl mx-auto">
    
    {{-- HEADER - Optimisé mobile --}}
    <div class="mb-6 sm:mb-8 text-center">
        <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-3xl sm:text-4xl"
             style="background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(139,92,246,0.15)); 
                    border: 2px solid rgba(59,130,246,0.3);">
            📝
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">
            Mes QCMs
        </h1>
        <p class="text-xs sm:text-sm mt-1" style="color: var(--edc-text-secondary);">
            Testez vos connaissances et suivez votre progression
        </p>
    </div>

    {{-- STATISTIQUES - Cartes optimisées --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 mb-6">
        <div class="stat-card group cursor-help" style="border-left-color: var(--edc-primary);" 
             title="Nombre total de QCMs disponibles">
            <p class="stat-value text-xl sm:text-2xl">{{ $stats['total'] }}</p>
            <p class="stat-label text-[10px] sm:text-xs">📝 Total QCMs</p>
        </div>
        <div class="stat-card group cursor-help" style="border-left-color: var(--edc-secondary);" 
             title="QCMs réussis avec une note ≥ minimum">
            <p class="stat-value text-xl sm:text-2xl">{{ $stats['reussis'] }}</p>
            <p class="stat-label text-[10px] sm:text-xs">✅ Réussis</p>
        </div>
        <div class="stat-card group cursor-help" style="border-left-color: #F59E0B;" 
             title="QCMs déjà tentés mais pas encore réussis">
            <p class="stat-value text-xl sm:text-2xl">{{ $stats['en_cours'] }}</p>
            <p class="stat-label text-[10px] sm:text-xs">🔄 En cours</p>
        </div>
        <div class="stat-card group cursor-help" style="border-left-color: var(--edc-text-muted);" 
             title="QCMs que vous n'avez pas encore essayés">
            <p class="stat-value text-xl sm:text-2xl">{{ $stats['non_tentes'] }}</p>
            <p class="stat-label text-[10px] sm:text-xs">⏳ Non tentés</p>
        </div>
    </div>

    {{-- LISTE DES QCMs --}}
    <div class="space-y-4 sm:space-y-5">
        @forelse($qcms as $qcm)
        <div class="edc-card overflow-hidden transition-all duration-300 group"
             style="{{ $qcm->est_verrouille ? 'opacity: 0.6;' : '' }}">
            
            {{-- En-tête coloré --}}
            <div class="px-4 sm:px-5 py-3 flex flex-wrap items-center gap-2"
                 style="background-color: {{ $qcm->est_verrouille ? 'rgba(148,163,184,0.08)' : 'rgba(59,130,246,0.06)' }}; 
                        border-bottom: 1px solid var(--edc-border);">
                
                {{-- Badges formation/niveau --}}
                <div class="flex flex-wrap items-center gap-2 flex-1 min-w-0">
                    <span class="badge text-[10px] sm:text-xs flex-shrink-0" 
                          style="background-color: rgba(59,130,246,0.12); color: #60A5FA;">
                        🎓 {{ $qcm->formation->titre }}
                    </span>
                    
                    @if($qcm->niveau)
                        <span class="badge text-[10px] sm:text-xs flex-shrink-0" 
                              style="background-color: rgba(148,163,184,0.10); color: #94A3B8;">
                            📂 {{ $qcm->niveau->nom }}
                        </span>
                    @else
                        <span class="badge text-[10px] sm:text-xs flex-shrink-0" 
                              style="background-color: rgba(251,191,36,0.12); color: #FBBF24;">
                            🏁 QCM Final
                        </span>
                    @endif
                </div>
                
                {{-- Statut --}}
                <div class="flex-shrink-0">
                    @if($qcm->est_verrouille)
                        <span class="badge text-[10px] sm:text-xs flex items-center gap-1"
                              style="background-color: rgba(148,163,184,0.15); color: #94A3B8;">
                            <span>🔒</span><span>Verrouillé</span>
                        </span>
                    @elseif($qcm->deja_reussi)
                        <span class="badge text-[10px] sm:text-xs flex items-center gap-1 font-bold"
                              style="background-color: rgba(16,185,129,0.15); color: #34D399;">
                            <span>{{ $qcm->niveau ? '✅' : '🏆' }}</span>
                            <span>{{ $qcm->niveau ? 'Niveau validé' : 'Réussi !' }}</span>
                        </span>
                    @elseif($qcm->tentatives_faites > 0)
                        <span class="badge text-[10px] sm:text-xs flex items-center gap-1"
                              style="background-color: rgba(245,158,11,0.15); color: #FBBF24;">
                            <span>🔄</span>
                            <span>{{ $qcm->tentatives_faites }}/{{ $qcm->tentatives_max }}</span>
                        </span>
                    @else
                        <span class="badge text-[10px] sm:text-xs flex items-center gap-1"
                              style="background-color: rgba(148,163,184,0.10); color: #94A3B8;">
                            <span>⏳</span><span>Non tenté</span>
                        </span>
                    @endif
                </div>
            </div>
            
            {{-- Corps --}}
            <div class="p-4 sm:p-5">
                {{-- Titre et description --}}
                <h3 class="font-bold text-sm sm:text-base mb-1.5" style="color: var(--edc-text-primary);">
                    {{ $qcm->titre }}
                </h3>
                @if($qcm->description)
                <p class="text-xs sm:text-sm mb-3 line-clamp-2" style="color: var(--edc-text-secondary);">
                    {{ $qcm->description }}
                </p>
                @endif

                {{-- Message verrouillage --}}
                @if($qcm->est_verrouille)
                <div class="rounded-xl p-3 sm:p-4 mt-2 flex items-start gap-2"
                     style="background-color: rgba(148,163,184,0.06); border: 1px solid rgba(148,163,184,0.2);">
                    <span class="text-lg flex-shrink-0">🔒</span>
                    <p class="text-xs" style="color: var(--edc-text-muted);">
                        {{ $qcm->niveau ? 'Validez le niveau précédent pour débloquer ce QCM.' : 'Validez tous les niveaux de la formation pour débloquer ce QCM final.' }}
                    </p>
                </div>
                @endif

                {{-- Statistiques QCM - Grille responsive --}}
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-1.5 sm:gap-2 mt-3">
                    <div class="rounded-xl p-2 sm:p-3 text-center transition-colors hover:bg-white/[0.02]"
                         style="background-color: rgba(59,130,246,0.04); border: 1px solid rgba(59,130,246,0.1);">
                        <p class="text-[10px] sm:text-xs" style="color: var(--edc-text-muted);">📋 Questions</p>
                        <p class="font-bold text-sm sm:text-base mt-0.5" style="color: var(--edc-primary-light);">{{ $qcm->questions_count }}</p>
                    </div>
                    <div class="rounded-xl p-2 sm:p-3 text-center transition-colors hover:bg-white/[0.02]"
                         style="background-color: rgba(16,185,129,0.04); border: 1px solid rgba(16,185,129,0.1);">
                        <p class="text-[10px] sm:text-xs" style="color: var(--edc-text-muted);">🎯 Note min.</p>
                        <p class="font-bold text-sm sm:text-base mt-0.5" style="color: var(--edc-secondary);">{{ $qcm->note_minimale }}/{{ $qcm->bareme }}</p>
                    </div>
                    <div class="rounded-xl p-2 sm:p-3 text-center transition-colors hover:bg-white/[0.02]"
                         style="background-color: rgba(245,158,11,0.04); border: 1px solid rgba(245,158,11,0.1);">
                        <p class="text-[10px] sm:text-xs" style="color: var(--edc-text-muted);">⏱ Durée/Q</p>
                        <p class="font-bold text-sm sm:text-base mt-0.5" style="color: var(--edc-accent-gold);">{{ $qcm->duree_par_question }}s</p>
                    </div>
                    <div class="rounded-xl p-2 sm:p-3 text-center transition-colors hover:bg-white/[0.02]"
                         style="background-color: rgba(139,92,246,0.04); border: 1px solid rgba(139,92,246,0.1);">
                        <p class="text-[10px] sm:text-xs" style="color: var(--edc-text-muted);">🏅 Meilleure</p>
                        <p class="font-bold text-sm sm:text-base mt-0.5" style="color: #8B5CF6;">{{ $qcm->meilleure_note ?: '—' }}/{{ $qcm->bareme }}</p>
                    </div>
                    <div class="rounded-xl p-2 sm:p-3 text-center transition-colors hover:bg-white/[0.02]"
                         style="background-color: rgba(236,72,153,0.04); border: 1px solid rgba(236,72,153,0.1);">
                        <p class="text-[10px] sm:text-xs" style="color: var(--edc-text-muted);">🔄 Tentatives</p>
                        <p class="font-bold text-sm sm:text-base mt-0.5" style="color: #EC4899;">{{ $qcm->tentatives_faites }}/{{ $qcm->tentatives_max }}</p>
                    </div>
                </div>

                {{-- Barre de progression --}}
                @if($qcm->meilleure_note)
                <div class="mt-4">
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-[10px] sm:text-xs font-medium" style="color: var(--edc-text-muted);">
                            📊 Progression
                        </span>
                        <span class="text-[10px] sm:text-xs font-bold" 
                              style="color: {{ $qcm->deja_reussi ? '#34D399' : '#60A5FA' }};">
                            {{ $qcm->meilleure_note }}/{{ $qcm->bareme }}
                            @if($qcm->deja_reussi)
                                <span class="ml-1">✅</span>
                            @endif
                        </span>
                    </div>
                    <div class="w-full h-2 sm:h-2.5 rounded-full overflow-hidden" 
                         style="background-color: var(--edc-bg-elevated);">
                        <div class="h-full rounded-full transition-all duration-500 progress-bar"
                             style="width: 0%; {{ $qcm->deja_reussi ? 'background: linear-gradient(90deg, #10B981, #059669);' : 'background: linear-gradient(90deg, #3B82F6, #8B5CF6);' }}"
                             data-width="{{ $qcm->bareme > 0 ? ($qcm->meilleure_note / $qcm->bareme) * 100 : 0 }}">
                        </div>
                    </div>
                </div>
                @endif

                {{-- Historique des tentatives --}}
                @if($qcm->tentatives_faites > 0)
                <details class="mt-4 group/details">
                    <summary class="text-xs cursor-pointer flex items-center gap-1.5 hover:underline"
                             style="color: var(--edc-text-muted);">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 group-open/details:rotate-90" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span>📋 Historique ({{ $qcm->tentatives_faites }} tentative{{ $qcm->tentatives_faites > 1 ? 's' : '' }})</span>
                    </summary>
                    <div class="mt-3 space-y-1.5">
                        @foreach($qcm->mes_sessions as $session)
                        <div class="flex items-center justify-between text-xs p-2.5 sm:p-3 rounded-xl transition-colors hover:bg-white/[0.02]"
                             style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
                            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                <span class="font-mono text-[10px] sm:text-xs flex-shrink-0" 
                                      style="color: var(--edc-text-muted);">
                                    #{{ $session->tentative }}
                                </span>
                                <span class="text-[10px] sm:text-xs truncate" 
                                      style="color: var(--edc-text-muted);">
                                    {{ $session->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                                <span class="font-bold text-xs sm:text-sm" 
                                      style="color: {{ $session->reussi ? '#34D399' : '#F87171' }};">
                                    {{ $session->note }}/{{ $qcm->bareme }}
                                </span>
                                <span>{{ $session->reussi ? '✅' : '❌' }}</span>
                                <a href="{{ route('client.qcms.resultat', $session) }}"
                                   class="text-[10px] sm:text-xs font-medium px-2 py-1 rounded-lg transition-colors hover:underline"
                                   style="color: var(--edc-primary-light); background-color: rgba(59,130,246,0.08);">
                                    Voir
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </details>
                @endif
            </div>

            {{-- Pied : Bouton d'action --}}
            <div class="px-4 sm:px-5 py-3 sm:py-4 flex items-center justify-center"
                 style="border-top: 1px solid var(--edc-border); background-color: var(--edc-bg-base);">
                
                @if($qcm->deja_reussi && $qcm->certificat)
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 w-full">
                        <p class="text-sm font-semibold flex items-center gap-1.5" style="color: var(--edc-secondary);">
                            <span>🎓</span><span>Certificat obtenu !</span>
                        </p>
                        <a href="{{ route('client.certificats.index') }}"
                           class="btn-touch inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-300 hover:scale-105"
                           style="background: rgba(16,185,129,0.1); color: #10B981; border: 1px solid rgba(16,185,129,0.3);">
                            <span>Voir mon certificat</span>
                            <span>→</span>
                        </a>
                    </div>
                @elseif($qcm->deja_reussi && $qcm->niveau)
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 w-full">
                        <p class="text-sm font-semibold flex items-center gap-1.5" style="color: var(--edc-primary-light);">
                            <span>✅</span><span>Niveau validé !</span>
                        </p>
                        <a href="{{ route('client.ressources', $qcm->formation) }}"
                           class="btn-touch inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-300 hover:scale-105"
                           style="background: rgba(59,130,246,0.1); color: #60A5FA; border: 1px solid rgba(59,130,246,0.3);">
                            <span>Continuer la formation</span>
                            <span>→</span>
                        </a>
                    </div>
                @elseif($qcm->deja_reussi)
                    <p class="text-center text-sm font-semibold flex items-center justify-center gap-1.5" 
                       style="color: var(--edc-accent-gold);">
                        <span>🎉</span>
                        <span>Formation réussie ! (gratuite — pas de certificat)</span>
                    </p>
                @elseif($qcm->est_verrouille)
                    <p class="text-center text-sm flex items-center gap-1.5" style="color: var(--edc-text-muted);">
                        <span>🔒</span><span>QCM verrouillé pour le moment</span>
                    </p>
                @elseif($qcm->peut_repasser)
                    <a href="{{ route('client.qcms.demarrer', $qcm) }}"
                       class="btn-primary btn-touch w-full sm:w-auto text-sm font-bold flex items-center justify-center gap-2 transition-all duration-300 hover:scale-105">
                        <span>{{ $qcm->tentatives_faites > 0 ? '🔄' : '▶️' }}</span>
                        <span>{{ $qcm->tentatives_faites > 0 ? 'Repasser le QCM' : 'Commencer le QCM' }}</span>
                    </a>
                @else
                    <div class="text-center">
                        <p class="text-sm font-semibold flex items-center justify-center gap-1.5" 
                           style="color: var(--edc-danger);">
                            <span>❌</span>
                            <span>Tentatives épuisées</span>
                        </p>
                        @if($qcm->meilleure_note)
                        <p class="text-xs mt-1" style="color: var(--edc-text-muted);">
                            Meilleure note : {{ $qcm->meilleure_note }}/20 (min. {{ $qcm->note_minimale }}/20)
                        </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @empty
        <div class="edc-card text-center py-16 sm:py-20 px-4" style="color: var(--edc-text-muted);">
            <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-3xl sm:text-4xl"
                 style="background-color: var(--edc-bg-base);">
                📝
            </div>
            <p class="font-semibold text-sm sm:text-base mb-2" style="color: var(--edc-text-secondary);">
                Aucun QCM disponible
            </p>
            <p class="text-xs sm:text-sm">
                Les enseignants ajouteront des QCMs pour vos formations prochainement.
            </p>
        </div>
        @endforelse
    </div>
    
    {{-- Message d'aide --}}
    @if(count($qcms) > 0)
    <div class="mt-6 text-center">
        <p class="text-xs" style="color: var(--edc-text-muted);">
            💡 Réussissez les QCMs de niveaux pour débloquer le QCM final et obtenir votre certificat.
        </p>
    </div>
    @endif
</div>

{{-- Animation des barres de progression --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const targetWidth = bar.getAttribute('data-width');
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = targetWidth + '%';
        }, 300);
    });
});
</script>
@endsection
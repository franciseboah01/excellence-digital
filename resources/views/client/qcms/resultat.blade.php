@extends('layouts.client')
@section('title', 'Résultat du QCM')

@section('content')
<div class="max-w-3xl mx-auto py-4 sm:py-8">

    {{-- HEADER RÉSULTAT - Design amélioré --}}
    <div class="rounded-2xl sm:rounded-3xl p-6 sm:p-8 text-center mb-6 text-white relative overflow-hidden"
        style="{{ $session->reussi
            ? 'background: linear-gradient(135deg, #059669, #10B981, #34D399);'
            : 'background: linear-gradient(135deg, #B91C1C, #EF4444, #F87171);' }}">
        
        {{-- Confettis décoratifs --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-4 left-4 w-16 h-16 rounded-full bg-white"></div>
            <div class="absolute top-8 right-8 w-12 h-12 rounded-full bg-white"></div>
            <div class="absolute bottom-4 left-1/3 w-20 h-20 rounded-full bg-white"></div>
            <div class="absolute bottom-8 right-4 w-14 h-14 rounded-full bg-white"></div>
        </div>
        
        <div class="relative z-10">
            {{-- Icône animée --}}
            <div class="text-5xl sm:text-6xl mb-4 animate-bounce-slow">
                {{ $session->reussi ? '🎓' : '😔' }}
            </div>
            
            <h1 class="text-2xl sm:text-3xl font-extrabold mb-2">
                {{ $session->reussi ? 'Félicitations !' : 'Essayez encore !' }}
            </h1>
            
            {{-- Note --}}
            <p class="text-lg sm:text-xl font-bold mb-1 opacity-90">
                Note : <span class="text-3xl sm:text-4xl">{{ $session->note }}</span>/{{ $session->qcm->bareme ?? 20 }}
            </p>
            
            {{-- Détails --}}
            <div class="flex flex-wrap justify-center gap-3 sm:gap-4 mt-3 text-sm" style="opacity: 0.85;">
                <span class="flex items-center gap-1">
                    <span>⭐</span>
                    <span>{{ $session->score }}/{{ $session->score_max }} pts</span>
                </span>
                <span class="hidden sm:inline">•</span>
                <span class="flex items-center gap-1">
                    <span>🔄</span>
                    <span>Tentative {{ $session->tentative }}/{{ $session->qcm->tentatives_max }}</span>
                </span>
            </div>

            {{-- Barre de progression --}}
            <div class="mt-5 max-w-xs sm:max-w-sm mx-auto">
                <div class="flex justify-between text-xs mb-1.5 opacity-75">
                    <span>0</span>
                    <span>{{ $session->qcm->note_minimale }} (min)</span>
                    <span>{{ $session->qcm->bareme ?? 20 }}</span>
                </div>
                <div class="rounded-full h-3 sm:h-4 relative" style="background-color: rgba(255,255,255,0.25);">
                    <div id="scoreBar" 
                         class="h-full rounded-full bg-white transition-all duration-1000 ease-out relative"
                         style="width: 0%;">
                        <div class="absolute -right-1.5 -top-1.5 w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-white shadow-lg flex items-center justify-center"
                             style="color: {{ $session->reussi ? '#059669' : '#B91C1C' }};">
                            <span class="text-xs font-bold">{{ $session->note }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Message --}}
            <div class="mt-4 rounded-xl p-3 text-sm" style="background-color: rgba(255,255,255,0.1);">
                @if($session->reussi)
                    <p>✅ Note minimale : {{ $session->qcm->note_minimale }}/{{ $session->qcm->bareme ?? 20 }} — <strong>Objectif atteint !</strong></p>
                @else
                    <p>
                        Note minimale requise : <strong>{{ $session->qcm->note_minimale }}/{{ $session->qcm->bareme ?? 20 }}</strong>
                        @if($session->tentative < $session->qcm->tentatives_max)
                            — Il vous reste <strong>{{ $session->qcm->tentatives_max - $session->tentative }} tentative(s)</strong>.
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- SUITE APRÈS RÉUSSITE — 3 cas distincts --}}
    @if($session->reussi && $session->certificat)
        {{-- Cas 1 : QCM final réussi, formation payante → certificat --}}
        <div class="rounded-2xl p-5 sm:p-6 mb-6 text-center relative overflow-hidden"
            style="background-color: rgba(251,191,36,0.08); border: 2px solid var(--edc-accent-gold);">
            <div class="absolute top-0 right-0 w-24 h-24 opacity-5" 
                 style="background: radial-gradient(circle, #FBBF24, transparent);"></div>
            <div class="relative z-10">
                <p class="text-4xl sm:text-5xl mb-3">🏆</p>
                <h2 class="text-lg sm:text-xl font-bold mb-2" style="color: var(--edc-accent-gold);">
                    Certificat disponible !
                </h2>
                <p class="text-xs sm:text-sm mb-1 font-mono" style="color: var(--edc-text-secondary);">
                    N° {{ $session->certificat->numero_certificat }}
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-3 mt-4">
                    <a href="{{ route('client.certificats.telecharger', ['certificat' => $session->certificat, 'format' => 'pdf']) }}"
                       class="btn-touch inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-bold transition-all duration-300 hover:scale-105"
                       style="background: linear-gradient(135deg, #FBBF24, #F59E0B); color: #1a1a1a;">
                        <span>📄</span><span>Télécharger le certificat PDF</span>
                    </a>
                </div>
            </div>
        </div>
    @elseif($session->reussi && $session->qcm->niveau_id !== null)
        {{-- Cas 2 : QCM de niveau réussi --}}
        <div class="rounded-2xl p-5 sm:p-6 mb-6 text-center"
            style="background-color: rgba(59,130,246,0.08); border: 2px solid var(--edc-primary);">
            <p class="text-4xl sm:text-5xl mb-3">✅</p>
            <h2 class="text-lg sm:text-xl font-bold mb-2" style="color: var(--edc-primary-light);">
                Niveau validé !
            </h2>
            <p class="text-xs sm:text-sm mb-4" style="color: var(--edc-text-secondary);">
                Vous pouvez maintenant accéder au niveau suivant de la formation.
            </p>
            <a href="{{ route('client.ressources', $session->qcm->formation) }}" 
               class="btn-primary btn-touch inline-flex items-center gap-2">
                <span>📚</span><span>Continuer vers le niveau suivant</span>
            </a>
        </div>
    @elseif($session->reussi)
        {{-- Cas 3 : QCM final réussi mais formation gratuite --}}
        <div class="rounded-2xl p-5 sm:p-6 mb-6 text-center"
            style="background-color: rgba(245,158,11,0.08); border: 2px solid var(--edc-accent-gold);">
            <p class="text-4xl sm:text-5xl mb-3">🎉</p>
            <h2 class="text-lg sm:text-xl font-bold mb-2" style="color: var(--edc-accent-gold);">
                Formation réussie !
            </h2>
            <p class="text-xs sm:text-sm" style="color: var(--edc-text-secondary);">
                Cette formation étant gratuite, elle ne donne pas droit à un certificat.
            </p>
        </div>
    @endif

    {{-- CORRECTION DÉTAILLÉE - Design amélioré --}}
    <div class="edc-card p-4 sm:p-6 mb-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base sm:text-lg font-bold flex items-center gap-2" style="color: var(--edc-text-primary);">
                <span>📋</span> Correction détaillée
            </h2>
            
            {{-- Stats rapides --}}
            @php
                $totalQuestions = $session->qcm->questions->count();
                $correctCount = 0;
                foreach($session->qcm->questions as $question) {
                    $detail = $session->reponses_donnees[$question->id] ?? null;
                    if ($detail && ($detail['correct'] ?? false)) $correctCount++;
                }
            @endphp
            <span class="text-xs px-3 py-1 rounded-full" 
                  style="background-color: {{ $correctCount >= $totalQuestions/2 ? 'rgba(16,185,129,0.12)' : 'rgba(239,68,68,0.12)' }}; 
                         color: {{ $correctCount >= $totalQuestions/2 ? '#34D399' : '#F87171' }};">
                {{ $correctCount }}/{{ $totalQuestions }} correcte(s)
            </span>
        </div>

        @foreach($session->qcm->questions as $index => $question)
        @php
            $detail   = $session->reponses_donnees[$question->id] ?? null;
            $correct  = $detail['correct'] ?? false;
            $donnees  = collect($detail['donnees'] ?? []);
            $correctes= collect($detail['correctes'] ?? []);
        @endphp
        
        <details class="mb-3 group/details" {{ $index === 0 ? 'open' : '' }}>
            <summary class="rounded-xl p-3 sm:p-4 cursor-pointer transition-all duration-200 list-none"
                style="{{ $correct
                    ? 'background-color: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.2);'
                    : 'background-color: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2);' }}">
                
                <div class="flex justify-between items-start gap-3">
                    <div class="flex items-start gap-2 sm:gap-3 min-w-0">
                        <span class="text-lg sm:text-xl flex-shrink-0">
                            {{ $correct ? '✅' : '❌' }}
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold text-sm sm:text-base line-clamp-2" style="color: var(--edc-text-primary);">
                                Q{{ $index + 1 }}. {{ $question->question }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-xs sm:text-sm font-bold"
                            style="color: {{ $correct ? 'var(--edc-secondary)' : 'var(--edc-danger)' }};">
                            {{ $correct ? '+' . $question->points : '0' }}/{{ $question->points }} pt
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-200 group-open/details:rotate-180" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: var(--edc-text-muted);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </summary>
            
            <div class="mt-2 space-y-1.5 pl-7 sm:pl-10">
                @foreach($question->reponses as $reponse)
                @php
                    $estDonnee   = $donnees->contains($reponse->id);
                    $estCorrecte = $reponse->est_correcte;
                @endphp
                <div class="flex items-center gap-2 sm:gap-3 text-xs sm:text-sm px-3 py-2.5 sm:py-3 rounded-xl transition-all duration-200"
                    style="@if($estCorrecte && $estDonnee)
                        background-color: rgba(16,185,129,0.12); color: #34D399; border: 1px solid rgba(16,185,129,0.2);
                    @elseif($estCorrecte && !$estDonnee)
                        background-color: rgba(16,185,129,0.06); color: #34D399; border: 1px dashed rgba(16,185,129,0.3);
                    @elseif(!$estCorrecte && $estDonnee)
                        background-color: rgba(239,68,68,0.1); color: #F87171; border: 1px solid rgba(239,68,68,0.2);
                    @else
                        background-color: var(--edc-bg-base); color: var(--edc-text-muted);
                    @endif">
                    
                    {{-- Icône --}}
                    <span class="flex-shrink-0 text-base sm:text-lg">
                        @if($estCorrecte && $estDonnee) ✅
                        @elseif($estCorrecte && !$estDonnee) ⭕
                        @elseif(!$estCorrecte && $estDonnee) ❌
                        @else ○
                        @endif
                    </span>
                    
                    {{-- Texte --}}
                    <span class="flex-1">{{ $reponse->contenu }}</span>
                    
                    {{-- Libellé --}}
                    @if($estCorrecte && !$estDonnee)
                    <span class="text-[10px] sm:text-xs flex-shrink-0 italic px-2 py-0.5 rounded-full"
                          style="background-color: rgba(16,185,129,0.1);">
                        Manquée
                    </span>
                    @elseif(!$estCorrecte && $estDonnee)
                    <span class="text-[10px] sm:text-xs flex-shrink-0 italic px-2 py-0.5 rounded-full"
                          style="background-color: rgba(239,68,68,0.1);">
                        Incorrecte
                    </span>
                    @endif
                </div>
                @endforeach
            </div>
        </details>
        @endforeach
    </div>

    {{-- ACTIONS - Design amélioré --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('client.qcms.index') }}" 
           class="btn-tertiary btn-touch flex-1 text-center text-sm flex items-center justify-center gap-1.5">
            <span>←</span><span>Retour aux QCMs</span>
        </a>
        @if(!$session->reussi && $session->tentative < $session->qcm->tentatives_max)
        <a href="{{ route('client.qcms.demarrer', $session->qcm) }}" 
           class="btn-primary btn-touch flex-1 text-center text-sm flex items-center justify-center gap-1.5">
            <span>🔄</span><span>Repasser le QCM</span>
        </a>
        @endif
    </div>
    
    {{-- Message d'encouragement --}}
    @if(!$session->reussi)
    <div class="mt-6 text-center">
        <p class="text-xs" style="color: var(--edc-text-muted);">
            💡 Révisez bien les ressources de la formation avant de retenter le QCM.
        </p>
    </div>
    @endif
</div>

{{-- Animation de la barre de progression --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scoreBar = document.getElementById('scoreBar');
    if (scoreBar) {
        const bareme = {{ $session->qcm->bareme ?? 20 }};
        const note = {{ $session->note }};
        const percentage = bareme > 0 ? (note / bareme) * 100 : 0;
        
        setTimeout(() => {
            scoreBar.style.width = percentage + '%';
        }, 500);
    }
});
</script>
@endsection
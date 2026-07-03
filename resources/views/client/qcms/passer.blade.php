@extends('layouts.client')
@section('title', 'QCM — ' . $qcm->titre)

@section('content')
<div class="max-w-3xl mx-auto" id="qcm-app"
    data-questions="{{ $qcm->questions->count() }}"
    data-duree="{{ $qcm->duree_par_question }}">

    {{-- HEADER QCM - Optimisé mobile --}}
    <div class="edc-card p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
            <div class="min-w-0 flex-1">
                <h1 class="text-lg sm:text-xl font-extrabold line-clamp-2" style="color: var(--edc-text-primary);">
                    📝 {{ $qcm->titre }}
                </h1>
                <p class="text-xs sm:text-sm mt-1 line-clamp-1" style="color: var(--edc-text-secondary);">
                    {{ $qcm->formation->titre }}
                    @if($qcm->niveau) — {{ $qcm->niveau->nom }} @endif
                </p>
                
                {{-- Badge type QCM --}}
                <div class="mt-2">
                    @if($qcm->niveau)
                        <span class="badge text-[10px] sm:text-xs inline-flex items-center gap-1"
                              style="background-color: rgba(59,130,246,0.12); color: #60A5FA;">
                            📂 QCM de niveau — Validation du niveau
                        </span>
                    @else
                        <span class="badge text-[10px] sm:text-xs inline-flex items-center gap-1"
                              style="background-color: rgba(251,191,36,0.15); color: #FBBF24;">
                            🏁 QCM final {{ $qcm->formation->est_payante ? '— Certificat possible' : '— Sans certificat' }}
                        </span>
                    @endif
                </div>
            </div>
            
            {{-- Infos droite --}}
            <div class="text-right flex-shrink-0 bg-[var(--edc-bg-base)] rounded-xl p-3">
                <p class="text-[10px] sm:text-xs flex items-center gap-1 justify-end" 
                   style="color: var(--edc-text-muted);">
                    <span>🔄</span>
                    <span>Tentative {{ $tentativesFaites + 1 }}/{{ $qcm->tentatives_max }}</span>
                </p>
                <p class="text-[10px] sm:text-xs mt-1 flex items-center gap-1 justify-end" 
                   style="color: var(--edc-text-muted);">
                    <span>🎯</span>
                    <span>Note min : <strong style="color: var(--edc-primary-light);">{{ $qcm->note_minimale }}/{{ $qcm->bareme ?? 20 }}</strong></span>
                </p>
            </div>
        </div>

        {{-- Barre de progression --}}
        <div class="mt-4">
            <div class="flex justify-between text-xs mb-1.5" style="color: var(--edc-text-muted);">
                <span>📊 Progression</span>
                <span id="progressLabel">Question 1/{{ $qcm->questions->count() }}</span>
            </div>
            <div class="w-full rounded-full h-2 sm:h-2.5 overflow-hidden" 
                 style="background-color: var(--edc-bg-elevated);">
                <div id="progressBar"
                    class="h-full rounded-full transition-all duration-500"
                    style="width: {{ (1 / $qcm->questions->count()) * 100 }}%; background: linear-gradient(90deg, #3B82F6, #8B5CF6);">
                </div>
            </div>
            
            {{-- Indicateurs de questions (points) --}}
            <div class="flex justify-center gap-1.5 sm:gap-2 mt-3 flex-wrap">
                @foreach($qcm->questions as $i => $question)
                <button type="button" 
                        onclick="allerQuestion({{ $i }})"
                        class="qcm-dot w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full transition-all duration-300 flex-shrink-0"
                        data-index="{{ $i }}"
                        style="background-color: {{ $i === 0 ? 'var(--edc-primary)' : 'var(--edc-bg-elevated)' }};"
                        title="Question {{ $i + 1 }}">
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- TIMER - Design amélioré --}}
    <div class="rounded-2xl p-4 sm:p-5 mb-4 sm:mb-6 flex flex-col sm:flex-row items-center justify-between gap-3 relative overflow-hidden"
        style="background: linear-gradient(135deg, #1e3a8a, #1d4ed8);">
        {{-- Fond décoratif --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full bg-white"></div>
            <div class="absolute -bottom-4 -left-4 w-32 h-32 rounded-full bg-white"></div>
        </div>
        
        <div class="flex items-center gap-3 relative z-10">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center text-xl sm:text-2xl bg-white/10 backdrop-blur-sm">
                ⏱
            </div>
            <div>
                <p class="text-[10px] sm:text-xs font-medium" style="color: rgba(255,255,255,0.6);">
                    Temps restant
                </p>
                <p id="timerDisplay" class="text-2xl sm:text-3xl font-mono font-bold transition-colors duration-300" 
                   style="color: #fff;">
                    {{ floor($qcm->duree_par_question / 60) }}:{{ str_pad($qcm->duree_par_question % 60, 2, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>
        
        <div class="text-center sm:text-right relative z-10">
            <p class="text-[10px] sm:text-xs font-medium" style="color: rgba(255,255,255,0.6);">
                Score actuel
            </p>
            <p id="scoreActuel" class="text-xl sm:text-2xl font-bold flex items-center gap-1.5 justify-center sm:justify-end" 
               style="color: #FBBF24;">
                <span>⭐</span><span>0 pt</span>
            </p>
        </div>
    </div>

    {{-- FORMULAIRE QCM --}}
    <form id="qcmForm" method="POST" action="{{ route('client.qcms.soumettre', $qcm) }}">
        @csrf
        <input type="hidden" name="duree_passee" id="dureePassed" value="0">

        @foreach($qcm->questions as $index => $question)
        <div class="question-slide animate-fade-in-up {{ $index > 0 ? 'hidden' : '' }}"
            data-index="{{ $index }}"
            data-points="{{ $question->points }}">

            <div class="edc-card p-4 sm:p-6 mb-4">
                {{-- En-tête question --}}
                <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                    <span class="badge text-[10px] sm:text-xs flex items-center gap-1"
                          style="background-color: rgba(59,130,246,0.12); color: #60A5FA;">
                        <span>📋</span>
                        <span>Question {{ $index + 1 }}/{{ $qcm->questions->count() }}</span>
                    </span>
                    <span class="badge text-[10px] sm:text-xs flex items-center gap-1"
                          style="background-color: rgba(16,185,129,0.12); color: #34D399;">
                        <span>🏆</span>
                        <span>{{ $question->points }} pt{{ $question->points > 1 ? 's' : '' }}</span>
                    </span>
                </div>

                {{-- Texte de la question --}}
                <p class="text-base sm:text-lg font-semibold mb-5 sm:mb-6 leading-relaxed" 
                   style="color: var(--edc-text-primary);">
                    {{ $question->question }}
                </p>

                {{-- Réponses - Design amélioré --}}
                <div class="space-y-2.5 sm:space-y-3">
                    @foreach($question->reponses as $reponse)
                    <label class="reponse-label flex items-start gap-3 p-3 sm:p-4 rounded-xl cursor-pointer transition-all duration-200 group"
                        style="border: 2px solid var(--edc-border);"
                        data-question="{{ $question->id }}">
                        
                        {{-- Checkbox personnalisée --}}
                        <div class="relative flex-shrink-0 mt-0.5">
                            <input type="checkbox"
                                name="reponses[{{ $question->id }}][]"
                                value="{{ $reponse->id }}"
                                class="reponse-checkbox sr-only"
                                onchange="updateReponseStyle(this)">
                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg border-2 flex items-center justify-center transition-all duration-200 group-hover:border-blue-400"
                                 style="border-color: var(--edc-border); background-color: var(--edc-bg-base);">
                                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-white opacity-0 transition-opacity duration-200 check-icon" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        
                        {{-- Texte de la réponse --}}
                        <span class="text-sm sm:text-base leading-relaxed flex-1 transition-colors duration-200"
                              style="color: var(--edc-text-secondary);">
                            {{ $reponse->contenu }}
                        </span>
                    </label>
                    @endforeach
                </div>

                {{-- Indication réponses multiples --}}
                @php $nbCorrectes = $question->reponsesCorrectes->count(); @endphp
                <div class="mt-3 flex items-center gap-1.5">
                    <span class="text-xs">💡</span>
                    <span class="text-[10px] sm:text-xs" style="color: var(--edc-text-muted);">
                        {{ $nbCorrectes > 1 ? "{$nbCorrectes} réponses correctes — Cochez toutes les bonnes réponses" : "Une seule réponse correcte" }}
                    </span>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="flex justify-between items-center gap-3">
                @if($index > 0)
                <button type="button" onclick="allerQuestion({{ $index - 1 }})" 
                        class="btn-tertiary btn-touch text-sm flex items-center gap-1.5">
                    <span>←</span><span>Précédent</span>
                </button>
                @else
                <div></div>
                @endif

                @if($index < $qcm->questions->count() - 1)
                <button type="button" onclick="allerQuestion({{ $index + 1 }})" 
                        class="btn-primary btn-touch text-sm flex items-center gap-1.5">
                    <span>Suivant</span><span>→</span>
                </button>
                @else
                <button type="button" onclick="soumettreQcm()" 
                        class="btn-success btn-touch text-sm flex items-center gap-1.5 animate-glow">
                    <span>✅</span><span>Terminer le QCM</span>
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </form>
    
    {{-- Bouton retour (abandon) --}}
    <div class="text-center mt-6">
        <a href="{{ route('client.qcms.index') }}" 
           class="text-xs hover:underline" 
           style="color: var(--edc-text-muted);"
           onclick="return confirm('⚠️ Quitter le QCM ? Vos réponses seront perdues.')">
            ← Abandonner et revenir aux QCMs
        </a>
    </div>
</div>

{{-- MODAL CONFIRMATION SOUMISSION --}}
<div id="modalSoumission"
    class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background-color: rgba(0,0,0,0.8);">
    <div class="edc-card p-6 sm:p-8 max-w-sm w-full text-center animate-fade-in-up">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center text-3xl"
             style="background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(5,150,105,0.15));">
            📤
        </div>
        <h3 class="text-lg sm:text-xl font-bold mb-2" style="color: var(--edc-text-primary);">
            Soumettre le QCM ?
        </h3>
        <p class="text-xs sm:text-sm mb-6" style="color: var(--edc-text-secondary);">
            Vous ne pourrez plus modifier vos réponses après la soumission. Assurez-vous d'avoir répondu à toutes les questions.
        </p>
        
        {{-- Résumé rapide --}}
        <div class="rounded-xl p-3 mb-4 text-xs" style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
            <p style="color: var(--edc-text-muted);">
                <span id="resumeQuestions">?</span> question(s) répondue(s) sur {{ $qcm->questions->count() }}
            </p>
        </div>
        
        <div class="flex gap-3">
            <button onclick="document.getElementById('modalSoumission').classList.add('hidden')" 
                    class="btn-tertiary btn-touch flex-1 text-sm">
                Annuler
            </button>
            <button onclick="document.getElementById('qcmForm').submit()" 
                    class="btn-success btn-touch flex-1 text-sm flex items-center justify-center gap-1.5">
                <span>✅</span><span>Confirmer</span>
            </button>
        </div>
    </div>
</div>

{{-- MODAL TEMPS ÉCOULÉ --}}
<div id="modalTemps"
    class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background-color: rgba(0,0,0,0.8);">
    <div class="edc-card p-6 sm:p-8 max-w-sm w-full text-center animate-fade-in-up">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center text-3xl"
             style="background: linear-gradient(135deg, rgba(239,68,68,0.15), rgba(185,28,28,0.15));">
            ⏰
        </div>
        <h3 class="text-lg sm:text-xl font-bold mb-2" style="color: var(--edc-danger);">
            Temps écoulé !
        </h3>
        <p class="text-xs sm:text-sm" style="color: var(--edc-text-secondary);">
            Le temps imparti pour cette question est dépassé. Passage automatique à la suite...
        </p>
        <div class="mt-4 w-full h-1.5 rounded-full overflow-hidden" style="background-color: var(--edc-bg-elevated);">
            <div class="h-full bg-red-500 rounded-full animate-pulse" style="width: 100%;"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const TOTAL_QUESTIONS   = parseInt(document.getElementById('qcm-app').dataset.questions);
const DUREE_PAR_QUESTION = parseInt(document.getElementById('qcm-app').dataset.duree);

let questionActuelle = 0;
let tempsRestant     = DUREE_PAR_QUESTION;
let intervalTimer    = null;
let dureeTotale      = 0;
let scoreTotal       = 0;

// Initialiser
demarrerTimer();
updateDots();

// Avertissement avant de quitter
window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = 'Attention ! Quitter la page mettra fin à votre QCM.';
});

function demarrerTimer() {
    clearInterval(intervalTimer);
    tempsRestant = DUREE_PAR_QUESTION;
    mettreAJourAffichageTimer();

    intervalTimer = setInterval(function() {
        tempsRestant--;
        dureeTotale++;
        document.getElementById('dureePassed').value = dureeTotale;
        mettreAJourAffichageTimer();

        if (tempsRestant <= 0) {
            clearInterval(intervalTimer);
            tempsEcoule();
        }

        // Changement de couleur du timer
        const timerEl = document.getElementById('timerDisplay');
        if (tempsRestant <= 10) {
            timerEl.style.color = '#EF4444';
            timerEl.style.animation = 'pulse-soft 0.5s ease-in-out infinite';
        } else if (tempsRestant <= 30) {
            timerEl.style.color = '#FBBF24';
            timerEl.style.animation = 'none';
        } else {
            timerEl.style.color = '#ffffff';
            timerEl.style.animation = 'none';
        }
    }, 1000);
}

function mettreAJourAffichageTimer() {
    const min = Math.floor(tempsRestant / 60);
    const sec = String(tempsRestant % 60).padStart(2, '0');
    document.getElementById('timerDisplay').textContent = `${min}:${sec}`;
}

function tempsEcoule() {
    const modal = document.getElementById('modalTemps');
    modal.classList.remove('hidden');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        if (questionActuelle < TOTAL_QUESTIONS - 1) {
            allerQuestion(questionActuelle + 1);
        } else {
            soumettreQcm();
        }
    }, 2500);
}

function allerQuestion(index) {
    // Cacher la question actuelle
    document.querySelectorAll('.question-slide')[questionActuelle].classList.add('hidden');
    
    // Afficher la nouvelle question
    questionActuelle = index;
    const nouvelleSlide = document.querySelectorAll('.question-slide')[questionActuelle];
    nouvelleSlide.classList.remove('hidden');
    nouvelleSlide.classList.add('animate-fade-in-up');

    // Mettre à jour la progression
    const pct = ((questionActuelle + 1) / TOTAL_QUESTIONS) * 100;
    document.getElementById('progressBar').style.width = pct + '%';
    document.getElementById('progressLabel').textContent =
        `Question ${questionActuelle + 1}/${TOTAL_QUESTIONS}`;
    
    updateDots();

    // Réinitialiser le timer
    demarrerTimer();
    
    // Scroll en haut
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateDots() {
    document.querySelectorAll('.qcm-dot').forEach((dot, i) => {
        const slide = document.querySelectorAll('.question-slide')[i];
        const checkboxes = slide.querySelectorAll('.reponse-checkbox');
        const hasAnswer = Array.from(checkboxes).some(cb => cb.checked);
        
        if (i === questionActuelle) {
            dot.style.backgroundColor = 'var(--edc-primary)';
            dot.style.transform = 'scale(1.3)';
            dot.style.boxShadow = '0 0 8px rgba(59,130,246,0.5)';
        } else if (hasAnswer) {
            dot.style.backgroundColor = 'var(--edc-secondary)';
            dot.style.transform = 'scale(1)';
            dot.style.boxShadow = 'none';
        } else {
            dot.style.backgroundColor = 'var(--edc-bg-elevated)';
            dot.style.transform = 'scale(1)';
            dot.style.boxShadow = 'none';
        }
    });
}

function updateReponseStyle(checkbox) {
    const label = checkbox.closest('.reponse-label');
    const checkIcon = label.querySelector('.check-icon');
    const checkDiv = label.querySelector('.w-5.h-5, .w-6.h-6');
    
    if (checkbox.checked) {
        label.style.borderColor = '#3B82F6';
        label.style.backgroundColor = 'rgba(59,130,246,0.08)';
        label.querySelector('span:last-child').style.color = '#60A5FA';
        checkDiv.style.backgroundColor = '#3B82F6';
        checkDiv.style.borderColor = '#3B82F6';
        checkIcon.style.opacity = '1';
    } else {
        label.style.borderColor = 'var(--edc-border)';
        label.style.backgroundColor = 'transparent';
        label.querySelector('span:last-child').style.color = 'var(--edc-text-secondary)';
        checkDiv.style.backgroundColor = 'var(--edc-bg-base)';
        checkDiv.style.borderColor = 'var(--edc-border)';
        checkIcon.style.opacity = '0';
    }
    
    updateScore();
    updateDots();
}

function updateScore() {
    let total = 0;
    document.querySelectorAll('.question-slide').forEach(slide => {
        const points = parseInt(slide.dataset.points);
        const checkboxes = slide.querySelectorAll('.reponse-checkbox');
        const hasAnswer = Array.from(checkboxes).some(cb => cb.checked);
        // On compte les points max possibles pour les questions répondues
        if (hasAnswer) total += points;
    });
    scoreTotal = total;
    document.getElementById('scoreActuel').innerHTML = `<span>⭐</span><span>${total} pt${total > 1 ? 's' : ''}</span>`;
}

function soumettreQcm() {
    clearInterval(intervalTimer);
    window.removeEventListener('beforeunload', () => {});
    
    // Compter les questions répondues
    let repondues = 0;
    document.querySelectorAll('.question-slide').forEach(slide => {
        const checkboxes = slide.querySelectorAll('.reponse-checkbox');
        if (Array.from(checkboxes).some(cb => cb.checked)) repondues++;
    });
    document.getElementById('resumeQuestions').textContent = repondues;
    
    document.getElementById('modalSoumission').classList.remove('hidden');
}

// Ajouter l'animation pulse-soft si pas déjà dans le CSS
if (!document.querySelector('#pulse-soft-style')) {
    const style = document.createElement('style');
    style.id = 'pulse-soft-style';
    style.textContent = `
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        .question-slide.animate-fade-in-up {
            animation: fadeInUp 0.3s ease forwards;
        }
    `;
    document.head.appendChild(style);
}
</script>
@endpush
@endsection
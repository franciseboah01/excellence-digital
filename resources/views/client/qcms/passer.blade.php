@extends('layouts.client')
@section('title', 'QCM — ' . $qcm->titre)

@section('content')
<div class="max-w-3xl mx-auto" id="qcm-app"
    data-questions="{{ $qcm->questions->count() }}"
    data-duree="{{ $qcm->duree_par_question }}">

    {{-- HEADER QCM --}}
    <div class="edc-card p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">📝 {{ $qcm->titre }}</h1>
                <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">{{ $qcm->formation->titre }}
                    @if($qcm->niveau) — {{ $qcm->niveau->nom }} @endif
                </p>
                {{-- ✅ Rappel du type de QCM et de ce qu'il donne (ou non) --}}
                @if($qcm->niveau)
                    <span class="badge text-xs mt-1 inline-block" style="background-color: rgba(59,130,246,0.12); color: #60A5FA;">
                        📂 QCM de niveau — validation requise pour débloquer le niveau suivant
                    </span>
                @else
                    <span class="badge text-xs mt-1 inline-block" style="background-color: rgba(251,191,36,0.15); color: #FBBF24;">
                        🏁 QCM final {{ $qcm->formation->est_payante ? '— donne droit au certificat' : '— formation gratuite, pas de certificat' }}
                    </span>
                @endif
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-xs" style="color: var(--edc-text-muted);">Tentative {{ $tentativesFaites + 1 }} / {{ $qcm->tentatives_max }}</p>
                {{-- ✅ CORRECTION : $certificat n'existe pas dans cette vue (jamais transmis
                     par le contrôleur). $qcm->bareme est déjà disponible directement. --}}
                <p class="text-xs mt-1" style="color: var(--edc-text-muted);">Note minimale : <strong style="color: var(--edc-primary-light);">{{ $qcm->note_minimale }}/{{ $qcm->bareme ?? 20 }}</strong></p>
            </div>
        </div>

        {{-- Barre de progression --}}
        <div class="mt-4">
            <div class="flex justify-between text-xs mb-1" style="color: var(--edc-text-muted);">
                <span>Progression</span>
                <span id="progressLabel">Question 1 / {{ $qcm->questions->count() }}</span>
            </div>
            <div class="w-full rounded-full h-2" style="background-color: var(--edc-bg-elevated);">
                <div id="progressBar"
                    class="h-2 rounded-full transition-all duration-500"
                    style="width: {{ (1 / $qcm->questions->count()) * 100 }}%; background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                </div>
            </div>
        </div>
    </div>

    {{-- TIMER GLOBAL --}}
    <div class="rounded-2xl p-4 mb-6 flex flex-col sm:flex-row items-center justify-between gap-3"
        style="background: linear-gradient(135deg, #1e3a8a, #1d4ed8);">
        <div class="flex items-center space-x-3">
            <span class="text-2xl">⏱</span>
            <div>
                <p class="text-xs" style="color: rgba(255,255,255,0.6);">Temps pour cette question</p>
                <p id="timerDisplay" class="text-3xl font-mono font-bold" style="color: #fff;">
                    {{ floor($qcm->duree_par_question / 60) }}:{{ str_pad($qcm->duree_par_question % 60, 2, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-xs" style="color: rgba(255,255,255,0.6);">Score actuel</p>
            <p id="scoreActuel" class="text-2xl font-bold" style="color: #FBBF24;">0 pts</p>
        </div>
    </div>

    {{-- FORMULAIRE QCM --}}
    <form id="qcmForm" method="POST" action="{{ route('client.qcms.soumettre', $qcm) }}">
        @csrf
        <input type="hidden" name="duree_passee" id="dureePassed" value="0">

        @foreach($qcm->questions as $index => $question)
        <div class="question-slide {{ $index > 0 ? 'hidden' : '' }}"
            data-index="{{ $index }}"
            data-points="{{ $question->points }}">

            <div class="edc-card p-6 mb-4">
                {{-- Numéro et points --}}
                <div class="flex justify-between items-center mb-4">
                    <span class="badge badge-blue">
                        Question {{ $index + 1 }} / {{ $qcm->questions->count() }}
                    </span>
                    <span class="badge badge-green">
                        🏆 {{ $question->points }} point{{ $question->points > 1 ? 's' : '' }}
                    </span>
                </div>

                {{-- Texte de la question --}}
                <p class="text-lg font-semibold mb-6 leading-relaxed" style="color: var(--edc-text-primary);">
                    {{ $question->question }}
                </p>

                {{-- Réponses --}}
                <div class="space-y-3">
                    @foreach($question->reponses as $reponse)
                    <label class="reponse-label flex items-start space-x-3 p-4 rounded-xl cursor-pointer transition"
                        style="border: 2px solid var(--edc-border);"
                        data-question="{{ $question->id }}"
                        onmouseover="if(!this.querySelector('input').checked){this.style.borderColor='var(--edc-primary)';this.style.backgroundColor='rgba(59,130,246,0.05)';}"
                        onmouseout="if(!this.querySelector('input').checked){this.style.borderColor='var(--edc-border)';this.style.backgroundColor='transparent';}">
                        <input type="checkbox"
                            name="reponses[{{ $question->id }}][]"
                            value="{{ $reponse->id }}"
                            class="reponse-checkbox mt-0.5 w-5 h-5 rounded flex-shrink-0"
                            style="accent-color: #3B82F6;"
                            onchange="updateReponseStyle(this)">
                        <span class="leading-relaxed" style="color: var(--edc-text-secondary);">{{ $reponse->contenu }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Indication --}}
                @php $nbCorrectes = $question->reponsesCorrectes->count(); @endphp
                <p class="text-xs mt-3" style="color: var(--edc-text-muted);">
                    💡 {{ $nbCorrectes > 1 ? "{$nbCorrectes} bonnes réponses possibles" : "1 seule bonne réponse" }}
                </p>
            </div>

            {{-- Navigation --}}
            <div class="flex justify-between items-center">
                @if($index > 0)
                <button type="button" onclick="allerQuestion({{ $index - 1 }})" class="btn-tertiary">
                    ← Précédent
                </button>
                @else
                <div></div>
                @endif

                @if($index < $qcm->questions->count() - 1)
                <button type="button" onclick="allerQuestion({{ $index + 1 }})" class="btn-primary">
                    Suivant →
                </button>
                @else
                <button type="button" onclick="soumettreQcm()" class="btn-success">
                    ✅ Terminer le QCM
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </form>
</div>

{{-- MODAL CONFIRMATION --}}
<div id="modalSoumission"
    class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background-color: rgba(0,0,0,0.75);">
    <div class="edc-card p-8 max-w-md w-full text-center">
        <p class="text-5xl mb-4">📤</p>
        <h3 class="text-xl font-bold mb-2" style="color: var(--edc-text-primary);">Soumettre le QCM ?</h3>
        <p class="text-sm mb-6" style="color: var(--edc-text-secondary);">
            Vous ne pourrez plus modifier vos réponses après la soumission.
        </p>
        <div class="flex space-x-3 justify-center">
            <button onclick="document.getElementById('modalSoumission').classList.add('hidden')" class="btn-tertiary">
                Continuer
            </button>
            <button onclick="document.getElementById('qcmForm').submit()" class="btn-success">
                ✅ Confirmer
            </button>
        </div>
    </div>
</div>

{{-- MODAL TEMPS ÉCOULÉ --}}
<div id="modalTemps"
    class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background-color: rgba(0,0,0,0.75);">
    <div class="edc-card p-8 max-w-md w-full text-center">
        <p class="text-5xl mb-4">⏰</p>
        <h3 class="text-xl font-bold mb-2" style="color: var(--edc-danger);">Temps écoulé !</h3>
        <p class="text-sm" style="color: var(--edc-text-secondary);">
            Le temps pour cette question est écoulé. Passage à la question suivante...
        </p>
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

demarrerTimer();

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

        const timerEl = document.getElementById('timerDisplay');
        if (tempsRestant <= 10) {
            timerEl.style.color = '#EF4444';
        } else {
            timerEl.style.color = '#ffffff';
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
    }, 2000);
}

function allerQuestion(index) {
    document.querySelectorAll('.question-slide')[questionActuelle].classList.add('hidden');
    questionActuelle = index;
    document.querySelectorAll('.question-slide')[questionActuelle].classList.remove('hidden');

    const pct = ((questionActuelle + 1) / TOTAL_QUESTIONS) * 100;
    document.getElementById('progressBar').style.width = pct + '%';
    document.getElementById('progressLabel').textContent =
        `Question ${questionActuelle + 1} / ${TOTAL_QUESTIONS}`;

    demarrerTimer();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateReponseStyle(checkbox) {
    const label = checkbox.closest('.reponse-label');
    if (checkbox.checked) {
        label.style.borderColor = '#3B82F6';
        label.style.backgroundColor = 'rgba(59,130,246,0.08)';
    } else {
        label.style.borderColor = 'var(--edc-border)';
        label.style.backgroundColor = 'transparent';
    }
}

function soumettreQcm() {
    clearInterval(intervalTimer);
    window.removeEventListener('beforeunload', () => {});
    document.getElementById('modalSoumission').classList.remove('hidden');
}
</script>
@endpush
@endsection
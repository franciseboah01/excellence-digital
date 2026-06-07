@extends('layouts.client')
@section('title', 'QCM — ' . $qcm->titre)

@section('content')
<div class="max-w-3xl mx-auto" id="qcm-app"
    data-questions="{{ $qcm->questions->count() }}"
    data-duree="{{ $qcm->duree_par_question }}">

    {{-- HEADER QCM --}}
    <div class="bg-white rounded-2xl shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-blue-900">📝 {{ $qcm->titre }}</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $qcm->formation->titre }}
                    @if($qcm->niveau) — {{ $qcm->niveau->nom }} @endif
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400">Tentative {{ $tentativesFaites + 1 }} / {{ $qcm->tentatives_max }}</p>
                <p class="text-xs text-gray-400 mt-1">Note minimale : <strong class="text-blue-800">{{ $qcm->note_minimale }}/20</strong></p>
            </div>
        </div>

        {{-- Barre de progression globale --}}
        <div class="mt-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Progression</span>
                <span id="progressLabel">Question 1 / {{ $qcm->questions->count() }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progressBar"
                    class="bg-blue-600 h-2 rounded-full transition-all duration-500"
                    style="width: {{ (1 / $qcm->questions->count()) * 100 }}%">
                </div>
            </div>
        </div>
    </div>

    {{-- TIMER GLOBAL --}}
    <div class="bg-blue-900 text-white rounded-2xl p-4 mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <span class="text-2xl">⏱</span>
            <div>
                <p class="text-xs text-blue-300">Temps pour cette question</p>
                <p id="timerDisplay" class="text-3xl font-mono font-bold">
                    {{ floor($qcm->duree_par_question / 60) }}:{{ str_pad($qcm->duree_par_question % 60, 2, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-xs text-blue-300">Score actuel</p>
            <p id="scoreActuel" class="text-2xl font-bold">0 pts</p>
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

            <div class="bg-white rounded-2xl shadow p-6 mb-4">
                {{-- Numéro et points --}}
                <div class="flex justify-between items-center mb-4">
                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">
                        Question {{ $index + 1 }} / {{ $qcm->questions->count() }}
                    </span>
                    <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                        🏆 {{ $question->points }} point{{ $question->points > 1 ? 's' : '' }}
                    </span>
                </div>

                {{-- Texte de la question --}}
                <p class="text-lg font-semibold text-gray-800 mb-6 leading-relaxed">
                    {{ $question->question }}
                </p>

                {{-- Réponses --}}
                <div class="space-y-3">
                    @foreach($question->reponses as $reponse)
                    <label class="reponse-label flex items-start space-x-3 p-4 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition"
                        data-question="{{ $question->id }}">
                        <input type="checkbox"
                            name="reponses[{{ $question->id }}][]"
                            value="{{ $reponse->id }}"
                            class="reponse-checkbox mt-0.5 w-5 h-5 text-blue-600 rounded flex-shrink-0"
                            onchange="updateReponseStyle(this)">
                        <span class="text-gray-700 leading-relaxed">{{ $reponse->contenu }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Indication nombre de bonnes réponses --}}
                @php $nbCorrectes = $question->reponsesCorrectes->count(); @endphp
                <p class="text-xs text-gray-400 mt-3">
                    💡 {{ $nbCorrectes > 1 ? "{$nbCorrectes} bonnes réponses possibles" : "1 seule bonne réponse" }}
                </p>
            </div>

            {{-- Navigation --}}
            <div class="flex justify-between items-center">
                @if($index > 0)
                <button type="button" onclick="allerQuestion({{ $index - 1 }})"
                    class="bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-300 transition">
                    ← Précédent
                </button>
                @else
                <div></div>
                @endif

                @if($index < $qcm->questions->count() - 1)
                <button type="button" onclick="allerQuestion({{ $index + 1 }})"
                    class="bg-blue-800 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-900 transition">
                    Suivant →
                </button>
                @else
                <button type="button" onclick="soumettreQcm()"
                    class="bg-green-700 text-white px-8 py-3 rounded-xl font-bold hover:bg-green-800 transition">
                    ✅ Terminer le QCM
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </form>
</div>

{{-- MODAL CONFIRMATION SOUMISSION --}}
<div id="modalSoumission"
    class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full text-center">
        <p class="text-5xl mb-4">📤</p>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Soumettre le QCM ?</h3>
        <p class="text-gray-500 mb-6 text-sm">
            Vous ne pourrez plus modifier vos réponses après la soumission.
        </p>
        <div class="flex space-x-3 justify-center">
            <button onclick="document.getElementById('modalSoumission').classList.add('hidden')"
                class="bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-300 transition">
                Continuer
            </button>
            <button onclick="document.getElementById('qcmForm').submit()"
                class="bg-green-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-green-800 transition">
                ✅ Confirmer
            </button>
        </div>
    </div>
</div>

{{-- MODAL TEMPS ÉCOULÉ --}}
<div id="modalTemps"
    class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full text-center">
        <p class="text-5xl mb-4">⏰</p>
        <h3 class="text-xl font-bold text-red-700 mb-2">Temps écoulé !</h3>
        <p class="text-gray-500 mb-6 text-sm">
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
let scoreActuel      = 0;

// Démarrer le timer dès le chargement
demarrerTimer();

// Empêcher de quitter la page
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

        // Alerte rouge quand < 10 secondes
        const timerEl = document.getElementById('timerDisplay');
        if (tempsRestant <= 10) {
            timerEl.classList.add('text-red-400');
        } else {
            timerEl.classList.remove('text-red-400');
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
    // Cacher question actuelle
    document.querySelectorAll('.question-slide')[questionActuelle]
        .classList.add('hidden');

    // Afficher nouvelle question
    questionActuelle = index;
    document.querySelectorAll('.question-slide')[questionActuelle]
        .classList.remove('hidden');

    // Mettre à jour la barre de progression
    const pct = ((questionActuelle + 1) / TOTAL_QUESTIONS) * 100;
    document.getElementById('progressBar').style.width = pct + '%';
    document.getElementById('progressLabel').textContent =
        `Question ${questionActuelle + 1} / ${TOTAL_QUESTIONS}`;

    // Redémarrer le timer
    demarrerTimer();

    // Scroll en haut
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateReponseStyle(checkbox) {
    const label = checkbox.closest('.reponse-label');
    if (checkbox.checked) {
        label.classList.add('border-blue-600', 'bg-blue-50');
        label.classList.remove('border-gray-200');
    } else {
        label.classList.remove('border-blue-600', 'bg-blue-50');
        label.classList.add('border-gray-200');
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
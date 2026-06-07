@extends('layouts.enseignant')
@section('title', 'Questions — ' . $qcm->titre)

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="{{ route('enseignant.qcms.index') }}" class="text-blue-600 hover:underline text-sm">
            ← Retour aux QCMs
        </a>
        <h1 class="text-2xl font-bold text-blue-900 mt-1">📝 {{ $qcm->titre }}</h1>
        <p class="text-gray-500 text-sm mt-0.5">
            🎓 {{ $qcm->formation->titre }}
            @if($qcm->niveau) — {{ $qcm->niveau->nom }} @endif
            • ⏱ {{ $qcm->duree_par_question }}s/question
            • 🎯 {{ $qcm->note_minimale }}/20
        </p>
    </div>
    <div class="flex space-x-3">
        <form method="POST" action="{{ route('enseignant.qcms.toggle', $qcm) }}">
            @csrf
            <button type="submit"
                class="px-4 py-2 rounded-lg text-sm font-medium transition
                {{ $qcm->actif ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                {{ $qcm->actif ? '⏸️ Désactiver' : '▶️ Activer le QCM' }}
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- FORMULAIRE AJOUT QUESTION --}}
    @if($qcm->questions->count() < 10)
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold text-blue-900 mb-1">
            ➕ Ajouter une question
        </h2>
        <p class="text-xs text-gray-400 mb-4">
            {{ $qcm->questions->count() }}/10 questions ajoutées
        </p>

        <form method="POST" action="{{ route('enseignant.qcms.questions.store', $qcm) }}"
            id="formQuestion">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Question *
                </label>
                <textarea name="question" rows="3" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Écrivez la question ici..."></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Points *
                </label>
                <select name="points"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="2" selected>2 points</option>
                    <option value="1">1 point</option>
                    <option value="3">3 points</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Propositions de réponses *
                    <span class="text-xs text-gray-400 font-normal">
                        (2 à 4 propositions, cochez la ou les bonnes)
                    </span>
                </label>

                @for($i = 0; $i < 4; $i++)
                <div class="flex items-center space-x-2 mb-2">
                    <input type="checkbox" name="correctes[]" value="{{ $i }}"
                        class="w-4 h-4 text-green-600 rounded"
                        title="Cocher si bonne réponse">
                    <input type="text" name="reponses[]"
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Proposition {{ $i + 1 }}{{ $i < 2 ? ' *' : ' (optionnel)' }}"
                        {{ $i < 2 ? 'required' : '' }}>
                </div>
                @endfor

                <p class="text-xs text-gray-400 mt-1">
                    ✅ = bonne réponse | Cases vides = ignorées
                </p>
            </div>

            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition">
                ➕ Ajouter la question
            </button>
        </form>
    </div>
    @else
    <div class="bg-green-50 border border-green-300 rounded-xl p-6 text-center">
        <p class="text-4xl mb-3">✅</p>
        <p class="font-bold text-green-800">10 questions atteintes !</p>
        <p class="text-sm text-green-600 mt-1">Le QCM est complet.</p>
        @if(!$qcm->actif)
        <form method="POST" action="{{ route('enseignant.qcms.toggle', $qcm) }}" class="mt-4">
            @csrf
            <button type="submit"
                class="bg-green-700 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-800 transition">
                ▶️ Activer le QCM maintenant
            </button>
        </form>
        @endif
    </div>
    @endif

    {{-- LISTE DES QUESTIONS --}}
    <div>
        <h2 class="text-lg font-bold text-blue-900 mb-4">
            📋 Questions ({{ $qcm->questions->count() }}/10)
        </h2>

        @forelse($qcm->questions as $index => $question)
        <div class="bg-white rounded-xl shadow p-4 mb-3 border-l-4 border-blue-700">
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-7 h-7 bg-blue-800 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                        {{ $index + 1 }}
                    </span>
                    <p class="font-medium text-gray-800 text-sm">{{ $question->question }}</p>
                </div>
                <div class="flex items-center space-x-2 ml-3 flex-shrink-0">
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">
                        {{ $question->points }} pt{{ $question->points > 1 ? 's' : '' }}
                    </span>
                    <form method="POST"
                        action="{{ route('enseignant.qcms.questions.destroy', [$qcm, $question]) }}"
                        onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs">🗑️</button>
                    </form>
                </div>
            </div>

            <div class="space-y-1 ml-9">
                @foreach($question->reponses as $reponse)
                <div class="flex items-center space-x-2 text-xs">
                    <span class="{{ $reponse->est_correcte ? 'text-green-600' : 'text-gray-400' }}">
                        {{ $reponse->est_correcte ? '✅' : '○' }}
                    </span>
                    <span class="{{ $reponse->est_correcte ? 'font-semibold text-green-700' : 'text-gray-500' }}">
                        {{ $reponse->contenu }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="bg-gray-50 rounded-xl p-8 text-center text-gray-400">
            <p class="text-3xl mb-2">📝</p>
            <p class="text-sm">Aucune question ajoutée.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
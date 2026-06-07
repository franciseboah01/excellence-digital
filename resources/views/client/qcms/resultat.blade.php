@extends('layouts.client')
@section('title', 'Résultat du QCM')

@section('content')
<div class="max-w-3xl mx-auto py-8">

    {{-- HEADER RÉSULTAT --}}
    <div class="rounded-2xl shadow p-8 text-center mb-6
        {{ $session->reussi ? 'bg-gradient-to-br from-green-700 to-green-500' : 'bg-gradient-to-br from-red-700 to-red-500' }}
        text-white">

        <p class="text-6xl mb-4">{{ $session->reussi ? '🎓' : '😔' }}</p>
        <h1 class="text-3xl font-extrabold mb-2">
            {{ $session->reussi ? 'Félicitations !' : 'Essayez encore !' }}
        </h1>
        <p class="text-xl font-bold mb-1">
            Note : {{ $session->note }}/20
        </p>
        <p class="opacity-80 text-sm">
            Score : {{ $session->score }} / {{ $session->score_max }} points •
            Tentative {{ $session->tentative }}
        </p>

        {{-- Barre de score --}}
        <div class="mt-5 bg-white bg-opacity-30 rounded-full h-4 max-w-sm mx-auto">
            <div class="h-4 rounded-full bg-white transition-all duration-1000"
                style="width: {{ ($session->note / 20) * 100 }}%">
            </div>
        </div>

        @if($session->reussi)
        <p class="mt-4 text-green-100 text-sm">
            ✅ Note minimale requise : {{ $session->qcm->note_minimale }}/20 — Objectif atteint !
        </p>
        @else
        <p class="mt-4 text-red-100 text-sm">
            Note minimale requise : {{ $session->qcm->note_minimale }}/20
            @if($session->tentative < $session->qcm->tentatives_max)
            — Il vous reste {{ $session->qcm->tentatives_max - $session->tentative }} tentative(s).
            @endif
        </p>
        @endif
    </div>

    {{-- CERTIFICAT SI RÉUSSI --}}
    @if($session->reussi && $session->certificat)
    <div class="bg-yellow-50 border-2 border-yellow-400 rounded-2xl p-6 mb-6 text-center">
        <p class="text-4xl mb-3">🏆</p>
        <h2 class="text-xl font-bold text-yellow-800 mb-2">Certificat disponible !</h2>
        <p class="text-yellow-700 text-sm mb-4">
            N° {{ $session->certificat->numero_certificat }}
        </p>
        <div class="flex justify-center space-x-3">
            <a href="{{ route('certificats.telecharger', $session->certificat) }}"
                class="bg-yellow-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-yellow-700 transition">
                📄 Télécharger le certificat PDF
            </a>
            <a href="{{ route('certificats.apercu', $session->certificat) }}" target="_blank"
                class="bg-white border-2 border-yellow-600 text-yellow-700 px-6 py-3 rounded-xl font-bold hover:bg-yellow-50 transition">
                👁 Aperçu
            </a>
        </div>
    </div>
    @endif

    {{-- CORRECTION DÉTAILLÉE --}}
    <div class="bg-white rounded-2xl shadow p-6 mb-6">
        <h2 class="text-lg font-bold text-blue-900 mb-5">📋 Correction détaillée</h2>

        @foreach($session->qcm->questions as $index => $question)
        @php
            $detail   = $session->reponses_donnees[$question->id] ?? null;
            $correct  = $detail['correct'] ?? false;
            $donnees  = collect($detail['donnees'] ?? []);
            $correctes= collect($detail['correctes'] ?? []);
        @endphp
        <div class="border rounded-xl p-4 mb-4
            {{ $correct ? 'border-green-300 bg-green-50' : 'border-red-300 bg-red-50' }}">

            <div class="flex justify-between items-start mb-3">
                <p class="font-semibold text-gray-800 text-sm">
                    Q{{ $index + 1 }}. {{ $question->question }}
                </p>
                <span class="text-xs font-bold ml-3 flex-shrink-0
                    {{ $correct ? 'text-green-700' : 'text-red-700' }}">
                    {{ $correct ? '✅ +' . $question->points . ' pts' : '❌ 0 pt' }}
                </span>
            </div>

            <div class="space-y-2">
                @foreach($question->reponses as $reponse)
                @php
                    $estDonnee   = $donnees->contains($reponse->id);
                    $estCorrecte = $reponse->est_correcte;
                @endphp
                <div class="flex items-center space-x-2 text-xs px-3 py-2 rounded-lg
                    @if($estCorrecte && $estDonnee) bg-green-200 text-green-800
                    @elseif($estCorrecte && !$estDonnee) bg-green-100 text-green-700 border border-green-300
                    @elseif(!$estCorrecte && $estDonnee) bg-red-200 text-red-800
                    @else bg-gray-100 text-gray-600
                    @endif">
                    <span>
                        @if($estCorrecte && $estDonnee) ✅
                        @elseif($estCorrecte && !$estDonnee) ⭕
                        @elseif(!$estCorrecte && $estDonnee) ❌
                        @else ○
                        @endif
                    </span>
                    <span>{{ $reponse->contenu }}</span>
                    @if($estCorrecte && !$estDonnee)
                    <span class="ml-auto italic">(Bonne réponse manquée)</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    {{-- ACTIONS --}}
    <div class="flex flex-col sm:flex-row gap-4">
        <a href="{{ route('client.qcms.index') }}"
            class="flex-1 text-center bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-300 transition">
            ← Retour aux QCMs
        </a>
        @if(!$session->reussi && $session->tentative < $session->qcm->tentatives_max)
        <a href="{{ route('client.qcms.demarrer', $session->qcm) }}"
            class="flex-1 text-center bg-blue-800 text-white py-3 rounded-xl font-semibold hover:bg-blue-900 transition">
            🔄 Repasser le QCM
        </a>
        @endif
    </div>
</div>
@endsection
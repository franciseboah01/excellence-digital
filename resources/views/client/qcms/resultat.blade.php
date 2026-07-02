@extends('layouts.client')
@section('title', 'Résultat du QCM')

@section('content')
<div class="max-w-3xl mx-auto py-8">

    {{-- HEADER RÉSULTAT --}}
    <div class="rounded-2xl p-8 text-center mb-6 text-white"
        style="{{ $session->reussi
            ? 'background: linear-gradient(135deg, #059669, #10B981);'
            : 'background: linear-gradient(135deg, #B91C1C, #EF4444);' }}">

        <p class="text-6xl mb-4">{{ $session->reussi ? '🎓' : '😔' }}</p>
        <h1 class="text-3xl font-extrabold mb-2">
            {{ $session->reussi ? 'Félicitations !' : 'Essayez encore !' }}
        </h1>
        {{-- ✅ CORRECTION : $certificat n'existe pas dans cette vue (jamais transmis
             par le contrôleur). $session->qcm->bareme est déjà disponible. --}}
        <p class="text-xl font-bold mb-1">
            Note : {{ $session->note }}/{{ $session->qcm->bareme ?? 20 }}
        </p>
        <p class="text-sm" style="opacity: 0.8;">
            Score : {{ $session->score }} / {{ $session->score_max }} points •
            Tentative {{ $session->tentative }}
        </p>

        {{-- Barre de score --}}
        <div class="mt-5 rounded-full h-4 max-w-sm mx-auto" style="background-color: rgba(255,255,255,0.3);">
            <div class="h-4 rounded-full bg-white transition-all duration-1000"
                style="width: {{ $session->qcm->bareme > 0 ? ($session->note / $session->qcm->bareme) * 100 : 0 }}%">
            </div>
        </div>

        @if($session->reussi)
        <p class="mt-4 text-sm" style="color: rgba(255,255,255,0.8);">
            ✅ Note minimale requise : {{ $session->qcm->note_minimale }}/{{ $session->qcm->bareme ?? 20 }} — Objectif atteint !
        </p>
        @else
        <p class="mt-4 text-sm" style="color: rgba(255,255,255,0.8);">
            Note minimale requise : {{ $session->qcm->note_minimale }}/{{ $session->qcm->bareme ?? 20 }}
            @if($session->tentative < $session->qcm->tentatives_max)
            — Il vous reste {{ $session->qcm->tentatives_max - $session->tentative }} tentative(s).
            @endif
        </p>
        @endif
    </div>

    {{-- ✅ SUITE APRÈS RÉUSSITE — 3 cas distincts --}}
    @if($session->reussi && $session->certificat)
        {{-- Cas 1 : QCM final réussi, formation payante → certificat --}}
        <div class="rounded-2xl p-6 mb-6 text-center"
            style="background-color: rgba(251,191,36,0.08); border: 2px solid var(--edc-accent-gold);">
            <p class="text-4xl mb-3">🏆</p>
            <h2 class="text-xl font-bold mb-2" style="color: var(--edc-accent-gold);">Certificat disponible !</h2>
            <p class="text-sm mb-4" style="color: var(--edc-text-secondary);">
                N° {{ $session->certificat->numero_certificat }}
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-3">
                <a href="{{ route('client.certificats.telecharger', ['certificat' => $session->certificat, 'format' => 'pdf']) }}"
                    class="btn-primary btn-sm" style="background: linear-gradient(135deg, #FBBF24, #F59E0B); color: #1a1a1a;">
                    📄 Télécharger le certificat PDF
                </a>
            </div>
        </div>
    @elseif($session->reussi && $session->qcm->niveau_id !== null)
        {{-- Cas 2 : QCM de niveau réussi → jamais de certificat, juste validation --}}
        <div class="rounded-2xl p-6 mb-6 text-center"
            style="background-color: rgba(59,130,246,0.08); border: 2px solid var(--edc-primary);">
            <p class="text-4xl mb-3">✅</p>
            <h2 class="text-xl font-bold mb-2" style="color: var(--edc-primary-light);">Niveau validé !</h2>
            <p class="text-sm" style="color: var(--edc-text-secondary);">
                Vous pouvez maintenant accéder au niveau suivant de la formation.
            </p>
            <a href="{{ route('client.ressources', $session->qcm->formation) }}" class="btn-primary btn-sm mt-4 inline-block">
                📚 Continuer vers le niveau suivant
            </a>
        </div>
    @elseif($session->reussi)
        {{-- Cas 3 : QCM final réussi mais formation gratuite → pas de certificat --}}
        <div class="rounded-2xl p-6 mb-6 text-center"
            style="background-color: rgba(245,158,11,0.08); border: 2px solid var(--edc-accent-gold);">
            <p class="text-4xl mb-3">🎉</p>
            <h2 class="text-xl font-bold mb-2" style="color: var(--edc-accent-gold);">Formation réussie !</h2>
            <p class="text-sm" style="color: var(--edc-text-secondary);">
                Cette formation étant gratuite, elle ne donne pas droit à un certificat.
            </p>
        </div>
    @endif

    {{-- CORRECTION DÉTAILLÉE --}}
    <div class="edc-card p-6 mb-6">
        <h2 class="text-lg font-bold mb-5" style="color: var(--edc-text-primary);">📋 Correction détaillée</h2>

        @foreach($session->qcm->questions as $index => $question)
        @php
            $detail   = $session->reponses_donnees[$question->id] ?? null;
            $correct  = $detail['correct'] ?? false;
            $donnees  = collect($detail['donnees'] ?? []);
            $correctes= collect($detail['correctes'] ?? []);
        @endphp
        <div class="rounded-xl p-4 mb-4"
            style="{{ $correct
                ? 'background-color: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.25);'
                : 'background-color: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.25);' }}">

            <div class="flex justify-between items-start mb-3">
                <p class="font-semibold text-sm" style="color: var(--edc-text-primary);">
                    Q{{ $index + 1 }}. {{ $question->question }}
                </p>
                <span class="text-xs font-bold ml-3 flex-shrink-0"
                    style="color: {{ $correct ? 'var(--edc-secondary)' : 'var(--edc-danger)' }};">
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
                    @if($estCorrecte && $estDonnee) correct-answer
                    @elseif($estCorrecte && !$estDonnee) missed-answer
                    @elseif(!$estCorrecte && $estDonnee) wrong-answer
                    @else neutral-answer
                    @endif"
                    style="@if($estCorrecte && $estDonnee)
                        background-color: rgba(16,185,129,0.15); color: #34D399;
                    @elseif($estCorrecte && !$estDonnee)
                        background-color: rgba(16,185,129,0.08); color: #34D399; border: 1px solid rgba(16,185,129,0.3);
                    @elseif(!$estCorrecte && $estDonnee)
                        background-color: rgba(239,68,68,0.15); color: #F87171;
                    @else
                        background-color: var(--edc-bg-base); color: var(--edc-text-muted);
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
                    <span class="ml-auto italic" style="color: var(--edc-text-muted);">(Bonne réponse manquée)</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    {{-- ACTIONS --}}
    <div class="flex flex-col sm:flex-row gap-4">
        <a href="{{ route('client.qcms.index') }}" class="btn-tertiary flex-1 text-center">
            ← Retour aux QCMs
        </a>
        @if(!$session->reussi && $session->tentative < $session->qcm->tentatives_max)
        <a href="{{ route('client.qcms.demarrer', $session->qcm) }}" class="btn-primary flex-1 text-center">
            🔄 Repasser le QCM
        </a>
        @endif
    </div>
</div>
@endsection
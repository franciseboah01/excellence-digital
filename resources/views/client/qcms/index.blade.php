@extends('layouts.client')
@section('title', 'Mes QCMs')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8 text-center">
        <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">📝 Mes QCMs</h1>
        <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">
            Testez vos connaissances et suivez votre progression
        </p>
    </div>

    {{-- STATISTIQUES --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="edc-card p-3 text-center">
            <p class="text-2xl font-bold" style="color: var(--edc-primary-light);">{{ $stats['total'] }}</p>
            <p class="text-xs" style="color: var(--edc-text-muted);">Total QCMs</p>
        </div>
        <div class="edc-card p-3 text-center">
            <p class="text-2xl font-bold" style="color: var(--edc-secondary);">{{ $stats['reussis'] }}</p>
            <p class="text-xs" style="color: var(--edc-text-muted);">✅ Réussis</p>
        </div>
        <div class="edc-card p-3 text-center">
            <p class="text-2xl font-bold" style="color: #F59E0B;">{{ $stats['en_cours'] }}</p>
            <p class="text-xs" style="color: var(--edc-text-muted);">🔄 En cours</p>
        </div>
        <div class="edc-card p-3 text-center">
            <p class="text-2xl font-bold" style="color: var(--edc-text-muted);">{{ $stats['non_tentes'] }}</p>
            <p class="text-xs" style="color: var(--edc-text-muted);">⏳ Non tentés</p>
        </div>
    </div>

    {{-- LISTE DES QCMs --}}
    <div class="space-y-5">
        @forelse($qcms as $qcm)
        <div class="edc-card overflow-hidden {{ $qcm->est_verrouille ? 'opacity-60' : '' }}">
            <div class="p-5">
                {{-- EN-TÊTE --}}
                <div class="flex flex-wrap justify-between items-start mb-3 gap-2">
                    <div>
                        <span class="badge badge-blue text-xs">
                            🎓 {{ $qcm->formation->titre }}
                        </span>
                        @if($qcm->niveau)
                            <span class="badge badge-gray text-xs ml-1">
                                📂 {{ $qcm->niveau->nom }}
                            </span>
                        @else
                            <span class="badge text-xs ml-1" style="background-color: rgba(251,191,36,0.15); color: #FBBF24;">
                                🏁 QCM Final {{ $qcm->formation->est_payante ? '(certificat)' : '(sans certificat — formation gratuite)' }}
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if($qcm->est_verrouille)
                            <span class="badge text-xs" style="background-color: rgba(148,163,184,0.15); color: #94A3B8;">
                                🔒 Verrouillé
                            </span>
                        @elseif($qcm->deja_reussi)
                            <span class="badge badge-green font-bold">
                                {{ $qcm->niveau ? '✅ Niveau validé' : '🏆 Réussi !' }}
                            </span>
                        @elseif($qcm->tentatives_faites > 0)
                            <span class="badge badge-gold">
                                🔄 {{ $qcm->tentatives_faites }}/{{ $qcm->tentatives_max }} tentatives
                            </span>
                        @else
                            <span class="badge badge-gray">
                                ⏳ Non tenté
                            </span>
                        @endif
                    </div>
                </div>

                {{-- TITRE --}}
                <h3 class="font-bold mb-1" style="color: var(--edc-text-primary);">{{ $qcm->titre }}</h3>
                @if($qcm->description)
                <p class="text-sm mb-2" style="color: var(--edc-text-secondary);">{{ $qcm->description }}</p>
                @endif

                {{-- ✅ Message de verrouillage --}}
                @if($qcm->est_verrouille)
                <div class="rounded-lg p-3 mt-2 text-xs" style="background-color: rgba(148,163,184,0.08); color: var(--edc-text-muted);">
                    🔒 {{ $qcm->niveau ? 'Validez le niveau précédent pour débloquer ce QCM.' : 'Validez tous les niveaux de la formation pour débloquer ce QCM final.' }}
                </div>
                @endif

                {{-- STATISTIQUES QCM --}}
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-2 mt-3 text-center">
                    <div class="rounded-lg p-2" style="background-color: rgba(59,130,246,0.06);">
                        <p class="text-xs" style="color: var(--edc-text-muted);">Questions</p>
                        <p class="font-bold" style="color: var(--edc-primary-light);">{{ $qcm->questions_count }}</p>
                    </div>
                    <div class="rounded-lg p-2" style="background-color: rgba(16,185,129,0.06);">
                        <p class="text-xs" style="color: var(--edc-text-muted);">Note min.</p>
                        <p class="font-bold" style="color: var(--edc-secondary);">{{ $qcm->note_minimale }}/20</p>
                    </div>
                    <div class="rounded-lg p-2" style="background-color: rgba(245,158,11,0.06);">
                        <p class="text-xs" style="color: var(--edc-text-muted);">Durée/Q</p>
                        <p class="font-bold" style="color: var(--edc-accent-gold);">{{ $qcm->duree_par_question }}s</p>
                    </div>
                    <div class="rounded-lg p-2" style="background-color: rgba(139,92,246,0.06);">
                        <p class="text-xs" style="color: var(--edc-text-muted);">Meilleure note</p>
                        <p class="font-bold" style="color: #8B5CF6;">{{ $qcm->meilleure_note ?: '-' }}/20</p>
                    </div>
                    <div class="rounded-lg p-2" style="background-color: rgba(236,72,153,0.06);">
                        <p class="text-xs" style="color: var(--edc-text-muted);">Tentatives</p>
                        <p class="font-bold" style="color: #EC4899;">{{ $qcm->tentatives_faites }}/{{ $qcm->tentatives_max }}</p>
                    </div>
                </div>

                {{-- BARRE DE PROGRESSION --}}
                @if($qcm->meilleure_note)
                <div class="mt-3">
                    <div class="flex justify-between text-xs mb-1" style="color: var(--edc-text-muted);">
                        <span>Progression</span>
                        <span>{{ $qcm->meilleure_note }}/20</span>
                    </div>
                    <div class="w-full rounded-full h-2" style="background-color: var(--edc-bg-elevated);">
                        <div class="h-2 rounded-full transition-all"
                            style="width: {{ ($qcm->meilleure_note / 20) * 100 }}%;
                            {{ $qcm->deja_reussi ? 'background: linear-gradient(135deg, #10B981, #059669);' : 'background: linear-gradient(135deg, #3B82F6, #1D4ED8);' }}">
                        </div>
                    </div>
                </div>
                @endif

                {{-- HISTORIQUE DES TENTATIVES (SI DÉJÀ TENTÉ) --}}
                @if($qcm->tentatives_faites > 0)
                <details class="mt-3">
                    <summary class="text-xs cursor-pointer" style="color: var(--edc-text-muted);">
                        📋 Voir l'historique ({{ $qcm->tentatives_faites }} tentatives)
                    </summary>
                    <div class="mt-2 space-y-1.5">
                        @foreach($qcm->mes_sessions as $session)
                        <div class="flex items-center justify-between text-xs p-2 rounded-lg"
                            style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
                            <div class="flex items-center gap-2">
                                <span>#{{ $session->tentative }}</span>
                                <span class="text-xs" style="color: var(--edc-text-muted);">
                                    {{ $session->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold {{ $session->reussi ? 'text-green-500' : 'text-red-500' }}">
                                    {{ $session->note }}/20
                                </span>
                                @if($session->reussi)
                                <span class="text-green-500">✅</span>
                                @else
                                <span class="text-red-500">❌</span>
                                @endif
                                <a href="{{ route('client.qcms.resultat', $session) }}"
                                    class="text-blue-400 hover:underline">
                                    Voir
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </details>
                @endif
            </div>

            {{-- BOUTON D'ACTION --}}
            <div class="px-5 py-3" style="border-top: 1px solid var(--edc-border); background-color: var(--edc-bg-base);">
                @if($qcm->deja_reussi && $qcm->certificat)
                    {{-- QCM final réussi, formation payante : certificat disponible --}}
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold" style="color: var(--edc-secondary);">
                            🎓 Certificat obtenu !
                        </p>
                        <a href="{{ route('client.certificats.index') }}"
                            class="text-xs font-bold px-3 py-1.5 rounded-lg transition"
                            style="background: rgba(16,185,129,0.1); color: #10B981; border: 1px solid rgba(16,185,129,0.3);">
                            Voir mon certificat →
                        </a>
                    </div>
                @elseif($qcm->deja_reussi && $qcm->niveau)
                    {{-- ✅ QCM de niveau réussi : jamais de certificat, juste validation --}}
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold" style="color: var(--edc-primary-light);">
                            ✅ Niveau validé !
                        </p>
                        <a href="{{ route('client.ressources', $qcm->formation) }}"
                            class="text-xs font-bold px-3 py-1.5 rounded-lg transition"
                            style="background: rgba(59,130,246,0.1); color: #60A5FA; border: 1px solid rgba(59,130,246,0.3);">
                            Continuer la formation →
                        </a>
                    </div>
                @elseif($qcm->deja_reussi)
                    {{-- ✅ QCM final réussi mais formation gratuite : pas de certificat --}}
                    <p class="text-center text-sm font-semibold" style="color: var(--edc-accent-gold);">
                        🎉 Formation réussie ! (formation gratuite — pas de certificat)
                    </p>
                @elseif($qcm->est_verrouille)
                    <p class="text-center text-sm" style="color: var(--edc-text-muted);">
                        🔒 QCM verrouillé pour le moment.
                    </p>
                @elseif($qcm->peut_repasser)
                    <a href="{{ route('client.qcms.demarrer', $qcm) }}"
                        class="btn-primary btn-sm w-full text-center">
                        {{ $qcm->tentatives_faites > 0 ? '🔄 Repasser le QCM' : '▶️ Commencer le QCM' }}
                    </a>
                @else
                    <p class="text-center text-sm" style="color: var(--edc-danger);">
                        ❌ Tentatives épuisées ({{ $qcm->tentatives_max }}/{{ $qcm->tentatives_max }})
                        @if($qcm->meilleure_note)
                        <span class="block text-xs" style="color: var(--edc-text-muted);">
                            Meilleure note : {{ $qcm->meilleure_note }}/20 (min. {{ $qcm->note_minimale }}/20)
                        </span>
                        @endif
                    </p>
                @endif
            </div>
        </div>
        @empty
        <div class="edc-card text-center py-12" style="color: var(--edc-text-muted);">
            <p class="text-5xl mb-4">📝</p>
            <p>Aucun QCM disponible pour vos formations.</p>
            <p class="text-sm mt-1">Les enseignants ajouteront des QCMs prochainement.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
@extends('layouts.enseignant')
@section('title', 'Résultats — ' . $qcm->titre)

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <a href="{{ route('enseignant.qcms.index') }}"
            class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
            style="color: var(--edc-primary-light);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retour aux QCMs</span>
        </a>
        <h1 class="text-xl sm:text-2xl font-extrabold mt-1" style="color: var(--edc-text-primary);">
            📊 Résultats — {{ $qcm->titre }}
        </h1>
        <p class="text-sm mt-0.5" style="color: var(--edc-text-secondary);">
            🎓 {{ $qcm->formation->titre }}
            @if($qcm->niveau) — {{ $qcm->niveau->nom }} @endif
        </p>
    </div>
    <a href="{{ route('enseignant.qcms.questions', $qcm) }}" class="btn-primary btn-sm">
        <span>✏️</span><span>Gérer le QCM</span>
    </a>
</div>

{{-- STATS --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['total',              '🎯 Sessions totales', 'var(--edc-primary)'],
        ['reussis',            '✅ Réussis',          'var(--edc-secondary)'],
        ['echoues',            '❌ Échoués',          'var(--edc-danger)'],
        ['moyenne',            '📈 Moyenne',          '#A78BFA'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">
            @if($stat[0] === 'moyenne')
                {{ number_format($stats['moyenne'], 1) }}/20
            @elseif($stat[0] === 'echoues')
                {{ $stats['total'] - $stats['reussis'] }}
            @else
                {{ $stats[$stat[0]] }}
            @endif
        </p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

{{-- Infos QCM --}}
<div class="rounded-xl p-4 mb-6 flex flex-wrap gap-4 text-sm"
    style="background-color: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.20);">
    <span class="font-medium" style="color: var(--edc-primary-light);">⏱ {{ $qcm->duree_par_question }}s / question</span>
    <span class="font-medium" style="color: var(--edc-primary-light);">🎯 Note minimale : {{ $qcm->note_minimale }}/20</span>
    <span class="font-medium" style="color: var(--edc-primary-light);">🔄 Max {{ $qcm->tentatives_max }} tentative(s)</span>
    <span class="font-medium" style="color: var(--edc-primary-light);">📝 {{ $qcm->questions->count() }} questions</span>
    @if($stats['total'] > 0)
    <span class="font-medium" style="color: var(--edc-secondary);">
        📊 Taux de réussite : {{ round(($stats['reussis'] / $stats['total']) * 100) }}%
    </span>
    @endif
</div>

{{-- LISTE DES SESSIONS --}}
<div class="edc-card overflow-hidden">
    <div class="px-5 py-4 flex justify-between items-center" style="border-bottom: 1px solid var(--edc-border);">
        <h2 class="font-bold" style="color: var(--edc-text-primary);">👥 Sessions des apprenants ({{ $stats['total'] }})</h2>
    </div>

    @if($sessions->count())
    {{-- Version desktop --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th>Apprenant</th>
                    <th class="text-center">Tentative</th>
                    <th>Score</th>
                    <th>Note</th>
                    <th>Résultat</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                <tr>
                    <td>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                                {{ strtoupper(substr($session->user->prenom, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium" style="color: var(--edc-text-primary);">
                                    {{ $session->user->prenom }} {{ $session->user->nom }}
                                </p>
                                <p class="text-xs" style="color: var(--edc-text-muted);">{{ $session->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="text-sm font-semibold" style="color: var(--edc-text-primary);">{{ $session->tentative }}</span>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold" style="color: var(--edc-text-primary);">
                                {{ $session->score }}/{{ $session->score_max }}
                            </span>
                            <div class="w-16 rounded-full h-1.5" style="background-color: var(--edc-bg-elevated);">
                                <div class="h-1.5 rounded-full"
                                    style="width:{{ $session->score_max > 0 ? round(($session->score/$session->score_max)*100) : 0 }}%;
                                    background-color: {{ $session->reussi ? 'var(--edc-secondary)' : 'var(--edc-danger)' }};">
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="font-bold text-base" style="color: {{ $session->note >= $qcm->note_minimale ? 'var(--edc-secondary)' : 'var(--edc-danger)' }};">
                            {{ $session->note }}/20
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $session->reussi ? 'badge-green' : 'badge-red' }}">
                            {{ $session->reussi ? '✅ Réussi' : '❌ Échoué' }}
                        </span>
                    </td>
                    <td>
                        <span style="color: var(--edc-text-muted);">{{ $session->created_at->format('d/m/Y') }}</span>
                        <br>
                        <span style="color: var(--edc-text-muted); font-size: 0.65rem;">{{ $session->created_at->diffForHumans() }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Version mobile --}}
    <div class="sm:hidden divide-y" style="border-color: var(--edc-border);">
        @foreach($sessions as $session)
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                        style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                        {{ strtoupper(substr($session->user->prenom, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-sm" style="color: var(--edc-text-primary);">
                            {{ $session->user->prenom }} {{ $session->user->nom }}
                        </p>
                        <p class="text-xs" style="color: var(--edc-text-muted);">Tentative {{ $session->tentative }}</p>
                    </div>
                </div>
                <span class="badge {{ $session->reussi ? 'badge-green' : 'badge-red' }}">
                    {{ $session->reussi ? '✅ Réussi' : '❌ Échoué' }}
                </span>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="rounded-lg p-2" style="background-color: var(--edc-bg-base);">
                    <p class="text-xs" style="color: var(--edc-text-muted);">Score</p>
                    <p class="font-bold text-sm" style="color: var(--edc-text-primary);">{{ $session->score }}/{{ $session->score_max }}</p>
                </div>
                <div class="rounded-lg p-2" style="background-color: {{ $session->reussi ? 'rgba(16,185,129,0.06)' : 'rgba(239,68,68,0.06)' }};">
                    <p class="text-xs" style="color: var(--edc-text-muted);">Note</p>
                    <p class="font-bold text-sm" style="color: {{ $session->reussi ? 'var(--edc-secondary)' : 'var(--edc-danger)' }};">{{ $session->note }}/20</p>
                </div>
                <div class="rounded-lg p-2" style="background-color: var(--edc-bg-base);">
                    <p class="text-xs" style="color: var(--edc-text-muted);">Date</p>
                    <p class="font-bold text-xs" style="color: var(--edc-text-primary);">{{ $session->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            <div class="mt-3">
                <div class="flex justify-between text-xs mb-1" style="color: var(--edc-text-muted);">
                    <span>Progression</span>
                    <span>{{ $session->score_max > 0 ? round(($session->score/$session->score_max)*100) : 0 }}%</span>
                </div>
                <div class="w-full rounded-full h-2" style="background-color: var(--edc-bg-elevated);">
                    <div class="h-2 rounded-full"
                        style="width:{{ $session->score_max > 0 ? round(($session->score/$session->score_max)*100) : 0 }}%;
                        background-color: {{ $session->reussi ? 'var(--edc-secondary)' : 'var(--edc-danger)' }};">
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="px-5 py-4" style="border-top: 1px solid var(--edc-border);">
        {{ $sessions->links() }}
    </div>

    @else
    <div class="text-center py-16" style="color: var(--edc-text-muted);">
        <p class="text-5xl mb-4">📊</p>
        <p class="font-medium">Aucune session pour ce QCM.</p>
        <p class="text-sm mt-1">Activez le QCM pour que les apprenants puissent le passer.</p>
        @if(!$qcm->actif)
        <form method="POST" action="{{ route('enseignant.qcms.toggle', $qcm) }}" class="mt-4">
            @csrf
            <button type="submit" class="btn-success">
                ▶️ Activer le QCM
            </button>
        </form>
        @endif
    </div>
    @endif
</div>
@endsection
@extends('layouts.client')
@section('title', 'QCMs & Certificats')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8 text-center">
        <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">🎓 QCMs & Certificats</h1>
        <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">
            Testez vos connaissances et obtenez vos certificats
        </p>
    </div>

    {{-- CERTIFICATS OBTENUS --}}
    @if($certificats->count())
    <div class="edc-card p-6 mb-6">
        <h2 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">🏆 Mes Certificats obtenus</h2>
        <div class="space-y-3">
            @foreach($certificats as $certificat)
            <div class="rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
                <div class="min-w-0">
                    <p class="font-bold truncate" style="color: var(--edc-text-primary);">{{ $certificat->formation->titre }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--edc-text-muted);">
                        N° {{ $certificat->numero_certificat }} •
                        {{ $certificat->delivre_le->format('d/m/Y') }} •
                        Note : {{ $certificat->note_obtenue }}/{{ $qcm->bareme }}
                    </p>
                </div>
                <a href="{{ route('certificats.telecharger', $certificat) }}"
                    class="btn-xs rounded-lg text-xs font-bold flex-shrink-0 transition"
                    style="background: linear-gradient(135deg, #FBBF24, #F59E0B); color: #1a1a1a;"
                    onmouseover="this.style.filter='brightness(1.1)'"
                    onmouseout="this.style.filter='brightness(1)'">
                    📄 PDF
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- QCMS DISPONIBLES --}}
    <div class="space-y-5">
        @forelse($qcms as $qcm)
        <div class="edc-card overflow-hidden">
            <div class="p-5">
                <div class="flex justify-between items-start mb-3">
                    <span class="badge badge-blue">
                        🎓 {{ $qcm->formation->titre }}
                    </span>
                    @if($qcm->deja_reussi)
                    <span class="badge badge-green font-bold">
                        🏆 Réussi !
                    </span>
                    @elseif($qcm->tentatives_faites > 0)
                    <span class="badge badge-gold">
                        🔄 {{ $qcm->tentatives_faites }}/{{ $qcm->tentatives_max }} tentatives
                    </span>
                    @endif
                </div>

                <h3 class="font-bold mb-1" style="color: var(--edc-text-primary);">{{ $qcm->titre }}</h3>
                @if($qcm->niveau)
                <p class="text-xs" style="color: var(--edc-text-muted);">📂 {{ $qcm->niveau->nom }}</p>
                @endif

                <div class="grid grid-cols-3 gap-2 mt-3 text-center">
                    <div class="rounded-lg p-2" style="background-color: rgba(59,130,246,0.06);">
                        <p class="text-xs" style="color: var(--edc-text-muted);">Questions</p>
                        <p class="font-bold" style="color: var(--edc-primary-light);">{{ $qcm->questions_count }}</p>
                    </div>
                    <div class="rounded-lg p-2" style="background-color: rgba(16,185,129,0.06);">
                        <p class="text-xs" style="color: var(--edc-text-muted);">Note min.</p>
                        <p class="font-bold" style="color: var(--edc-secondary);">{{ $qcm->note_minimale }}/{{ $qcm->bareme }}</p>
                    </div>
                    <div class="rounded-lg p-2" style="background-color: rgba(245,158,11,0.06);">
                        <p class="text-xs" style="color: var(--edc-text-muted);">Durée/Q</p>
                        <p class="font-bold" style="color: var(--edc-accent-gold);">{{ $qcm->duree_par_question }}s</p>
                    </div>
                </div>

                @if($qcm->meilleure_note)
                <div class="mt-3">
                    <div class="flex justify-between text-xs mb-1" style="color: var(--edc-text-muted);">
                        <span>Meilleure note</span>
                        <span>{{ $qcm->meilleure_note }}/{{ $qcm->bareme }}</span>
                    </div>
                    <div class="w-full rounded-full h-2" style="background-color: var(--edc-bg-elevated);">
                        <div class="h-2 rounded-full transition-all"
                            style="width: {{ ($qcm->meilleure_note / 20) * 100 }}%;
                            {{ $qcm->deja_reussi ? 'background: linear-gradient(135deg, #10B981, #059669);' : 'background: linear-gradient(135deg, #3B82F6, #1D4ED8);' }}">
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="px-5 py-3" style="border-top: 1px solid var(--edc-border); background-color: var(--edc-bg-base);">
                @if($qcm->deja_reussi)
                <p class="text-center text-sm font-semibold" style="color: var(--edc-secondary);">
                    🎓 Certificat obtenu !
                </p>
                @elseif($qcm->peut_repasser)
                <a href="{{ route('client.qcms.demarrer', $qcm) }}"
                    class="btn-primary btn-sm w-full text-center">
                    {{ $qcm->tentatives_faites > 0 ? '🔄 Repasser le QCM' : '▶️ Commencer le QCM' }}
                </a>
                @else
                <p class="text-center text-sm" style="color: var(--edc-danger);">
                    ❌ Tentatives épuisées ({{ $qcm->tentatives_max }}/{{ $qcm->tentatives_max }})
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
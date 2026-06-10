@extends('layouts.admin')
@section('title', 'Détail QCM — ' . $qcm->titre)
@section('page_title', '📝 ' . $qcm->titre)
@section('page_subtitle', 'Détail complet du QCM')

@section('content')
<div class="mt-4">
    <a href="{{ route('admin.qcms.index') }}"
        class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
        style="color: var(--edc-primary-light);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Retour aux QCMs</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">

    {{-- INFOS QCM --}}
    <div class="edc-card p-6">
        <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">ℹ️ Informations</h3>
        <ul class="space-y-3 text-sm">
            <li class="flex justify-between">
                <span style="color: var(--edc-text-muted);">Formation</span>
                <span class="font-medium" style="color: var(--edc-text-primary);">{{ $qcm->formation->titre }}</span>
            </li>
            @if($qcm->niveau)
            <li class="flex justify-between">
                <span style="color: var(--edc-text-muted);">Niveau</span>
                <span class="font-medium" style="color: var(--edc-text-primary);">{{ $qcm->niveau->nom }}</span>
            </li>
            @endif
            <li class="flex justify-between">
                <span style="color: var(--edc-text-muted);">Enseignant</span>
                <span class="font-medium" style="color: var(--edc-text-primary);">{{ $qcm->createur->nom_complet }}</span>
            </li>
            <li class="flex justify-between">
                <span style="color: var(--edc-text-muted);">Durée/question</span>
                <span class="font-medium" style="color: var(--edc-text-primary);">{{ $qcm->duree_par_question }}s</span>
            </li>
            <li class="flex justify-between">
                <span style="color: var(--edc-text-muted);">Note minimale</span>
                <span class="font-medium" style="color: var(--edc-text-primary);">{{ $qcm->note_minimale }}/{{ $qcm->bareme }}</span>
            </li>
            <li class="flex justify-between">
                <span style="color: var(--edc-text-muted);">Tentatives max</span>
                <span class="font-medium" style="color: var(--edc-text-primary);">{{ $qcm->tentatives_max }}</span>
            </li>
            <li class="flex justify-between">
                <span style="color: var(--edc-text-muted);">Statut</span>
                <span class="badge text-xs" style="{{ $qcm->actif
                    ? 'background-color: rgba(16,185,129,0.12); color: #34D399;'
                    : 'background-color: rgba(148,163,184,0.10); color: #94A3B8;' }}">
                    {{ $qcm->actif ? '✅ Actif' : '⏸️ Inactif' }}
                </span>
            </li>
        </ul>
        @if($qcm->description)
        <p class="text-sm mt-4" style="color: var(--edc-text-secondary);">{{ $qcm->description }}</p>
        @endif
    </div>

    {{-- QUESTIONS --}}
    <div class="lg:col-span-2">
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">📋 Questions ({{ $qcm->questions->count() }})</h3>
            @forelse($qcm->questions as $index => $question)
            <div class="rounded-xl p-4 mb-3" style="border: 1px solid var(--edc-border); border-left: 4px solid var(--edc-primary);">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold"
                            style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">{{ $index + 1 }}</span>
                        <p class="font-semibold text-sm" style="color: var(--edc-text-primary);">{{ $question->question }}</p>
                    </div>
                    <span class="badge badge-blue text-xs">{{ $question->points }} pt</span>
                </div>
                <div class="space-y-1 ml-9">
                    @foreach($question->reponses as $reponse)
                    <div class="flex items-center space-x-2 text-xs">
                        <span style="color: {{ $reponse->est_correcte ? 'var(--edc-secondary)' : 'var(--edc-text-muted)' }};">
                            {{ $reponse->est_correcte ? '✅' : '○' }}
                        </span>
                        <span style="color: {{ $reponse->est_correcte ? 'var(--edc-text-primary)' : 'var(--edc-text-muted)' }};">
                            {{ $reponse->contenu }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <p class="text-center py-6" style="color: var(--edc-text-muted);">Aucune question.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
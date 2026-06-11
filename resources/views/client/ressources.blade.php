@extends('layouts.client')
@section('title', 'Ressources — ' . $formation->titre)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('client.formations') }}" class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
            style="color: var(--edc-primary-light);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retour aux formations</span>
        </a>
    </div>

    {{-- EN-TÊTE AVEC IMAGE --}}
    <div class="edc-card overflow-hidden mb-6">
        @if($formation->image)
        <img src="{{ asset('storage/' . $formation->image) }}" alt="{{ $formation->titre }}"
            class="w-full h-48 sm:h-56 object-cover">
        @else
        <div class="w-full h-32 sm:h-40 flex items-center justify-center"
            style="background: linear-gradient(135deg, #1e3a8a, #2563eb);">
            <span class="text-5xl">🎓</span>
        </div>
        @endif
        <div class="p-6">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="badge text-xs" style="background-color: rgba(59,130,246,0.12); color: #60A5FA;">
                    {{ $formation->module->icone ?? '📚' }} {{ $formation->module->nom ?? '—' }}
                </span>
                @if($formation->duree)
                <span class="badge text-xs" style="background-color: rgba(148,163,184,0.10); color: #94A3B8;">⏱ {{ $formation->duree }}</span>
                @endif
                @if($formation->prix)
                <span class="badge text-xs" style="background-color: rgba(16,185,129,0.12); color: #34D399;">💰 {{ number_format($formation->prix, 0, ',', ' ') }} FCFA</span>
                @endif
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">📚 {{ $formation->titre }}</h1>

            {{-- Description encadrée --}}
            <div class="mt-4 rounded-xl p-4" style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
                <p class="text-sm font-semibold mb-1" style="color: var(--edc-text-primary);">📝 Description</p>
                <p class="text-sm leading-relaxed" style="color: var(--edc-text-secondary);">{{ $formation->description }}</p>
            </div>
        </div>
    </div>

    {{-- Ressources générales --}}
    @if($ressources_generales->count())
    <div class="edc-card mb-6 p-6">
        <h2 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">📁 Ressources générales</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($ressources_generales as $ressource)
                @include('client.partials.ressource-card', ['ressource' => $ressource])
            @endforeach
        </div>
    </div>
    @endif

    {{-- Ressources par niveau --}}
    @forelse($niveaux as $niveau)
    <div class="edc-card mb-6 overflow-hidden">
        <div class="px-6 py-4" style="background-color: rgba(59,130,246,0.08); border-left: 4px solid var(--edc-primary);">
            <h2 class="text-lg font-bold" style="color: var(--edc-text-primary);">
                📂 Niveau {{ $niveau->ordre }} — {{ $niveau->nom }}
            </h2>
            @if($niveau->description)
            <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">{{ $niveau->description }}</p>
            @endif
        </div>

        <div class="p-6">
            @if($niveau->ressources->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($niveau->ressources as $ressource)
                    @include('client.partials.ressource-card', ['ressource' => $ressource])
                @endforeach
            </div>
            @else
            <p class="text-sm text-center py-4" style="color: var(--edc-text-muted);">
                Aucune ressource disponible pour ce niveau.
            </p>
            @endif
        </div>
    </div>
    @empty
    <div class="edc-card text-center py-12" style="color: var(--edc-text-muted);">
        <p class="text-4xl mb-3">📭</p>
        <p>Aucun contenu disponible pour le moment.</p>
        <p class="text-sm mt-1">L'enseignant ajoutera bientôt des ressources.</p>
    </div>
    @endforelse
</div>
@endsection
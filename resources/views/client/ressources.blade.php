@extends('layouts.client')
@section('title', 'Ressources — ' . $formation->titre)

@section('content')
<div class="mb-6">
    <a href="{{ route('client.formations') }}" class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
        style="color: var(--edc-primary-light);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Retour aux formations</span>
    </a>
    <h1 class="text-xl sm:text-2xl font-extrabold mt-2" style="color: var(--edc-text-primary);">📚 {{ $formation->titre }}</h1>
    <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">{{ $formation->description }}</p>
</div>

{{-- Ressources générales --}}
@if($ressources_generales->count())
<div class="edc-card mb-6 p-6">
    <h2 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">📁 Ressources générales</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
@endsection
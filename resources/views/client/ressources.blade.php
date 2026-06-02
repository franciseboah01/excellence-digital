@extends('layouts.client')
@section('title', 'Ressources — ' . $formation->titre)

@section('content')
<div class="mb-6">
    <a href="{{ route('client.formations') }}" class="text-blue-600 hover:underline text-sm">
        ← Retour aux formations
    </a>
    <h1 class="text-2xl font-bold text-blue-900 mt-2">📚 {{ $formation->titre }}</h1>
    <p class="text-gray-500 text-sm mt-1">{{ $formation->description }}</p>
</div>

{{-- Ressources générales --}}
@if($ressources_generales->count())
<div class="bg-white rounded-xl shadow mb-6 p-6">
    <h2 class="text-lg font-bold text-blue-900 mb-4">📁 Ressources générales</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($ressources_generales as $ressource)
            @include('client.partials.ressource-card', ['ressource' => $ressource])
        @endforeach
    </div>
</div>
@endif

{{-- Ressources par niveau --}}
@forelse($niveaux as $niveau)
<div class="bg-white rounded-xl shadow mb-6 overflow-hidden">
    <div class="bg-blue-50 border-l-4 border-blue-700 px-6 py-4">
        <h2 class="text-lg font-bold text-blue-900">
            📂 Niveau {{ $niveau->ordre }} — {{ $niveau->nom }}
        </h2>
        @if($niveau->description)
        <p class="text-gray-500 text-sm mt-1">{{ $niveau->description }}</p>
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
        <p class="text-gray-400 text-sm text-center py-4">
            Aucune ressource disponible pour ce niveau.
        </p>
        @endif
    </div>
</div>
@empty
<div class="bg-white rounded-xl shadow text-center py-12 text-gray-400">
    <p class="text-4xl mb-3">📭</p>
    <p>Aucun contenu disponible pour le moment.</p>
    <p class="text-sm mt-1">L'enseignant ajoutera bientôt des ressources.</p>
</div>
@endforelse
@endsection
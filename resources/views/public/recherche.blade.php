@extends('layouts.public')
@section('title', 'Recherche : ' . $query)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-blue-900">
            🔍 Résultats pour : "{{ $query }}"
        </h1>
        <p class="text-gray-500 mt-1">{{ $totalResultats }} résultat(s) trouvé(s)</p>
    </div>

    {{-- NOUVELLE RECHERCHE --}}
    <form action="{{ route('recherche') }}" method="GET" class="mb-8 flex">
        <input type="text" name="q" value="{{ $query }}"
            class="flex-1 border border-gray-300 rounded-l-xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit"
            class="bg-blue-800 text-white px-6 py-3 rounded-r-xl font-semibold hover:bg-blue-900 transition">
            🔍 Rechercher
        </button>
    </form>

    @if($totalResultats === 0)
    <div class="bg-white rounded-2xl shadow p-12 text-center text-gray-400">
        <p class="text-5xl mb-4">🔍</p>
        <p class="text-lg font-medium">Aucun résultat trouvé.</p>
        <p class="text-sm mt-2">Essayez avec d'autres mots-clés.</p>
    </div>
    @endif

    {{-- SERVICES --}}
    @if($services->count())
    <div class="mb-8">
        <h2 class="text-lg font-bold text-blue-900 mb-4 flex items-center space-x-2">
            <span>💼</span><span>Services ({{ $services->count() }})</span>
        </h2>
        <div class="space-y-3">
            @foreach($services as $service)
            <a href="{{ route('demande.form') }}?service={{ $service->id }}"
                class="block bg-white rounded-xl shadow p-5 hover:shadow-lg transition border-l-4 border-blue-700">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">{{ $service->icone }}</span>
                    <div>
                        <p class="font-bold text-gray-800">{{ $service->titre }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">{{ Str::limit($service->description, 100) }}</p>
                    </div>
                    @if($service->prix)
                    <span class="ml-auto text-blue-700 font-bold text-sm flex-shrink-0">
                        {{ number_format($service->prix, 0, ',', ' ') }} FCFA
                    </span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- FORMATIONS --}}
    @if($formations->count())
    <div class="mb-8">
        <h2 class="text-lg font-bold text-blue-900 mb-4 flex items-center space-x-2">
            <span>🎓</span><span>Formations ({{ $formations->count() }})</span>
        </h2>
        <div class="space-y-3">
            @foreach($formations as $formation)
            <a href="{{ route('formations.show', $formation) }}"
                class="block bg-white rounded-xl shadow p-5 hover:shadow-lg transition border-l-4 border-green-600">
                <p class="font-bold text-gray-800">{{ $formation->titre }}</p>
                <div class="flex items-center space-x-3 mt-1">
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                        {{ ucfirst($formation->niveau) }}
                    </span>
                    @if($formation->duree)
                    <span class="text-xs text-gray-400">⏱ {{ $formation->duree }}</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($formation->description, 100) }}</p>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ARTICLES --}}
    @if($articles->count())
    <div class="mb-8">
        <h2 class="text-lg font-bold text-blue-900 mb-4 flex items-center space-x-2">
            <span>📰</span><span>Articles ({{ $articles->count() }})</span>
        </h2>
        <div class="space-y-3">
            @foreach($articles as $article)
            <a href="{{ route('blog.show', $article->slug) }}"
                class="block bg-white rounded-xl shadow p-5 hover:shadow-lg transition border-l-4 border-purple-600">
                <p class="font-bold text-gray-800">{{ $article->titre }}</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    📅 {{ $article->publie_le?->format('d/m/Y') }} •
                    {{ ucfirst($article->categorie) }}
                </p>
                @if($article->extrait)
                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($article->extrait, 100) }}</p>
                @endif
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
@extends('layouts.public')
@section('title', 'Recherche : ' . $query)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="mb-8">
        <h1 class="text-section">
            🔍 Résultats pour : "{{ $query }}"
        </h1>
        <p class="text-sm mt-1" style="color: var(--edc-text-muted);">{{ $totalResultats }} résultat(s) trouvé(s)</p>
    </div>

    {{-- Barre de recherche --}}
    <form action="{{ route('recherche') }}" method="GET" class="mb-8 flex">
        <input type="text" name="q" value="{{ $query }}"
            class="edc-input flex-1 rounded-r-none" placeholder="Rechercher...">
        <button type="submit" class="btn-primary rounded-l-none">
            🔍 Rechercher
        </button>
    </form>

    @if($totalResultats === 0)
    <div class="edc-card p-12 text-center">
        <p class="text-5xl mb-4">🔍</p>
        <p class="text-lg font-medium" style="color: var(--edc-text-primary);">Aucun résultat trouvé.</p>
        <p class="text-sm mt-2" style="color: var(--edc-text-muted);">Essayez avec d'autres mots-clés.</p>
    </div>
    @endif

    {{-- SERVICES --}}
    @if($services->count())
    <div class="mb-8">
        <h2 class="text-lg font-bold mb-4 flex items-center space-x-2" style="color: var(--edc-text-primary);">
            <span>💼</span><span>Services ({{ $services->count() }})</span>
        </h2>
        <div class="space-y-3">
            @foreach($services as $service)
            <a href="{{ route('demande.form') }}?service={{ $service->id }}"
                class="edc-card p-5 block" style="border-left: 4px solid var(--edc-primary);">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">{{ $service->icone }}</span>
                    <div class="flex-1">
                        <p class="font-bold" style="color: var(--edc-text-primary);">{{ $service->titre }}</p>
                        <p class="text-sm mt-0.5" style="color: var(--edc-text-secondary);">{{ Str::limit($service->description, 100) }}</p>
                    </div>
                    @if($service->prix)
                    <span class="font-bold text-sm flex-shrink-0" style="color: var(--edc-primary-light);">
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
        <h2 class="text-lg font-bold mb-4 flex items-center space-x-2" style="color: var(--edc-text-primary);">
            <span>🎓</span><span>Formations ({{ $formations->count() }})</span>
        </h2>
        <div class="space-y-3">
            @foreach($formations as $formation)
            <a href="{{ route('formations.show', $formation) }}"
                class="edc-card p-5 block" style="border-left: 4px solid var(--edc-secondary);">
                <p class="font-bold" style="color: var(--edc-text-primary);">{{ $formation->titre }}</p>
                <div class="flex items-center space-x-3 mt-1">
                    <span class="badge badge-green">{{ ucfirst($formation->niveau) }}</span>
                    @if($formation->duree)
                    <span class="text-xs" style="color: var(--edc-text-muted);">⏱ {{ $formation->duree }}</span>
                    @endif
                </div>
                <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">{{ Str::limit($formation->description, 100) }}</p>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ARTICLES --}}
    @if($articles->count())
    <div class="mb-8">
        <h2 class="text-lg font-bold mb-4 flex items-center space-x-2" style="color: var(--edc-text-primary);">
            <span>📰</span><span>Articles ({{ $articles->count() }})</span>
        </h2>
        <div class="space-y-3">
            @foreach($articles as $article)
            <a href="{{ route('blog.show', $article->slug) }}"
                class="edc-card p-5 block" style="border-left: 4px solid #A78BFA;">
                <p class="font-bold" style="color: var(--edc-text-primary);">{{ $article->titre }}</p>
                <p class="text-xs mt-0.5" style="color: var(--edc-text-muted);">
                    📅 {{ $article->publie_le?->format('d/m/Y') }} •
                    {{ ucfirst($article->categorie) }}
                </p>
                @if($article->extrait)
                <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">{{ Str::limit($article->extrait, 100) }}</p>
                @endif
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
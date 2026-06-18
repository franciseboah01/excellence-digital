@extends('layouts.public')
@section('title', 'Blog & Actualités — ' . \App\Models\Configuration::get('site_nom', 'EDC'))

@section('content')
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="text-section">📰 Blog & Actualités</h1>
        <p class="section-subtitle">Conseils, tutoriels et actualités du numérique</p>
    </div>

    {{-- 🆕 FILTRES PAR CATÉGORIE --}}
    @if($categories->count())
    <div class="flex flex-wrap justify-center gap-2 mb-10">
        <a href="{{ route('blog.index') }}"
           class="px-4 py-2 rounded-full text-xs font-semibold transition
                  {{ empty($categorieActive) ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
            Tous
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('blog.index', ['categorie' => $cat]) }}"
           class="px-4 py-2 rounded-full text-xs font-semibold transition
                  {{ ($categorieActive ?? '') === $cat ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
            {{ ucfirst($cat) }}
        </a>
        @endforeach
    </div>
    @endif

    <div class="grid-responsive-3">
        @forelse($articles as $article)
        <article class="edc-card overflow-hidden">
            @if($article->image)
            <img src="{{ asset('storage/' . $article->image) }}"
                alt="{{ $article->titre }}"
                class="w-full h-44 object-cover">
            @else
            <div class="w-full h-44 flex items-center justify-center"
                style="background: linear-gradient(135deg, var(--edc-primary-dark), var(--edc-primary));">
                <span class="text-5xl">📰</span>
            </div>
            @endif

            <div class="p-5">
                <div class="flex items-center space-x-2 mb-3">
                    <span class="badge badge-blue">
                        {{ ucfirst($article->categorie) }}
                    </span>
                    <span class="text-xs" style="color: var(--edc-text-muted);">
                        👁 {{ $article->vues }} vue(s)
                    </span>
                </div>

                <h2 class="font-bold text-lg mb-2 leading-tight" style="color: var(--edc-text-primary);">
                    <a href="{{ route('blog.show', $article->slug) }}" class="hover:underline"
                        style="color: var(--edc-text-primary);">
                        {{ $article->titre }}
                    </a>
                </h2>

                @if($article->extrait)
                <p class="text-sm leading-relaxed mb-4" style="color: var(--edc-text-secondary);">
                    {{ Str::limit($article->extrait, 100) }}
                </p>
                @endif

                <div class="flex items-center justify-between">
                    <span class="text-xs" style="color: var(--edc-text-muted);">
                        {{ $article->publie_le?->format('d/m/Y') }}
                    </span>
                    <a href="{{ route('blog.show', $article->slug) }}" class="btn-primary btn-xs">
                        Lire →
                    </a>
                </div>
            </div>
        </article>
        @empty
        <div class="col-span-3 text-center py-16" style="color: var(--edc-text-muted);">
            <p class="text-5xl mb-4">📰</p>
            <p>Aucun article publié pour le moment.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-8">{{ $articles->links() }}</div>
</div>
@endsection
@extends('layouts.public')
@section('title', 'Blog & Actualités — EDC')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-blue-900">📰 Blog & Actualités</h1>
        <p class="text-gray-500 mt-3">Conseils, tutoriels et actualités du numérique</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($articles as $article)
        <article class="bg-white rounded-xl shadow hover:shadow-xl transition overflow-hidden">
            @if($article->image)
            <img src="{{ asset('storage/' . $article->image) }}"
                alt="{{ $article->titre }}"
                class="w-full h-44 object-cover">
            @else
            <div class="w-full h-44 bg-gradient-to-br from-blue-700 to-blue-500 flex items-center justify-center">
                <span class="text-5xl">📰</span>
            </div>
            @endif

            <div class="p-5">
                <div class="flex items-center space-x-2 mb-3">
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium">
                        {{ ucfirst($article->categorie) }}
                    </span>
                    <span class="text-xs text-gray-400">
                        👁 {{ $article->vues }} vue(s)
                    </span>
                </div>

                <h2 class="font-bold text-blue-900 mb-2 leading-tight">
                    <a href="{{ route('blog.show', $article->slug) }}"
                        class="hover:underline">
                        {{ $article->titre }}
                    </a>
                </h2>

                @if($article->extrait)
                <p class="text-gray-500 text-sm leading-relaxed mb-4">
                    {{ Str::limit($article->extrait, 100) }}
                </p>
                @endif

                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">
                        {{ $article->publie_le?->format('d/m/Y') }}
                    </span>
                    <a href="{{ route('blog.show', $article->slug) }}"
                        class="text-xs bg-blue-800 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition font-medium">
                        Lire →
                    </a>
                </div>
            </div>
        </article>
        @empty
        <div class="col-span-3 text-center py-16 text-gray-400">
            <p class="text-5xl mb-4">📰</p>
            <p>Aucun article publié pour le moment.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-8">{{ $articles->links() }}</div>
</div>
@endsection
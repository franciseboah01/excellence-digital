@extends('layouts.public')
@section('title', $article->titre . ' — EDC')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">

    <a href="{{ route('blog.index') }}" class="text-blue-600 hover:underline text-sm">
        ← Retour au blog
    </a>

    <article class="mt-6">
        {{-- IMAGE --}}
        @if($article->image)
        <img src="{{ asset('storage/' . $article->image) }}"
            alt="{{ $article->titre }}"
            class="w-full h-64 object-cover rounded-2xl mb-6">
        @endif

        {{-- HEADER --}}
        <div class="mb-6">
            <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">
                {{ ucfirst($article->categorie) }}
            </span>
            <h1 class="text-3xl font-bold text-blue-900 mt-4 leading-tight">
                {{ $article->titre }}
            </h1>
            <div class="flex items-center space-x-4 mt-3 text-sm text-gray-400">
                <span>✍️ {{ $article->auteur->prenom }} {{ $article->auteur->nom }}</span>
                <span>📅 {{ $article->publie_le?->format('d/m/Y') }}</span>
                <span>👁 {{ $article->vues }} vue(s)</span>
            </div>
        </div>

        {{-- PARTAGE RÉSEAUX SOCIAUX --}}
        <div class="flex items-center space-x-3 mb-6 p-4 bg-gray-50 rounded-xl">
            <span class="text-sm text-gray-600 font-semibold">Partager :</span>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                target="_blank"
                class="bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-medium hover:bg-blue-800 transition">
                📘 Facebook
            </a>
            <a href="https://wa.me/?text={{ urlencode($article->titre . ' — ' . url()->current()) }}"
                target="_blank"
                class="bg-green-600 text-white px-4 py-2 rounded-lg text-xs font-medium hover:bg-green-700 transition">
                💬 WhatsApp
            </a>
            <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->titre) }}&url={{ urlencode(url()->current()) }}"
                target="_blank"
                class="bg-sky-500 text-white px-4 py-2 rounded-lg text-xs font-medium hover:bg-sky-600 transition">
                🐦 Twitter
            </a>
        </div>

        {{-- CONTENU --}}
        <div class="prose prose-blue max-w-none text-gray-700 leading-relaxed">
            {!! nl2br(e($article->contenu)) !!}
        </div>
    </article>

    {{-- ARTICLES LIÉS --}}
    @if($articlesLies->count())
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-blue-900 mb-6">📰 Articles similaires</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($articlesLies as $lie)
            <a href="{{ route('blog.show', $lie->slug) }}"
                class="bg-white rounded-xl shadow hover:shadow-lg transition p-5 block">
                <h3 class="font-bold text-blue-900 mb-2">{{ Str::limit($lie->titre, 60) }}</h3>
                <p class="text-xs text-gray-400">{{ $lie->publie_le?->format('d/m/Y') }}</p>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
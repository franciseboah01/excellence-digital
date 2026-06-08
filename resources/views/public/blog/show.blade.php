@extends('layouts.public')
@section('title', $article->titre . ' — EDC')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">

    {{-- Retour --}}
    <a href="{{ route('blog.index') }}" class="inline-flex items-center space-x-1 text-sm font-medium transition"
        style="color: var(--edc-primary-light);"
        onmouseover="this.style.color='#93C5FD'"
        onmouseout="this.style.color='#60A5FA'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Retour au blog</span>
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
            <span class="badge badge-blue">
                {{ ucfirst($article->categorie) }}
            </span>
            <h1 class="text-3xl font-extrabold mt-4 leading-tight" style="color: var(--edc-text-primary);">
                {{ $article->titre }}
            </h1>
            <div class="flex items-center space-x-4 mt-3 text-sm" style="color: var(--edc-text-muted);">
                <span>✍️ {{ $article->auteur->prenom }} {{ $article->auteur->nom }}</span>
                <span>📅 {{ $article->publie_le?->format('d/m/Y') }}</span>
                <span>👁 {{ $article->vues }} vue(s)</span>
            </div>
        </div>

        {{-- PARTAGE RÉSEAUX SOCIAUX --}}
        <div class="flex items-center space-x-3 mb-8 p-4 rounded-xl"
            style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
            <span class="text-sm font-semibold" style="color: var(--edc-text-secondary);">Partager :</span>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                target="_blank"
                class="btn-primary btn-xs" style="background: #1877F2;">
                📘 Facebook
            </a>
            <a href="https://wa.me/?text={{ urlencode($article->titre . ' — ' . url()->current()) }}"
                target="_blank"
                class="btn-success btn-xs">
                💬 WhatsApp
            </a>
            <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->titre) }}&url={{ urlencode(url()->current()) }}"
                target="_blank"
                class="btn-xs rounded-lg px-4 py-2 text-white font-bold text-xs" style="background: #1DA1F2;">
                🐦 Twitter
            </a>
        </div>

        {{-- CONTENU --}}
        <div class="prose max-w-none leading-relaxed" style="color: var(--edc-text-secondary);">
            {!! nl2br(e($article->contenu)) !!}
        </div>
    </article>

    {{-- ARTICLES LIÉS --}}
    @if($articlesLies->count())
    <div class="mt-16">
        <h2 class="text-section mb-6">📰 Articles similaires</h2>
        <div class="grid-responsive-3">
            @foreach($articlesLies as $lie)
            <a href="{{ route('blog.show', $lie->slug) }}" class="edc-card p-5 block">
                <h3 class="font-bold mb-2" style="color: var(--edc-text-primary);">{{ Str::limit($lie->titre, 60) }}</h3>
                <p class="text-xs" style="color: var(--edc-text-muted);">{{ $lie->publie_le?->format('d/m/Y') }}</p>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
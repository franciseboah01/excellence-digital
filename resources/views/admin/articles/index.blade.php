@extends('layouts.admin')
@section('title', 'Articles')
@section('page_title', '📰 Gestion du Blog')
@section('page_subtitle', 'Articles et actualités')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
    @foreach([
        ['total',    '📰 Total',        'var(--edc-text-muted)'],
        ['publie',   '✅ Publiés',      'var(--edc-secondary)'],
        ['brouillon','📝 Brouillons',   'var(--edc-accent-gold)'],
        ['vues',     '👁 Vues totales', 'var(--edc-primary)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">{{ $stats[$stat[0]] }}</p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

<div class="flex justify-end mt-5">
    <a href="{{ route('admin.articles.create') }}" class="btn-primary btn-sm">➕ Nouvel article</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-4">
    @forelse($articles as $article)
    <div class="edc-card overflow-hidden {{ $article->statut === 'brouillon' ? 'opacity-70' : '' }}">
        @if($article->image)
        <img src="{{ asset('storage/' . $article->image) }}" class="w-full h-36 object-cover">
        @else
        <div class="w-full h-36 flex items-center justify-center" style="background: linear-gradient(135deg, #1e3a8a, #3B82F6);">
            <span class="text-4xl">📰</span>
        </div>
        @endif

        <div class="p-4">
            <div class="flex justify-between items-start mb-2">
                <span class="badge badge-blue text-xs">{{ $article->categorie }}</span>
                <span class="badge text-xs" style="{{ $article->statut === 'publie'
                    ? 'background-color: rgba(16,185,129,0.12); color: #34D399;'
                    : 'background-color: rgba(245,158,11,0.12); color: #FBBF24;' }}">
                    {{ $article->statut === 'publie' ? '✅' : '📝' }}
                </span>
            </div>
            <h3 class="font-bold text-sm leading-tight mb-2" style="color: var(--edc-text-primary);">{{ Str::limit($article->titre, 50) }}</h3>
            <div class="flex items-center justify-between text-xs mb-3" style="color: var(--edc-text-muted);">
                <span>{{ $article->publie_le?->format('d/m/Y') ?? 'Non publié' }}</span>
                <span>👁 {{ $article->vues }}</span>
            </div>
            <div class="flex justify-between items-center">
                <a href="{{ route('admin.articles.edit', $article) }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">✏️ Modifier</a>
                <div class="flex space-x-2">
                    @if($article->statut === 'publie')
                    <a href="{{ route('blog.show', $article->slug) }}" target="_blank" class="text-xs font-medium hover:underline" style="color: var(--edc-secondary);">👁 Voir</a>
                    @endif
                    <form method="POST" action="{{ route('admin.articles.destroy', $article) }}"
                        onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-medium hover:underline" style="color: var(--edc-danger);">🗑️</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 edc-card text-center py-16" style="color: var(--edc-text-muted);">
        <p class="text-5xl mb-4">📰</p>
        <p>Aucun article créé.</p>
        <a href="{{ route('admin.articles.create') }}" class="btn-primary btn-sm mt-4 inline-block">Créer le premier article</a>
    </div>
    @endforelse
</div>

<div class="mt-6">{{ $articles->links() }}</div>
@endsection
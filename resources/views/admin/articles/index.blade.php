@extends('layouts.admin')
@section('title', 'Articles')
@section('page_title', 'Gestion du Blog')
@section('page_subtitle', 'Articles et actualités')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-gray-400">
        <p class="text-2xl font-bold text-gray-700">{{ $stats['total'] }}</p>
        <p class="text-gray-500 text-xs mt-1">📰 Total</p>
    </div>
    <div class="bg-green-50 rounded-xl shadow p-4 text-center border-l-4 border-green-500">
        <p class="text-2xl font-bold text-green-600">{{ $stats['publie'] }}</p>
        <p class="text-gray-500 text-xs mt-1">✅ Publiés</p>
    </div>
    <div class="bg-yellow-50 rounded-xl shadow p-4 text-center border-l-4 border-yellow-500">
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['brouillon'] }}</p>
        <p class="text-gray-500 text-xs mt-1">📝 Brouillons</p>
    </div>
    <div class="bg-blue-50 rounded-xl shadow p-4 text-center border-l-4 border-blue-500">
        <p class="text-2xl font-bold text-blue-600">{{ $stats['vues'] }}</p>
        <p class="text-gray-500 text-xs mt-1">👁 Vues totales</p>
    </div>
</div>

<div class="flex justify-end mt-5">
    <a href="{{ route('admin.articles.create') }}"
        class="bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
        ➕ Nouvel article
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-4">
    @forelse($articles as $article)
    <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden
        {{ $article->statut === 'brouillon' ? 'opacity-70' : '' }}">
        @if($article->image)
        <img src="{{ asset('storage/' . $article->image) }}"
            class="w-full h-36 object-cover">
        @else
        <div class="w-full h-36 bg-gradient-to-br from-blue-700 to-blue-500 flex items-center justify-center">
            <span class="text-4xl">📰</span>
        </div>
        @endif

        <div class="p-4">
            <div class="flex justify-between items-start mb-2">
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                    {{ $article->categorie }}
                </span>
                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                    {{ $article->statut === 'publie' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $article->statut === 'publie' ? '✅' : '📝' }}
                </span>
            </div>
            <h3 class="font-bold text-gray-800 text-sm leading-tight mb-2">
                {{ Str::limit($article->titre, 50) }}
            </h3>
            <div class="flex items-center justify-between text-xs text-gray-400 mb-3">
                <span>{{ $article->publie_le?->format('d/m/Y') ?? 'Non publié' }}</span>
                <span>👁 {{ $article->vues }}</span>
            </div>
            <div class="flex justify-between items-center">
                <a href="{{ route('admin.articles.edit', $article) }}"
                    class="text-xs text-blue-600 hover:underline font-medium">✏️ Modifier</a>
                <div class="flex space-x-2">
                    @if($article->statut === 'publie')
                    <a href="{{ route('blog.show', $article->slug) }}" target="_blank"
                        class="text-xs text-green-600 hover:underline">👁 Voir</a>
                    @endif
                    <form method="POST" action="{{ route('admin.articles.destroy', $article) }}"
                        onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 hover:underline">🗑️</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-xl shadow text-center py-16 text-gray-400">
        <p class="text-5xl mb-4">📰</p>
        <p>Aucun article créé.</p>
        <a href="{{ route('admin.articles.create') }}"
            class="inline-block mt-4 bg-blue-800 text-white px-5 py-2 rounded-lg text-sm">
            Créer le premier article
        </a>
    </div>
    @endforelse
</div>

<div class="mt-6">{{ $articles->links() }}</div>
@endsection
@extends('layouts.enseignant')
@section('title', 'Mes Ressources')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-blue-900">📚 Mes Ressources</h1>
    <a href="{{ route('enseignant.ressources.create') }}"
        class="bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
        + Ajouter une ressource
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($ressources as $ressource)
    @php
        $config = match($ressource->type) {
            'pdf'      => ['border-red-200 bg-red-50',    '📄', 'text-red-700'],
            'ebook'    => ['border-purple-200 bg-purple-50','📖','text-purple-700'],
            'lien'     => ['border-green-200 bg-green-50', '🔗', 'text-green-700'],
            'video'    => ['border-yellow-200 bg-yellow-50','🎬','text-yellow-700'],
            'document' => ['border-blue-200 bg-blue-50',  '📝', 'text-blue-700'],
            default    => ['border-gray-200 bg-gray-50',  '📎', 'text-gray-700'],
        };
    @endphp
    <div class="bg-white rounded-xl shadow border {{ $config[0] }} overflow-hidden hover:shadow-lg transition">
        <div class="p-5">
            <div class="flex items-start justify-between mb-3">
                <span class="text-3xl">{{ $config[1] }}</span>
                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 font-medium uppercase">
                    {{ $ressource->type }}
                </span>
            </div>
            <h3 class="font-bold text-gray-800 mb-1">{{ $ressource->titre }}</h3>
            <p class="text-xs text-blue-700 font-medium mb-1">{{ $ressource->formation->titre }}</p>
            @if($ressource->niveau)
            <p class="text-xs text-gray-400">📂 {{ $ressource->niveau->nom }}</p>
            @endif
            @if($ressource->description)
            <p class="text-xs text-gray-500 mt-2">{{ Str::limit($ressource->description, 70) }}</p>
            @endif
        </div>
        <div class="border-t border-gray-100 px-5 py-3 flex justify-between items-center bg-gray-50">
            <span class="text-xs text-gray-400">{{ $ressource->created_at->format('d/m/Y') }}</span>
            <div class="flex space-x-2">
                <a href="{{ route('enseignant.ressources.edit', $ressource) }}"
                    class="text-xs text-blue-600 hover:underline font-medium">✏️ Modifier</a>
                <form method="POST" action="{{ route('enseignant.ressources.destroy', $ressource) }}"
                    onsubmit="return confirm('Supprimer cette ressource ?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="text-xs text-red-600 hover:underline font-medium">🗑️ Supprimer</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-xl shadow text-center py-16 text-gray-400">
        <p class="text-5xl mb-4">📭</p>
        <p class="font-medium">Aucune ressource ajoutée pour le moment.</p>
        <a href="{{ route('enseignant.ressources.create') }}"
            class="inline-block mt-4 bg-blue-800 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-900 transition">
            Ajouter ma première ressource
        </a>
    </div>
    @endforelse
</div>

<div class="mt-6">{{ $ressources->links() }}</div>
@endsection
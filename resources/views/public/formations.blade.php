@extends('layouts.public')
@section('title', 'Formations — Excellence Digital Center')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-blue-900">Nos Formations</h1>
        <p class="text-gray-500 mt-3">Des formations pratiques, accessibles à tous</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($formations as $formation)
        <div class="bg-white rounded-xl shadow hover:shadow-xl transition overflow-hidden">
            @if($formation->image)
            <img src="{{ asset('storage/' . $formation->image) }}"
                alt="{{ $formation->titre }}" class="w-full h-44 object-cover">
            @else
            <div class="w-full h-44 bg-gradient-to-br from-blue-800 to-blue-500 flex items-center justify-center">
                <span class="text-6xl">🎓</span>
            </div>
            @endif
            <div class="p-6">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium">
                        {{ ucfirst($formation->niveau) }}
                    </span>
                    @if($formation->duree)
                    <span class="text-xs text-gray-400">⏱ {{ $formation->duree }}</span>
                    @endif
                </div>
                <h3 class="text-lg font-bold text-blue-900 mb-2">{{ $formation->titre }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-4">
                    {{ Str::limit($formation->description, 100) }}
                </p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">
                        👥 {{ $formation->inscriptions_count }} inscrits
                    </span>
                    <a href="{{ route('formations.show', $formation) }}"
                        class="bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
                        Voir détails
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-16 text-gray-400">
            <p class="text-5xl mb-4">🎓</p>
            <p>Aucune formation disponible pour le moment.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
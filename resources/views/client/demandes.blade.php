@extends('layouts.client')
@section('title', 'Mes Demandes')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-blue-900">📋 Mes Demandes de Service</h1>
    <a href="{{ route('demande.form') }}"
        class="bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
        + Nouvelle demande
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    @forelse($demandes as $demande)
    <div class="p-6 border-b border-gray-100 last:border-0">

        {{-- En-tête demande --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-blue-900">{{ $demande->service->titre }}</h3>
                <p class="text-xs text-gray-400 mt-1">
                    Demande #{{ $demande->id }} — {{ $demande->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            @include('client.partials.statut-badge', ['statut' => $demande->statut])
        </div>

        {{-- TIMELINE STATUT --}}
        <div class="flex items-center space-x-2 mt-4 overflow-x-auto pb-2">
            @php
                $etapes = [
                    'en_attente' => ['⏳', 'En attente'],
                    'en_cours'   => ['🔄', 'En cours'],
                    'termine'    => ['✅', 'Terminé'],
                ];
                $statuts = array_keys($etapes);
                $indexActuel = array_search($demande->statut, $statuts);
            @endphp

            @foreach($etapes as $key => $etape)
            @php $index = array_search($key, $statuts); @endphp
            <div class="flex items-center">
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold
                        {{ $index <= $indexActuel
                            ? 'bg-blue-800 text-white'
                            : 'bg-gray-200 text-gray-400' }}">
                        {{ $etape[0] }}
                    </div>
                    <p class="text-xs mt-1 {{ $index <= $indexActuel ? 'text-blue-800 font-semibold' : 'text-gray-400' }}">
                        {{ $etape[1] }}
                    </p>
                </div>
                @if(!$loop->last)
                <div class="w-16 h-1 mx-1 rounded
                    {{ $index < $indexActuel ? 'bg-blue-800' : 'bg-gray-200' }}">
                </div>
                @endif
            </div>
            @endforeach
        </div>

        @if($demande->message)
        <p class="mt-3 text-sm text-gray-500 bg-gray-50 rounded-lg p-3">
            💬 {{ $demande->message }}
        </p>
        @endif
    </div>
    @empty
    <div class="text-center py-16 text-gray-400">
        <p class="text-5xl mb-4">📋</p>
        <p class="font-medium">Aucune demande pour le moment.</p>
        <a href="{{ route('demande.form') }}"
            class="inline-block mt-4 bg-blue-800 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-900 transition">
            Faire une demande
        </a>
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $demandes->links() }}</div>
@endsection
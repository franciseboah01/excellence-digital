@extends('layouts.client')
@section('title', 'Mes Formations')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-blue-900">🎓 Mes Formations</h1>
    <p class="text-gray-500 mt-1">Accédez aux ressources de vos formations validées</p>
</div>

@forelse($inscriptions as $inscription)
<div class="bg-white rounded-xl shadow mb-6 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-800 to-blue-600 p-5 text-white">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold">{{ $inscription->formation->titre }}</h2>
                <p class="text-blue-200 text-sm mt-1">
                    Niveau : {{ ucfirst($inscription->formation->niveau) }}
                    @if($inscription->formation->duree)
                    • ⏱ {{ $inscription->formation->duree }}
                    @endif
                </p>
            </div>
            @php
                $statutInscription = match($inscription->statut) {
                    'valide'     => ['bg-green-400 text-white', '✅ Validé'],
                    'en_attente' => ['bg-yellow-400 text-gray-900', '⏳ En attente'],
                    'refuse'     => ['bg-red-400 text-white', '❌ Refusé'],
                    default      => ['bg-gray-300 text-gray-700', $inscription->statut],
                };
            @endphp
            <span class="text-xs px-3 py-1 rounded-full font-medium {{ $statutInscription[0] }}">
                {{ $statutInscription[1] }}
            </span>
        </div>
    </div>

    <div class="p-5">
        @if($inscription->statut === 'valide')
        <a href="{{ route('client.ressources', $inscription->formation) }}"
            class="inline-flex items-center space-x-2 bg-blue-800 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-900 transition">
            <span>📚 Accéder aux ressources</span>
            <span>→</span>
        </a>
        @else
        <p class="text-yellow-700 bg-yellow-50 rounded-lg px-4 py-3 text-sm">
            ⏳ Votre inscription est en attente de validation par l'administrateur.
        </p>
        @endif
    </div>
</div>
@empty
<div class="bg-white rounded-xl shadow text-center py-16 text-gray-400">
    <p class="text-5xl mb-4">🎓</p>
    <p class="font-medium">Vous n'êtes inscrit à aucune formation.</p>
    <a href="{{ route('formations.index') }}"
        class="inline-block mt-4 bg-blue-800 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-900 transition">
        Voir les formations disponibles
    </a>
</div>
@endforelse
@endsection
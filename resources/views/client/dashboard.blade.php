@extends('layouts.client')
@section('title', 'Tableau de bord — Mon Espace')

@section('content')

{{-- En-tête de bienvenue --}}
<div class="mb-8">
    <h1 class="text-2xl font-bold text-blue-900">
        Bonjour {{ auth()->user()->prenom }} 👋
    </h1>
    <p class="text-gray-500 mt-1">Bienvenue dans votre espace personnel</p>
</div>

{{-- CARDS STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-600">
        <p class="text-3xl font-bold text-blue-700">{{ $stats['demandes'] }}</p>
        <p class="text-gray-500 text-sm mt-1">📋 Demandes totales</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-3xl font-bold text-green-600">{{ $stats['demandes_cours'] }}</p>
        <p class="text-gray-500 text-sm mt-1">🔄 En cours</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
        <p class="text-3xl font-bold text-purple-600">{{ $stats['formations'] }}</p>
        <p class="text-gray-500 text-sm mt-1">🎓 Formations</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-red-500">
        <p class="text-3xl font-bold text-red-500">{{ $stats['notifications'] }}</p>
        <p class="text-gray-500 text-sm mt-1">🔔 Non lues</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- DERNIÈRES DEMANDES --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold text-blue-900">📋 Dernières demandes</h2>
            <a href="{{ route('client.demandes') }}"
                class="text-sm text-blue-600 hover:underline">Voir tout</a>
        </div>

        @forelse($dernieres_demandes as $demande)
        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
            <div>
                <p class="font-medium text-gray-800 text-sm">{{ $demande->service->titre }}</p>
                <p class="text-xs text-gray-400">{{ $demande->created_at->format('d/m/Y') }}</p>
            </div>
            @include('client.partials.statut-badge', ['statut' => $demande->statut])
        </div>
        @empty
        <p class="text-gray-400 text-sm text-center py-4">Aucune demande pour le moment.</p>
        @endforelse

        <div class="mt-4">
            <a href="{{ route('demande.form') }}"
                class="w-full block text-center bg-blue-800 text-white py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
                + Nouvelle demande
            </a>
        </div>
    </div>

    {{-- MES FORMATIONS --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold text-blue-900">🎓 Mes formations</h2>
            <a href="{{ route('client.formations') }}"
                class="text-sm text-blue-600 hover:underline">Voir tout</a>
        </div>

        @forelse($mes_formations as $inscription)
        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
            <div>
                <p class="font-medium text-gray-800 text-sm">{{ $inscription->formation->titre }}</p>
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                    {{ ucfirst($inscription->formation->niveau) }}
                </span>
            </div>
            <a href="{{ route('client.ressources', $inscription->formation) }}"
                class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-medium hover:bg-green-200 transition">
                Accéder →
            </a>
        </div>
        @empty
        <p class="text-gray-400 text-sm text-center py-4">Aucune formation validée.</p>
        @endforelse
    </div>

    {{-- NOTIFICATIONS --}}
    <div class="bg-white rounded-xl shadow p-6 lg:col-span-2">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold text-blue-900">🔔 Notifications récentes</h2>
            <a href="{{ route('client.notifications') }}"
                class="text-sm text-blue-600 hover:underline">Tout voir</a>
        </div>

        @forelse($notifications as $notif)
        <div class="flex items-start space-x-3 py-3 border-b border-gray-100 last:border-0
            {{ !$notif->lu ? 'bg-blue-50 rounded-lg px-3' : '' }}">
            <div class="mt-1 text-xl">
                @if($notif->type == 'success') ✅
                @elseif($notif->type == 'warning') ⚠️
                @elseif($notif->type == 'error') ❌
                @else 📢
                @endif
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-800 text-sm">{{ $notif->titre }}</p>
                <p class="text-gray-500 text-xs mt-0.5">{{ $notif->message }}</p>
                <p class="text-gray-300 text-xs mt-1">{{ $notif->created_at->diffForHumans() }}</p>
            </div>
            @if(!$notif->lu)
            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></span>
            @endif
        </div>
        @empty
        <p class="text-gray-400 text-sm text-center py-4">Aucune notification.</p>
        @endforelse
    </div>

</div>
@endsection
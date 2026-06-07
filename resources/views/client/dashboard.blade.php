@extends('layouts.client')
@section('title', 'Mon Espace — EDC')

@section('content')

{{-- HEADER --}}
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-extrabold text-blue-900">
            Bonjour {{ auth()->user()->prenom }} 👋
        </h1>
        <p class="text-gray-500 mt-1 text-sm">
            Bienvenue dans votre espace — {{ now()->isoFormat('dddd D MMMM Y') }}
        </p>
    </div>
    <a href="{{ route('demande.form') }}" class="btn-primary mt-4 md:mt-0 btn-sm">
        <span>➕</span><span>Nouvelle demande</span>
    </a>
</div>

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="stat-card border-blue-600">
        <p class="stat-value text-blue-700">{{ $stats['demandes'] }}</p>
        <p class="stat-label">📋 Demandes</p>
    </div>
    <div class="stat-card border-green-500">
        <p class="stat-value text-green-600">{{ $stats['demandes_cours'] }}</p>
        <p class="stat-label">🔄 En cours</p>
    </div>
    <div class="stat-card border-purple-500">
        <p class="stat-value text-purple-600">{{ $stats['formations'] }}</p>
        <p class="stat-label">🎓 Formations</p>
    </div>
    <div class="stat-card border-red-500">
        <p class="stat-value text-red-500">{{ $stats['notifications'] }}</p>
        <p class="stat-label">🔔 Non lues</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- COLONNE PRINCIPALE --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- DERNIÈRES DEMANDES --}}
        <div class="edc-card p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-lg font-bold text-blue-900">📋 Dernières demandes</h2>
                <a href="{{ route('client.demandes') }}"
                    class="text-sm text-blue-600 hover:underline font-medium">
                    Tout voir →
                </a>
            </div>

            @forelse($dernieres_demandes as $demande)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-lg flex-shrink-0">
                        {{ $demande->service->icone ?? '💼' }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 text-sm">{{ $demande->service->titre }}</p>
                        <p class="text-xs text-gray-400">{{ $demande->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                @include('client.partials.statut-badge', ['statut' => $demande->statut])
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">
                <p class="text-3xl mb-2">📋</p>
                <p class="text-sm">Aucune demande pour le moment.</p>
            </div>
            @endforelse

            <div class="mt-4">
                <a href="{{ route('demande.form') }}" class="btn-primary btn-sm w-full text-center">
                    + Nouvelle demande
                </a>
            </div>
        </div>

        {{-- MES FORMATIONS --}}
        <div class="edc-card p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-lg font-bold text-blue-900">🎓 Mes Formations</h2>
                <a href="{{ route('client.formations') }}"
                    class="text-sm text-blue-600 hover:underline font-medium">
                    Tout voir →
                </a>
            </div>

            @forelse($mes_formations as $inscription)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div>
                    <p class="font-medium text-gray-800 text-sm">{{ $inscription->formation->titre }}</p>
                    <span class="badge badge-blue text-xs mt-1">
                        {{ ucfirst($inscription->formation->niveau) }}
                    </span>
                </div>
                <a href="{{ route('client.ressources', $inscription->formation) }}"
                    class="badge badge-green hover:bg-green-200 transition cursor-pointer">
                    Accéder →
                </a>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">
                <p class="text-3xl mb-2">🎓</p>
                <p class="text-sm">Aucune formation en cours.</p>
                <a href="{{ route('formations.index') }}"
                    class="inline-block mt-3 btn-primary btn-xs">
                    Voir les formations
                </a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- COLONNE DROITE --}}
    <div class="space-y-6">

        {{-- PROFIL RAPIDE --}}
        <div class="edc-card p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-blue-800 flex items-center justify-center text-white text-2xl font-bold mx-auto mb-3 overflow-hidden">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                        class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                @endif
            </div>
            <p class="font-bold text-blue-900">{{ auth()->user()->nom_complet }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ auth()->user()->email }}</p>
            <span class="badge badge-blue mt-2">👤 Client</span>
            <div class="mt-4">
                <a href="{{ route('client.profil') }}" class="btn-secondary btn-sm w-full">
                    ✏️ Mon profil
                </a>
            </div>
        </div>

        {{-- ACTIONS RAPIDES --}}
        <div class="edc-card p-6">
            <h3 class="font-bold text-blue-900 mb-4">⚡ Actions rapides</h3>
            <div class="space-y-2">
                <a href="{{ route('demande.form') }}"
                    class="flex items-center space-x-3 p-3 bg-blue-50 rounded-xl hover:bg-blue-100 transition text-sm font-medium text-blue-800">
                    <span>💼</span><span>Nouveau service</span>
                </a>
                <a href="{{ route('client.formations') }}"
                    class="flex items-center space-x-3 p-3 bg-green-50 rounded-xl hover:bg-green-100 transition text-sm font-medium text-green-800">
                    <span>🎓</span><span>Mes cours</span>
                </a>
                <a href="{{ route('client.qcms.index') }}"
                    class="flex items-center space-x-3 p-3 bg-purple-50 rounded-xl hover:bg-purple-100 transition text-sm font-medium text-purple-800">
                    <span>📝</span><span>QCMs & Certifs</span>
                </a>
                <a href="{{ route('messages.index') }}"
                    class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-xl hover:bg-yellow-100 transition text-sm font-medium text-yellow-800">
                    <span>💬</span><span>Messagerie</span>
                </a>
                <a href="{{ route('client.temoignages.index') }}"
                    class="flex items-center space-x-3 p-3 bg-red-50 rounded-xl hover:bg-red-100 transition text-sm font-medium text-red-700">
                    <span>⭐</span><span>Laisser un avis</span>
                </a>
            </div>
        </div>

        {{-- NOTIFICATIONS RÉCENTES --}}
        <div class="edc-card p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-blue-900">🔔 Notifications</h3>
                <a href="{{ route('client.notifications') }}"
                    class="text-xs text-blue-600 hover:underline">Tout voir</a>
            </div>

            @forelse($notifications->take(3) as $notif)
            <div class="flex items-start space-x-2 py-2 border-b border-gray-100 last:border-0">
                <span class="text-lg mt-0.5">
                    @if($notif->type == 'success') ✅
                    @elseif($notif->type == 'warning') ⚠️
                    @elseif($notif->type == 'error') ❌
                    @else 📢
                    @endif
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-800 truncate">{{ $notif->titre }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
                @if(!$notif->lu)
                <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1"></div>
                @endif
            </div>
            @empty
            <p class="text-gray-400 text-xs text-center py-4">Aucune notification.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
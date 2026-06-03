@extends('layouts.admin')
@section('title', 'Détail — ' . $user->nom_complet)
@section('page_title', 'Fiche Utilisateur')
@section('page_subtitle', $user->nom_complet)

@section('content')
<div class="mt-6">
    <a href="{{ route('admin.users.index') }}"
        class="text-blue-600 hover:underline text-sm">← Retour à la liste</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">

    {{-- PROFIL --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="text-center mb-5">
            <div class="w-20 h-20 rounded-full bg-blue-800 flex items-center justify-center text-white text-3xl font-bold mx-auto mb-3">
                {{ strtoupper(substr($user->prenom, 0, 1)) }}
            </div>
            <h2 class="text-xl font-bold text-blue-900">{{ $user->nom_complet }}</h2>
            <p class="text-gray-500 text-sm mt-1">{{ $user->email }}</p>
            @foreach($roles as $role)
            <span class="inline-block mt-2 text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">
                {{ ucfirst($role) }}
            </span>
            @endforeach
        </div>

        <ul class="space-y-3 text-sm">
            <li class="flex justify-between">
                <span class="text-gray-500">Téléphone</span>
                <span class="font-medium">{{ $user->telephone ?? '—' }}</span>
            </li>
            <li class="flex justify-between">
                <span class="text-gray-500">Statut</span>
                @php
                    $s = match($user->statut) {
                        'actif'    => 'bg-green-100 text-green-700',
                        'suspendu' => 'bg-red-100 text-red-700',
                        default    => 'bg-gray-100 text-gray-600',
                    };
                @endphp
                <span class="text-xs px-2 py-1 rounded-full font-medium {{ $s }}">
                    {{ ucfirst($user->statut) }}
                </span>
            </li>
            <li class="flex justify-between">
                <span class="text-gray-500">Email vérifié</span>
                <span>{{ $user->email_verified_at ? '✅' : '❌' }}</span>
            </li>
            <li class="flex justify-between">
                <span class="text-gray-500">Inscrit le</span>
                <span class="font-medium">{{ $user->created_at->format('d/m/Y') }}</span>
            </li>
        </ul>

        {{-- Actions --}}
        <div class="mt-5 space-y-2">
            <form method="POST"
                action="{{ route('admin.users.toggle-statut', $user) }}">
                @csrf
                <button type="submit"
                    class="w-full py-2 rounded-lg text-sm font-medium transition
                    {{ $user->statut === 'actif'
                        ? 'bg-red-100 text-red-700 hover:bg-red-200'
                        : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                    {{ $user->statut === 'actif' ? '⛔ Suspendre le compte' : '✅ Réactiver le compte' }}
                </button>
            </form>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                onsubmit="return confirm('Supprimer définitivement ce compte ?')">
                @csrf @method('DELETE')
                <button type="submit"
                    class="w-full py-2 rounded-lg text-sm font-medium bg-red-50 text-red-600 hover:bg-red-100 transition">
                    🗑️ Supprimer le compte
                </button>
            </form>
        </div>
    </div>

    {{-- DÉTAILS --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Formations & Inscriptions --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">🎓 Formations & Inscriptions</h3>
            @forelse($user->inscriptions as $inscription)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div>
                    <p class="font-medium text-gray-800">{{ $inscription->formation->titre }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Inscrit le {{ $inscription->date_inscription->format('d/m/Y') }}
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    @php
                        $si = match($inscription->statut) {
                            'valide'     => ['bg-green-100 text-green-700', '✅ Validé'],
                            'en_attente' => ['bg-yellow-100 text-yellow-700', '⏳ En attente'],
                            'refuse'     => ['bg-red-100 text-red-700', '❌ Refusé'],
                            default      => ['bg-gray-100 text-gray-600', $inscription->statut],
                        };
                    @endphp
                    <span class="text-xs px-2 py-1 rounded-full font-medium {{ $si[0] }}">
                        {{ $si[1] }}
                    </span>
                    @if($inscription->statut === 'en_attente')
                    <form method="POST"
                        action="{{ route('admin.users.inscription.valider', $inscription) }}">
                        @csrf
                        <button type="submit"
                            class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 transition">
                            Valider
                        </button>
                    </form>
                    <form method="POST"
                        action="{{ route('admin.users.inscription.rejeter', $inscription) }}">
                        @csrf
                        <button type="submit"
                            class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 transition">
                            Rejeter
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">Aucune inscription.</p>
            @endforelse
        </div>

        {{-- Demandes de services --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">📋 Historique des demandes</h3>
            @forelse($user->demandesService as $demande)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div>
                    <p class="font-medium text-gray-800 text-sm">{{ $demande->service->titre }}</p>
                    <p class="text-xs text-gray-400">{{ $demande->created_at->format('d/m/Y') }}</p>
                </div>
                @php
                    $sd = match($demande->statut) {
                        'en_attente' => 'bg-yellow-100 text-yellow-700',
                        'en_cours'   => 'bg-blue-100 text-blue-700',
                        'termine'    => 'bg-green-100 text-green-700',
                        'annule'     => 'bg-red-100 text-red-700',
                        default      => 'bg-gray-100 text-gray-600',
                    };
                @endphp
                <span class="text-xs px-2 py-1 rounded-full font-medium {{ $sd }}">
                    {{ ucfirst(str_replace('_', ' ', $demande->statut)) }}
                </span>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">Aucune demande.</p>
            @endforelse
        </div>

        {{-- Notifications récentes --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">🔔 Notifications récentes</h3>
            @forelse($user->notifications as $notif)
            <div class="py-2 border-b border-gray-100 last:border-0">
                <p class="text-sm font-medium text-gray-800">{{ $notif->titre }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $notif->message }}</p>
                <p class="text-xs text-gray-300 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">Aucune notification.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
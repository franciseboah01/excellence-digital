@extends('layouts.admin')
@section('title', 'Clients')
@section('page_title', 'Gestion des Clients')
@section('page_subtitle', 'Liste et gestion des clients inscrits')

@section('content')

{{-- FILTRES --}}
<div class="bg-white rounded-xl shadow p-5 mt-6">
    <form method="GET" action="{{ route('admin.users.index') }}"
        class="grid grid-cols-2 md:grid-cols-5 gap-3">

        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="🔍 Nom, prénom, email..."
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 col-span-2 md:col-span-1">

        <select name="statut"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Tous les statuts</option>
            <option value="actif"    {{ request('statut') == 'actif' ? 'selected' : '' }}>✅ Actif</option>
            <option value="inactif"  {{ request('statut') == 'inactif' ? 'selected' : '' }}>⏸️ Inactif</option>
            <option value="suspendu" {{ request('statut') == 'suspendu' ? 'selected' : '' }}>⛔ Suspendu</option>
        </select>

        <select name="formation_id"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Toutes les formations</option>
            @foreach($formations as $formation)
            <option value="{{ $formation->id }}"
                {{ request('formation_id') == $formation->id ? 'selected' : '' }}>
                {{ $formation->titre }}
            </option>
            @endforeach
        </select>

        <input type="date" name="date_debut" value="{{ request('date_debut') }}"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

        <div class="flex space-x-2">
            <button type="submit"
                class="flex-1 bg-blue-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-900 transition">
                Filtrer
            </button>
            <a href="{{ route('admin.users.index') }}"
                class="flex-1 text-center bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-medium hover:bg-gray-300 transition">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- TABLEAU --}}
<div class="bg-white rounded-xl shadow mt-5 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-blue-900">
            👥 {{ $clients->total() }} client(s) trouvé(s)
        </h3>
        <a href="{{ route('admin.enseignants.index') }}"
            class="text-sm bg-green-100 text-green-700 px-4 py-2 rounded-lg hover:bg-green-200 transition font-medium">
            👨‍🏫 Gérer les enseignants
        </a>
    </div>

    <div class="table-responsive">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Client</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Formations</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Inscription</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($clients as $client)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 text-gray-400">{{ $client->id }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-blue-800 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($client->prenom, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $client->nom_complet }}</p>
                                @if($client->telephone)
                                <p class="text-xs text-gray-400">📲 {{ $client->telephone }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $client->email }}</td>
                    <td class="px-5 py-3">
                        @forelse($client->inscriptions->take(2) as $inscription)
                        @php
                            $badgeInscription = match($inscription->statut) {
                                'valide'     => 'bg-green-100 text-green-700',
                                'en_attente' => 'bg-yellow-100 text-yellow-700',
                                'refuse'     => 'bg-red-100 text-red-700',
                                default      => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="inline-block text-xs px-2 py-0.5 rounded-full {{ $badgeInscription }} mb-1">
                            {{ Str::limit($inscription->formation->titre, 20) }}
                        </span>
                        @empty
                        <span class="text-gray-300 text-xs">Aucune</span>
                        @endforelse
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs">
                        {{ $client->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-5 py-3">
                        @php
                            $statutBadge = match($client->statut) {
                                'actif'    => 'bg-green-100 text-green-700',
                                'suspendu' => 'bg-red-100 text-red-700',
                                default    => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="text-xs px-2 py-1 rounded-full font-medium {{ $statutBadge }}">
                            {{ ucfirst($client->statut) }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.users.show', $client) }}"
                                class="text-xs text-blue-600 hover:underline font-medium">
                                👁️ Détails
                            </a>
                            <form method="POST"
                                action="{{ route('admin.users.toggle-statut', $client) }}">
                                @csrf
                                <button type="submit"
                                    class="text-xs font-medium
                                    {{ $client->statut === 'actif'
                                        ? 'text-red-600 hover:underline'
                                        : 'text-green-600 hover:underline' }}">
                                    {{ $client->statut === 'actif' ? '⛔ Suspendre' : '✅ Réactiver' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $client) }}"
                                onsubmit="return confirm('Supprimer ce client ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-xs text-red-600 hover:underline font-medium">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                        <p class="text-4xl mb-3">👥</p>
                        <p>Aucun client trouvé.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100">
        {{ $clients->links() }}
    </div>
</div>
@endsection
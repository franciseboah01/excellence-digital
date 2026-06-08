@extends('layouts.admin')
@section('title', 'Demandes')
@section('page_title', 'Gestion des Demandes')
@section('page_subtitle', 'Suivi et traitement des demandes de service')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-6">
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-gray-400">
        <p class="text-2xl font-bold text-gray-700">{{ $stats['total'] }}</p>
        <p class="text-gray-500 text-xs mt-1">📋 Total</p>
    </div>
    <div class="bg-yellow-50 rounded-xl shadow p-4 text-center border-l-4 border-yellow-500">
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['en_attente'] }}</p>
        <p class="text-gray-500 text-xs mt-1">⏳ En attente</p>
    </div>
    <div class="bg-blue-50 rounded-xl shadow p-4 text-center border-l-4 border-blue-500">
        <p class="text-2xl font-bold text-blue-600">{{ $stats['en_cours'] }}</p>
        <p class="text-gray-500 text-xs mt-1">🔄 En cours</p>
    </div>
    <div class="bg-green-50 rounded-xl shadow p-4 text-center border-l-4 border-green-500">
        <p class="text-2xl font-bold text-green-600">{{ $stats['terminees'] }}</p>
        <p class="text-gray-500 text-xs mt-1">✅ Terminées</p>
    </div>
    <div class="bg-red-50 rounded-xl shadow p-4 text-center border-l-4 border-red-500">
        <p class="text-2xl font-bold text-red-600">{{ $stats['annulees'] }}</p>
        <p class="text-gray-500 text-xs mt-1">❌ Annulées</p>
    </div>
</div>

{{-- FILTRES --}}
<div class="bg-white rounded-xl shadow p-5 mt-5">
    <form method="GET" action="{{ route('admin.demandes.index') }}"
        class="grid grid-cols-2 md:grid-cols-5 gap-3">

        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="🔍 Nom, email..."
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

        <select name="statut"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Tous les statuts</option>
            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
            <option value="en_cours"   {{ request('statut') == 'en_cours' ? 'selected' : '' }}>🔄 En cours</option>
            <option value="termine"    {{ request('statut') == 'termine' ? 'selected' : '' }}>✅ Terminé</option>
            <option value="annule"     {{ request('statut') == 'annule' ? 'selected' : '' }}>❌ Annulé</option>
        </select>

        <select name="service_id"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Tous les services</option>
            @foreach($services as $service)
            <option value="{{ $service->id }}"
                {{ request('service_id') == $service->id ? 'selected' : '' }}>
                {{ $service->titre }}
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
            <a href="{{ route('admin.demandes.index') }}"
                class="flex-1 text-center bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-medium hover:bg-gray-300 transition">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- TABLEAU --}}
<div class="bg-white rounded-xl shadow mt-5 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-blue-900">
            📋 {{ $demandes->total() }} demande(s) trouvée(s)
        </h3>
    </div>
    <div class="table-responsive">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Client</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Service</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($demandes as $demande)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 text-gray-400">{{ $demande->id }}</td>
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">
                            {{ $demande->user?->nom_complet ?? $demande->nom_visiteur }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $demande->user?->email ?? $demande->email_visiteur }}
                        </p>
                        @if(!$demande->user_id)
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                            Visiteur
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">{{ $demande->service->titre }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $demande->service->icone }}
                            {{ ucfirst(str_replace('_', ' ', $demande->service->categorie)) }}
                        </p>
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs">
                        {{ $demande->created_at->format('d/m/Y') }}<br>
                        <span class="text-gray-300">{{ $demande->created_at->diffForHumans() }}</span>
                    </td>
                    <td class="px-5 py-3">
                        @php
                            $badge = match($demande->statut) {
                                'en_attente' => 'bg-yellow-100 text-yellow-700',
                                'en_cours'   => 'bg-blue-100 text-blue-700',
                                'termine'    => 'bg-green-100 text-green-700',
                                'annule'     => 'bg-red-100 text-red-700',
                                default      => 'bg-gray-100 text-gray-600',
                            };
                            $label = match($demande->statut) {
                                'en_attente' => '⏳ En attente',
                                'en_cours'   => '🔄 En cours',
                                'termine'    => '✅ Terminé',
                                'annule'     => '❌ Annulé',
                                default      => $demande->statut,
                            };
                        @endphp
                        <span class="text-xs px-2 py-1 rounded-full font-medium {{ $badge }}">
                            {{ $label }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <a href="{{ route('admin.demandes.show', $demande) }}"
                            class="text-xs text-blue-600 hover:underline font-medium">
                            👁️ Traiter
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                        <p class="text-4xl mb-3">📋</p>
                        <p>Aucune demande trouvée.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $demandes->links() }}
    </div>
</div>
@endsection
@extends('layouts.admin')
@section('title', 'Paiements')
@section('page_title', 'Gestion des Paiements')
@section('page_subtitle', 'Suivi et enregistrement des paiements')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mt-6">
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-gray-400">
        <p class="text-xl font-bold text-gray-700">{{ $stats['total'] }}</p>
        <p class="text-gray-500 text-xs mt-1">📋 Total</p>
    </div>
    <div class="bg-yellow-50 rounded-xl shadow p-4 text-center border-l-4 border-yellow-500">
        <p class="text-xl font-bold text-yellow-600">{{ $stats['en_attente'] }}</p>
        <p class="text-gray-500 text-xs mt-1">⏳ En attente</p>
    </div>
    <div class="bg-blue-50 rounded-xl shadow p-4 text-center border-l-4 border-blue-500">
        <p class="text-xl font-bold text-blue-600">{{ $stats['partiel'] }}</p>
        <p class="text-gray-500 text-xs mt-1">⚠️ Partiel</p>
    </div>
    <div class="bg-green-50 rounded-xl shadow p-4 text-center border-l-4 border-green-500">
        <p class="text-xl font-bold text-green-600">{{ $stats['complete'] }}</p>
        <p class="text-gray-500 text-xs mt-1">✅ Complets</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-purple-500 md:col-span-2">
        <p class="text-lg font-bold text-purple-700">
            {{ number_format($stats['total_percu'], 0, ',', ' ') }} FCFA
        </p>
        <p class="text-gray-500 text-xs mt-1">💰 Total perçu / {{ number_format($stats['total_attendu'], 0, ',', ' ') }} FCFA</p>
        <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
            @php
                $pct = $stats['total_attendu'] > 0
                    ? min(100, ($stats['total_percu'] / $stats['total_attendu']) * 100)
                    : 0;
            @endphp
            <div class="bg-purple-600 h-1.5 rounded-full" style="width:{{ $pct }}%"></div>
        </div>
    </div>
</div>

{{-- FILTRES --}}
<div class="bg-white rounded-xl shadow p-5 mt-5">
    <form method="GET" action="{{ route('admin.paiements.index') }}"
        class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">

        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="🔍 Référence, nom..."
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 col-span-2 md:col-span-1">

        <select name="statut"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Tous statuts</option>
            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
            <option value="partiel"    {{ request('statut') == 'partiel' ? 'selected' : '' }}>⚠️ Partiel</option>
            <option value="complete"   {{ request('statut') == 'complete' ? 'selected' : '' }}>✅ Complet</option>
        </select>

        <select name="mode_paiement"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Tous modes</option>
            <option value="especes"      {{ request('mode_paiement') == 'especes' ? 'selected' : '' }}>💵 Espèces</option>
            <option value="mobile_money" {{ request('mode_paiement') == 'mobile_money' ? 'selected' : '' }}>📱 Mobile Money</option>
            <option value="virement"     {{ request('mode_paiement') == 'virement' ? 'selected' : '' }}>🏦 Virement</option>
        </select>

        <input type="date" name="date_debut" value="{{ request('date_debut') }}"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

        <input type="date" name="date_fin" value="{{ request('date_fin') }}"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

        <div class="flex space-x-2">
            <button type="submit"
                class="flex-1 bg-blue-800 text-white rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-900 transition">
                Filtrer
            </button>
            <a href="{{ route('admin.paiements.index') }}"
                class="flex-1 text-center bg-gray-200 text-gray-700 rounded-lg px-3 py-2 text-sm hover:bg-gray-300 transition">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- BOUTON AJOUTER --}}
<div class="flex justify-end mt-4">
    <a href="{{ route('admin.paiements.create') }}"
        class="bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
        ➕ Nouveau paiement
    </a>
</div>

{{-- TABLEAU --}}
<div class="bg-white rounded-xl shadow mt-4 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Référence</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Client</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Objet</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Montant</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Progression</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($paiements as $paiement)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <p class="font-mono text-xs font-bold text-blue-800">{{ $paiement->reference }}</p>
                        <p class="text-xs text-gray-300">{{ $paiement->created_at->format('d/m/Y') }}</p>
                    </td>
                    <td class="px-5 py-4">
                        <p class="font-medium text-gray-800">
                            {{ $paiement->user->prenom }} {{ $paiement->user->nom }}
                        </p>
                    </td>
                    <td class="px-5 py-4">
                        <p class="text-xs text-gray-600">
                            @if($paiement->formation)
                                🎓 {{ Str::limit($paiement->formation->titre, 25) }}
                            @elseif($paiement->service)
                                💼 {{ Str::limit($paiement->service->titre, 25) }}
                            @else
                                —
                            @endif
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}
                        </p>
                    </td>
                    <td class="px-5 py-4">
                        <p class="font-semibold text-gray-800">
                            {{ number_format($paiement->montant_paye, 0, ',', ' ') }}
                            <span class="text-gray-400 font-normal text-xs">
                                / {{ number_format($paiement->montant_total, 0, ',', ' ') }} FCFA
                            </span>
                        </p>
                    </td>
                    <td class="px-5 py-4">
                        <div class="w-24">
                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                <span>{{ $paiement->pourcentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="h-2 rounded-full
                                    {{ $paiement->pourcentage == 100 ? 'bg-green-500' : ($paiement->pourcentage > 0 ? 'bg-blue-500' : 'bg-gray-300') }}"
                                    style="width:{{ $paiement->pourcentage }}%">
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $badge = match($paiement->statut) {
                                'complete'   => 'bg-green-100 text-green-700',
                                'partiel'    => 'bg-blue-100 text-blue-700',
                                default      => 'bg-yellow-100 text-yellow-700',
                            };
                            $label = match($paiement->statut) {
                                'complete'   => '✅ Complet',
                                'partiel'    => '⚠️ Partiel',
                                default      => '⏳ En attente',
                            };
                        @endphp
                        <span class="text-xs px-2 py-1 rounded-full font-medium {{ $badge }}">
                            {{ $label }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.paiements.show', $paiement) }}"
                                class="text-xs text-blue-600 hover:underline font-medium">
                                👁️ Voir
                            </a>
                            <a href="{{ route('admin.paiements.recu', $paiement) }}"
                                class="text-xs text-green-600 hover:underline font-medium">
                                📄 Reçu
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                        <p class="text-4xl mb-3">💰</p>
                        <p>Aucun paiement enregistré.</p>
                        <a href="{{ route('admin.paiements.create') }}"
                            class="inline-block mt-4 bg-blue-800 text-white px-5 py-2 rounded-lg text-sm">
                            Enregistrer le premier paiement
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $paiements->links() }}
    </div>
</div>
@endsection
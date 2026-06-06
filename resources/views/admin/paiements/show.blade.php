@extends('layouts.admin')
@section('title', 'Paiement ' . $paiement->reference)
@section('page_title', 'Détail du Paiement')

@section('content')
<div class="mt-4 flex justify-between items-center">
    <a href="{{ route('admin.paiements.index') }}"
        class="text-blue-600 hover:underline text-sm">← Retour</a>
    <a href="{{ route('admin.paiements.recu', $paiement) }}"
        class="bg-green-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">
        📄 Télécharger le reçu PDF
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">

    {{-- INFOS --}}
    <div class="lg:col-span-2 space-y-5">

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">📋 Informations du paiement</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Référence</p>
                    <p class="font-mono font-bold text-blue-800">{{ $paiement->reference }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Date</p>
                    <p class="font-semibold">{{ $paiement->date_paiement?->format('d/m/Y') ?? now()->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Client</p>
                    <p class="font-semibold">{{ $paiement->user->prenom }} {{ $paiement->user->nom }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Mode</p>
                    <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Objet</p>
                    <p class="font-semibold">
                        @if($paiement->formation) 🎓 {{ $paiement->formation->titre }}
                        @elseif($paiement->service) 💼 {{ $paiement->service->titre }}
                        @else — @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Enregistré par</p>
                    <p class="font-semibold">{{ $paiement->enregistrePar?->prenom ?? '—' }}</p>
                </div>
            </div>
            @if($paiement->notes)
            <div class="mt-4 bg-gray-50 rounded-lg p-3 text-sm text-gray-600">
                📝 {{ $paiement->notes }}
            </div>
            @endif
        </div>

        {{-- Mise à jour --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">🔄 Mettre à jour le paiement</h3>
            <form method="POST" action="{{ route('admin.paiements.update', $paiement) }}">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Montant payé (FCFA)
                        </label>
                        <input type="number" name="montant_paye"
                            value="{{ $paiement->montant_paye }}"
                            min="0" max="{{ $paiement->montant_total }}" step="100" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Mode de paiement
                        </label>
                        <select name="mode_paiement" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="especes"      {{ $paiement->mode_paiement == 'especes' ? 'selected' : '' }}>💵 Espèces</option>
                            <option value="mobile_money" {{ $paiement->mode_paiement == 'mobile_money' ? 'selected' : '' }}>📱 Mobile Money</option>
                            <option value="virement"     {{ $paiement->mode_paiement == 'virement' ? 'selected' : '' }}>🏦 Virement</option>
                            <option value="autre"        {{ $paiement->mode_paiement == 'autre' ? 'selected' : '' }}>🔄 Autre</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $paiement->notes }}</textarea>
                </div>
                <button type="submit"
                    class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition">
                    💾 Mettre à jour
                </button>
            </form>
        </div>
    </div>

    {{-- PANEL STATUT --}}
    <div class="space-y-5">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">💰 Récapitulatif</h3>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Total</span>
                    <span class="font-bold">{{ number_format($paiement->montant_total, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Payé</span>
                    <span class="font-bold text-green-600">{{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</span>
                </div>
                @if($paiement->montant_restant > 0)
                <div class="flex justify-between">
                    <span class="text-gray-500">Restant</span>
                    <span class="font-bold text-red-600">{{ number_format($paiement->montant_restant, 0, ',', ' ') }} FCFA</span>
                </div>
                @endif
            </div>

            <div class="mt-4">
                <div class="flex justify-between text-xs text-gray-400 mb-1">
                    <span>Progression</span>
                    <span>{{ $paiement->pourcentage }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="h-3 rounded-full transition-all
                        {{ $paiement->pourcentage == 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                        style="width:{{ $paiement->pourcentage }}%">
                    </div>
                </div>
            </div>

            <div class="mt-4 text-center">
                @php
                    $badge = match($paiement->statut) {
                        'complete'   => 'bg-green-100 text-green-700 border-green-300',
                        'partiel'    => 'bg-blue-100 text-blue-700 border-blue-300',
                        default      => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                    };
                    $label = match($paiement->statut) {
                        'complete'   => '✅ Paiement complet',
                        'partiel'    => '⚠️ Paiement partiel',
                        default      => '⏳ En attente',
                    };
                @endphp
                <span class="inline-block border-2 rounded-xl px-4 py-2 text-sm font-bold {{ $badge }}">
                    {{ $label }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
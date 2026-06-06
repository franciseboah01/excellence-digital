@extends('layouts.admin')
@section('title', 'Nouveau Paiement')
@section('page_title', 'Enregistrer un Paiement')

@section('content')
<div class="mt-6 max-w-2xl">
    <a href="{{ route('admin.paiements.index') }}"
        class="text-blue-600 hover:underline text-sm">← Retour</a>

    <div class="bg-white rounded-xl shadow p-8 mt-4">
        <form method="POST" action="{{ route('admin.paiements.store') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Client *</label>
                <select name="user_id" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Choisir un client --</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('user_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->prenom }} {{ $client->nom }} — {{ $client->email }}
                    </option>
                    @endforeach
                </select>
                @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Formation concernée
                    </label>
                    <select name="formation_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Aucune --</option>
                        @foreach($formations as $formation)
                        <option value="{{ $formation->id }}" {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                            {{ $formation->titre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Service concerné
                    </label>
                    <select name="service_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Aucun --</option>
                        @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->titre }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Montant total (FCFA) *
                    </label>
                    <input type="number" name="montant_total" value="{{ old('montant_total') }}"
                        required min="1" step="100"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ex : 50000">
                    @error('montant_total') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Montant payé (FCFA) *
                    </label>
                    <input type="number" name="montant_paye" value="{{ old('montant_paye', 0) }}"
                        required min="0" step="100"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('montant_paye') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Mode de paiement *
                    </label>
                    <select name="mode_paiement" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="especes"      {{ old('mode_paiement') == 'especes' ? 'selected' : '' }}>💵 Espèces</option>
                        <option value="mobile_money" {{ old('mode_paiement') == 'mobile_money' ? 'selected' : '' }}>📱 Mobile Money</option>
                        <option value="virement"     {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>🏦 Virement</option>
                        <option value="autre"        {{ old('mode_paiement') == 'autre' ? 'selected' : '' }}>🔄 Autre</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Date de paiement
                    </label>
                    <input type="date" name="date_paiement"
                        value="{{ old('date_paiement', now()->format('Y-m-d')) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
            </div>

            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition">
                💾 Enregistrer le paiement
            </button>
        </form>
    </div>
</div>
@endsection
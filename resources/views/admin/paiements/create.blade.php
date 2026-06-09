@extends('layouts.admin')
@section('title', 'Nouveau Paiement')
@section('page_title', '💰 Enregistrer un Paiement')

@section('content')
<div class="mt-6 max-w-2xl">
    <a href="{{ route('admin.paiements.index') }}"
        class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
        style="color: var(--edc-primary-light);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Retour</span>
    </a>

    <div class="edc-card p-6 sm:p-8 mt-4">
        <form method="POST" action="{{ route('admin.paiements.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="edc-label">Client *</label>
                <select name="user_id" required class="edc-select">
                    <option value="">-- Choisir un client --</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('user_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->prenom }} {{ $client->nom }} — {{ $client->email }}
                    </option>
                    @endforeach
                </select>
                @error('user_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="edc-label">Formation concernée</label>
                    <select name="formation_id" class="edc-select">
                        <option value="">-- Aucune --</option>
                        @foreach($formations as $formation)
                        <option value="{{ $formation->id }}" {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                            {{ $formation->titre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="edc-label">Service concerné</label>
                    <select name="service_id" class="edc-select">
                        <option value="">-- Aucun --</option>
                        @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->titre }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="edc-label">Montant total (FCFA) *</label>
                    <input type="number" name="montant_total" value="{{ old('montant_total') }}"
                        required min="1" step="100" class="edc-input" placeholder="Ex : 50000">
                    @error('montant_total') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="edc-label">Montant payé (FCFA) *</label>
                    <input type="number" name="montant_paye" value="{{ old('montant_paye', 0) }}"
                        required min="0" step="100" class="edc-input">
                    @error('montant_paye') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="edc-label">Mode de paiement *</label>
                    <select name="mode_paiement" required class="edc-select">
                        <option value="especes"      {{ old('mode_paiement') == 'especes' ? 'selected' : '' }}>💵 Espèces</option>
                        <option value="mobile_money" {{ old('mode_paiement') == 'mobile_money' ? 'selected' : '' }}>📱 Mobile Money</option>
                        <option value="virement"     {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>🏦 Virement</option>
                        <option value="autre"        {{ old('mode_paiement') == 'autre' ? 'selected' : '' }}>🔄 Autre</option>
                    </select>
                </div>
                <div>
                    <label class="edc-label">Date de paiement</label>
                    <input type="date" name="date_paiement" value="{{ old('date_paiement', now()->format('Y-m-d')) }}"
                        class="edc-input">
                </div>
            </div>

            <div>
                <label class="edc-label">Notes</label>
                <textarea name="notes" rows="3" class="edc-input"
                    placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn-primary w-full">💾 Enregistrer le paiement</button>
        </form>
    </div>
</div>
@endsection
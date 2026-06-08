@extends('layouts.client')
@section('title', 'Nouvelle demande')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('client.dashboard') }}" class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
            style="color: var(--edc-primary-light);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retour au dashboard</span>
        </a>
        <h1 class="text-xl sm:text-2xl font-extrabold mt-2" style="color: var(--edc-text-primary);">📋 Nouvelle demande de service</h1>
        <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">Remplissez le formulaire, nous vous répondons sous 24h</p>
    </div>

    <div class="edc-card p-6 sm:p-8">
        <form method="POST" action="{{ route('client.demande.store') }}" class="space-y-5">
            @csrf

            {{-- Nom complet (pré-rempli, non modifiable) --}}
            <div>
                <label class="edc-label">Nom complet</label>
                <input type="text" value="{{ auth()->user()->nom_complet }}"
                    class="edc-input" style="opacity: 0.6;" disabled>
                <p class="text-xs mt-1" style="color: var(--edc-text-muted);">Connecté en tant que {{ auth()->user()->nom_complet }}</p>
            </div>

            {{-- Email (pré-rempli, non modifiable) --}}
            <div>
                <label class="edc-label">Email</label>
                <input type="email" value="{{ auth()->user()->email }}"
                    class="edc-input" style="opacity: 0.6;" disabled>
            </div>

            {{-- Téléphone (pré-rempli, modifiable) --}}
            <div>
                <label class="edc-label">Téléphone / WhatsApp</label>
                <input type="text" name="telephone_visiteur" value="{{ old('telephone_visiteur', auth()->user()->telephone) }}"
                    class="edc-input" placeholder="+225 07 00 00 00 00">
            </div>

            {{-- Service souhaité --}}
            <div>
                <label class="edc-label">Service souhaité *</label>
                <select name="service_id" class="edc-select" required>
                    <option value="">-- Choisir un service --</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id', request('service')) == $service->id ? 'selected' : '' }}>
                        {{ $service->icone ?? '⚙️' }} {{ $service->titre }}
                    </option>
                    @endforeach
                </select>
                @error('service_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Message --}}
            <div>
                <label class="edc-label">Message / Détails</label>
                <textarea name="message" rows="4" class="edc-input"
                    placeholder="Décrivez votre besoin en détail...">{{ old('message') }}</textarea>
                @error('message') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary w-full">
                📩 Envoyer ma demande
            </button>
        </form>
    </div>
</div>
@endsection
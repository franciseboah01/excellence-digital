@extends('layouts.public')
@section('title', 'Demande de service — Excellence Digital Center')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16">
    <div class="text-center mb-10">
        <h1 class="text-section">Faire une demande de service</h1>
        <p class="section-subtitle">Remplissez le formulaire, nous vous répondons sous 24h</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">
        <span>✅</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="edc-card p-8">
        <form method="POST" action="{{ route('demande.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="edc-label">Votre nom complet *</label>
                <input type="text" name="nom_visiteur" value="{{ old('nom_visiteur') }}"
                    class="edc-input" placeholder="Ex : Koné Ibrahima" required>
                @error('nom_visiteur') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="edc-label">Email *</label>
                <input type="email" name="email_visiteur" value="{{ old('email_visiteur') }}"
                    class="edc-input" placeholder="votre@email.com" required>
                @error('email_visiteur') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="edc-label">Téléphone / WhatsApp</label>
                <input type="text" name="telephone_visiteur" value="{{ old('telephone_visiteur') }}"
                    class="edc-input" placeholder="+225 07 00 00 00 00">
            </div>

            <div>
                <label class="edc-label">Service souhaité *</label>
                <select name="service_id" class="edc-select" required>
                    <option value="">-- Choisir un service --</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}"
                        {{ (old('service_id') == $service->id || request('service') == $service->id) ? 'selected' : '' }}>
                        {{ $service->titre }}
                    </option>
                    @endforeach
                </select>
                @error('service_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="edc-label">Message / Détails</label>
                <textarea name="message" rows="4" class="edc-input"
                    placeholder="Décrivez votre besoin en détail...">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="btn-primary w-full text-lg">
                📩 Envoyer ma demande
            </button>
        </form>
    </div>

    <p class="text-center text-sm mt-6" style="color: var(--edc-text-muted);">
        Ou contactez-nous directement sur
        <a href="https://wa.me/2250748746140" class="font-medium hover:underline"
            style="color: var(--edc-secondary);" target="_blank">
            WhatsApp 💬
        </a>
    </p>
</div>
@endsection
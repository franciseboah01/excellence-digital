@extends('layouts.client')
@section('title', 'Mon Profil')

@section('content')
<div class="mb-6">
    <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">👤 Mon Profil</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Infos personnelles --}}
    <div class="edc-card p-6">
        <h2 class="text-lg font-bold mb-5" style="color: var(--edc-text-primary);">Informations personnelles</h2>

        <form method="POST" action="{{ route('client.profil.update') }}" enctype="multipart/form-data">
            @csrf

            {{-- Avatar --}}
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold overflow-hidden"
                    style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}?t={{ auth()->user()->updated_at->timestamp }}" 
                            alt="Avatar" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <label class="cursor-pointer text-sm font-medium hover:underline" style="color: var(--edc-primary-light);">
                        Changer la photo
                        <input type="file" name="avatar" accept="image/*" class="hidden">
                    </label>
                    <p class="text-xs mt-1" style="color: var(--edc-text-muted);">JPG, PNG — max 2MB</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="edc-label">Prénom</label>
                    <input type="text" name="prenom" value="{{ auth()->user()->prenom }}" class="edc-input">
                    @error('prenom') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="edc-label">Nom</label>
                    <input type="text" name="nom" value="{{ auth()->user()->nom }}" class="edc-input">
                    @error('nom') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="edc-label">Email</label>
                <input type="email" value="{{ auth()->user()->email }}"
                    class="edc-input cursor-not-allowed"
                    style="opacity: 0.5;" disabled>
            </div>

            <div class="mb-6">
                <label class="edc-label">Téléphone / WhatsApp</label>
                <input type="text" name="telephone" value="{{ auth()->user()->telephone }}"
                    class="edc-input" placeholder="+225 07 00 00 00 00">
            </div>

            <button type="submit" class="btn-primary w-full">
                💾 Enregistrer les modifications
            </button>
        </form>
    </div>

    {{-- Changer mot de passe --}}
    <div class="edc-card p-6">
        <h2 class="text-lg font-bold mb-5" style="color: var(--edc-text-primary);">🔒 Changer le mot de passe</h2>

        <form method="POST" action="{{ route('client.password.update') }}">
            @csrf

            <div class="mb-4">
                <label class="edc-label">Mot de passe actuel</label>
                <input type="password" name="current_password" class="edc-input">
                @error('current_password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="edc-label">Nouveau mot de passe</label>
                <input type="password" name="password" class="edc-input">
                @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="edc-label">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" class="edc-input">
            </div>

            <button type="submit" class="btn-tertiary w-full">
                🔑 Changer le mot de passe
            </button>
        </form>
    </div>
</div>
@endsection
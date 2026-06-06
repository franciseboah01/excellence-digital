@extends('layouts.enseignant')
@section('title', 'Mon Profil')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-blue-900">👤 Mon Profil</h1>
    <p class="text-gray-500 mt-1 text-sm">Gérez vos informations personnelles</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- INFOS PERSONNELLES --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold text-blue-900 mb-5">Informations personnelles</h2>

        <form method="POST" action="{{ route('enseignant.profil.update') }}"
            enctype="multipart/form-data">
            @csrf

            {{-- Avatar --}}
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-16 h-16 rounded-full bg-green-700 flex items-center justify-center text-white text-2xl font-bold overflow-hidden flex-shrink-0">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                            class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <label class="cursor-pointer text-sm text-blue-700 font-medium hover:underline">
                        Changer la photo
                        <input type="file" name="avatar" accept="image/*" class="hidden">
                    </label>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG — max 2MB</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Prénom</label>
                    <input type="text" name="prenom"
                        value="{{ auth()->user()->prenom }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @error('prenom')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nom</label>
                    <input type="text" name="nom"
                        value="{{ auth()->user()->nom }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @error('nom')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input type="email" value="{{ auth()->user()->email }}" disabled
                    class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-400 cursor-not-allowed">
                <p class="text-xs text-gray-400 mt-1">L'email ne peut pas être modifié.</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Téléphone / WhatsApp
                </label>
                <input type="text" name="telephone"
                    value="{{ auth()->user()->telephone }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="+225 07 00 00 00 00">
            </div>

            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-semibold hover:bg-blue-900 transition">
                💾 Enregistrer les modifications
            </button>
        </form>
    </div>

    {{-- MOT DE PASSE --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold text-blue-900 mb-5">🔒 Changer le mot de passe</h2>

        <form method="POST" action="{{ route('enseignant.password.update') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Mot de passe actuel
                </label>
                <input type="password" name="current_password" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @error('current_password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Nouveau mot de passe
                </label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Confirmer le mot de passe
                </label>
                <input type="password" name="password_confirmation" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <button type="submit"
                class="w-full bg-gray-800 text-white py-3 rounded-xl font-semibold hover:bg-gray-900 transition">
                🔑 Changer le mot de passe
            </button>
        </form>
    </div>
</div>
@endsection
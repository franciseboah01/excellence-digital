@extends('layouts.admin')
@section('title', 'Enseignants')
@section('page_title', 'Gestion des Enseignants')
@section('page_subtitle', 'Ajout, modification et supervision des enseignants')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

    {{-- FORMULAIRE AJOUT --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold text-blue-900 mb-5">➕ Ajouter un enseignant</h2>

        <form method="POST" action="{{ route('admin.enseignants.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Prénom *</label>
                <input type="text" name="prenom" value="{{ old('prenom') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('prenom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nom *</label>
                <input type="text" name="nom" value="{{ old('nom') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Téléphone</label>
                <input type="text" name="telephone" value="{{ old('telephone') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Mot de passe *</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Confirmer le mot de passe *
                </label>
                <input type="password" name="password_confirmation" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition">
                ➕ Créer l'enseignant
            </button>
        </form>
    </div>

    {{-- LISTE ENSEIGNANTS --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-blue-900">
                    👨‍🏫 {{ $enseignants->count() }} enseignant(s)
                </h2>
            </div>

            @forelse($enseignants as $enseignant)
            <div class="p-5 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition"
                x-data="{ editOpen: false }">

                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-green-700 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($enseignant->prenom, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $enseignant->nom_complet }}</p>
                            <p class="text-xs text-gray-400">{{ $enseignant->email }}</p>
                            @if($enseignant->telephone)
                            <p class="text-xs text-gray-400">📲 {{ $enseignant->telephone }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">
                            📚 {{ $enseignant->ressources_count }} ressource(s)
                        </span>
                        @php
                            $se = match($enseignant->statut) {
                                'actif'    => 'bg-green-100 text-green-700',
                                'suspendu' => 'bg-red-100 text-red-700',
                                default    => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="text-xs px-2 py-1 rounded-full {{ $se }}">
                            {{ ucfirst($enseignant->statut) }}
                        </span>
                        <button @click="editOpen = !editOpen"
                            class="text-xs text-blue-600 hover:underline font-medium">
                            ✏️ Modifier
                        </button>
                        <form method="POST"
                            action="{{ route('admin.users.toggle-statut', $enseignant) }}">
                            @csrf
                            <button type="submit"
                                class="text-xs font-medium
                                {{ $enseignant->statut === 'actif'
                                    ? 'text-red-600 hover:underline'
                                    : 'text-green-600 hover:underline' }}">
                                {{ $enseignant->statut === 'actif' ? '⛔' : '✅' }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Formulaire de modification inline --}}
                <div x-show="editOpen" x-cloak class="mt-4 bg-gray-50 rounded-xl p-4">
                    <form method="POST"
                        action="{{ route('admin.enseignants.update', $enseignant) }}">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="text-xs font-semibold text-gray-600 mb-1 block">Prénom</label>
                                <input type="text" name="prenom"
                                    value="{{ $enseignant->prenom }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 mb-1 block">Nom</label>
                                <input type="text" name="nom"
                                    value="{{ $enseignant->nom }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-xs font-semibold text-gray-600 mb-1 block">Téléphone</label>
                            <input type="text" name="telephone"
                                value="{{ $enseignant->telephone }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit"
                            class="bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
                            💾 Enregistrer
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">
                <p class="text-4xl mb-3">👨‍🏫</p>
                <p>Aucun enseignant enregistré.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
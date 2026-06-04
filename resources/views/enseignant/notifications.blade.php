@extends('layouts.enseignant')
@section('title', 'Envoyer une notification')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-blue-900">🔔 Envoyer une notification</h1>
    <p class="text-gray-500 mt-1">Notifiez les apprenants de vos formations</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Formulaire --}}
    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('enseignant.notifications.envoyer') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Formation ciblée *
                </label>
                <select name="formation_id" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Choisir une formation --</option>
                    @foreach($formations as $formation)
                    <option value="{{ $formation->id }}" {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                        {{ $formation->titre }}
                        ({{ $formation->inscriptions->count() }} apprenant(s))
                    </option>
                    @endforeach
                </select>
                @error('formation_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Type *</label>
                <select name="type" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="info"    {{ old('type') == 'info' ? 'selected' : '' }}>📢 Information</option>
                    <option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>✅ Bonne nouvelle</option>
                    <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>⚠️ Avertissement</option>
                </select>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Titre *</label>
                <input type="text" name="titre" value="{{ old('titre') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ex : Nouveau cours disponible !">
                @error('titre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Message *</label>
                <textarea name="message" rows="4" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Écrivez votre message ici...">{{ old('message') }}</textarea>
                @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                class="w-full bg-blue-800 text-white py-4 rounded-xl font-bold hover:bg-blue-900 transition">
                📤 Envoyer la notification
            </button>
        </form>
    </div>

    {{-- SECTION EMAIL --}}
    <div class="bg-white rounded-xl shadow p-6 mt-6">
        <h2 class="text-lg font-bold text-blue-900 mb-5">✉️ Envoyer un email aux apprenants</h2>

        <form method="POST" action="{{ route('enseignant.emails.envoyer') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Formation *</label>
                <select name="formation_id" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Choisir --</option>
                    @foreach($formations as $formation)
                    <option value="{{ $formation->id }}">{{ $formation->titre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Sujet *</label>
                <input type="text" name="sujet" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ex : Nouveau cours disponible">
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Message *</label>
                <textarea name="message" rows="4" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Votre message..."></textarea>
            </div>

            <button type="submit"
                class="w-full bg-green-700 text-white py-3 rounded-xl font-bold hover:bg-green-800 transition">
                📧 Envoyer l'email
            </button>
        </form>
    </div>

    {{-- Apprenants par formation --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold text-blue-900 mb-5">👥 Apprenants par formation</h2>
        @forelse($formations as $formation)
        <div class="mb-5 border-b border-gray-100 pb-4 last:border-0">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-800">{{ $formation->titre }}</h3>
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                    {{ $formation->inscriptions->count() }} inscrits
                </span>
            </div>
            @if($formation->inscriptions->count())
            <div class="flex flex-wrap gap-2">
                @foreach($formation->inscriptions->take(5) as $inscription)
                <div class="w-8 h-8 rounded-full bg-blue-800 flex items-center justify-center text-white text-xs font-bold"
                    title="{{ $inscription->user->nom_complet }}">
                    {{ strtoupper(substr($inscription->user->prenom, 0, 1)) }}
                </div>
                @endforeach
                @if($formation->inscriptions->count() > 5)
                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-xs font-bold">
                    +{{ $formation->inscriptions->count() - 5 }}
                </div>
                @endif
            </div>
            @else
            <p class="text-gray-400 text-sm">Aucun apprenant inscrit.</p>
            @endif
        </div>
        @empty
        <p class="text-gray-400 text-sm text-center py-4">Aucune formation assignée.</p>
        @endforelse
    </div>
</div>
@endsection
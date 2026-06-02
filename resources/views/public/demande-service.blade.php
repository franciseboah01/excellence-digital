@extends('layouts.public')
@section('title', 'Demande de service — Excellence Digital Center')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-blue-900">Faire une demande de service</h1>
        <p class="text-gray-500 mt-2">Remplissez le formulaire, nous vous répondons sous 24h</p>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl p-4 mb-6 text-center font-medium">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg p-8">
        <form method="POST" action="{{ route('demande.store') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Votre nom complet *</label>
                <input type="text" name="nom_visiteur" value="{{ old('nom_visiteur') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ex : Koné Ibrahima" required>
                @error('nom_visiteur') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email *</label>
                <input type="email" name="email_visiteur" value="{{ old('email_visiteur') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="votre@email.com" required>
                @error('email_visiteur') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Téléphone / WhatsApp</label>
                <input type="text" name="telephone_visiteur" value="{{ old('telephone_visiteur') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="+225 07 00 00 00 00">
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Service souhaité *</label>
                <select name="service_id"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Choisir un service --</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}"
                        {{ (old('service_id') == $service->id || request('service') == $service->id) ? 'selected' : '' }}>
                        {{ $service->titre }}
                    </option>
                    @endforeach
                </select>
                @error('service_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Message / Détails</label>
                <textarea name="message" rows="4"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Décrivez votre besoin en détail...">{{ old('message') }}</textarea>
            </div>

            <button type="submit"
                class="w-full bg-blue-800 text-white py-4 rounded-xl font-bold text-lg hover:bg-blue-900 transition">
                📩 Envoyer ma demande
            </button>
        </form>
    </div>

    <p class="text-center text-gray-400 text-sm mt-6">
        Ou contactez-nous directement sur
        <a href="https://wa.me/2250748746140" class="text-green-600 font-medium hover:underline" target="_blank">
            WhatsApp 💬
        </a>
    </p>
</div>
@endsection
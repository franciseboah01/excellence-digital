@extends('layouts.public')
@section('title', 'Contact — Excellence Digital Center')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-blue-900">Contactez-nous</h1>
        <p class="text-gray-500 mt-3">Nous vous répondons dans les plus brefs délais</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        {{-- Formulaire --}}
        <div class="bg-white rounded-2xl shadow-lg p-8">

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl p-4 mb-6 text-center font-medium">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('contact.send') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nom complet *</label>
                    <input type="text" name="nom" value="{{ old('nom') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Votre nom" required>
                    @error('nom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="votre@email.com" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sujet *</label>
                    <input type="text" name="sujet" value="{{ old('sujet') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Objet de votre message" required>
                    @error('sujet') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Message *</label>
                    <textarea name="message" rows="5"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Votre message..." required>{{ old('message') }}</textarea>
                    @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                    class="w-full bg-blue-800 text-white py-4 rounded-xl font-bold hover:bg-blue-900 transition">
                    📩 Envoyer le message
                </button>
            </form>
        </div>

        {{-- Infos de contact --}}
        <div class="space-y-6">
            <div class="bg-blue-50 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-blue-900 mb-4">📍 Nos coordonnées</h3>
                <ul class="space-y-4 text-gray-700">
                    <li class="flex items-start space-x-3">
                        <span class="text-2xl">📍</span>
                        <div>
                            <p class="font-semibold">Localisation</p>
                            <p class="text-gray-500">Korhogo / Sirasso, Côte d'Ivoire</p>
                        </div>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="text-2xl">📲</span>
                        <div>
                            <p class="font-semibold">WhatsApp</p>
                            <a href="https://wa.me/2250748746140"
                                class="text-green-600 hover:underline font-medium" target="_blank">
                                +225 07 48 74 61 40
                            </a>
                        </div>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="text-2xl">✉️</span>
                        <div>
                            <p class="font-semibold">Email</p>
                            <p class="text-gray-500">contact@excellencedigital.ci</p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="bg-blue-800 text-white rounded-2xl p-6 text-center">
                <p class="text-xl font-bold mb-2">Besoin urgent ?</p>
                <p class="text-blue-200 mb-4 text-sm">Contactez-nous directement sur WhatsApp</p>
                <a href="https://wa.me/2250748746140" target="_blank"
                    class="bg-green-500 text-white font-bold px-6 py-3 rounded-full hover:bg-green-600 transition inline-block">
                    💬 WhatsApp maintenant
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
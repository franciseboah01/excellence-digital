@extends('layouts.public')
@section('title', 'Contact — Excellence Digital Center')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="text-section">Contactez-nous</h1>
        <p class="section-subtitle">Nous vous répondons dans les plus brefs délais</p>
    </div>

    <div class="grid-responsive-2 gap-10">
        {{-- Formulaire --}}
        <div class="edc-card p-8">

            @if(session('success'))
            <div class="alert alert-success mb-6">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('contact.send') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="edc-label">Nom complet *</label>
                    <input type="text" name="nom" value="{{ old('nom') }}"
                        class="edc-input" placeholder="Votre nom" required>
                    @error('nom') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="edc-label">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="edc-input" placeholder="votre@email.com" required>
                    @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="edc-label">Sujet *</label>
                    <input type="text" name="sujet" value="{{ old('sujet') }}"
                        class="edc-input" placeholder="Objet de votre message" required>
                    @error('sujet') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="edc-label">Message *</label>
                    <textarea name="message" rows="5" class="edc-input"
                        placeholder="Votre message..." required>{{ old('message') }}</textarea>
                    @error('message') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn-primary w-full">
                    📩 Envoyer le message
                </button>
            </form>
        </div>

        {{-- Infos de contact --}}
        <div class="space-y-6">
            <div class="edc-card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">📍 Nos coordonnées</h3>
                <ul class="space-y-4">
                    <li class="flex items-start space-x-3">
                        <span class="text-2xl">📍</span>
                        <div>
                            <p class="font-semibold" style="color: var(--edc-text-primary);">Localisation</p>
                            <p style="color: var(--edc-text-muted);">Korhogo / Sirasso, Côte d'Ivoire</p>
                        </div>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="text-2xl">📲</span>
                        <div>
                            <p class="font-semibold" style="color: var(--edc-text-primary);">WhatsApp</p>
                            <a href="https://wa.me/2250748746140"
                                class="font-medium hover:underline" style="color: var(--edc-secondary);" target="_blank">
                                +225 07 48 74 61 40
                            </a>
                        </div>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="text-2xl">✉️</span>
                        <div>
                            <p class="font-semibold" style="color: var(--edc-text-primary);">Email</p>
                            <p style="color: var(--edc-text-muted);">contact@excellencedigital.ci</p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="edc-card p-6 text-center" style="background: linear-gradient(135deg, var(--edc-primary-dark), var(--edc-primary));">
                <p class="text-xl font-bold mb-2" style="color: #fff;">Besoin urgent ?</p>
                <p class="text-sm mb-4" style="color: rgba(255,255,255,0.7);">Contactez-nous directement sur WhatsApp</p>
                <a href="https://wa.me/2250748746140" target="_blank"
                    class="btn-success">
                    💬 WhatsApp maintenant
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
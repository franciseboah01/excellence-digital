@extends('layouts.public')
@section('title', 'Contact — ' . \App\Models\Configuration::get('site_nom', 'Excellence Digital Center'))

@php
    $siteAdresse  = \App\Models\Configuration::get('site_adresse', 'Korhogo / Sirasso, Côte d\'Ivoire');
    $siteVille    = \App\Models\Configuration::get('site_ville', 'Korhogo / Sirasso');
    $sitePays     = \App\Models\Configuration::get('site_pays', 'Côte d\'Ivoire');
    $siteContact  = \App\Models\Configuration::get('site_contact', '+225 0700000000');
    $siteEmail    = \App\Models\Configuration::get('site_email', 'contact@excellencedigital.ci');
    $siteWhatsapp = \App\Models\Configuration::get('site_whatsapp', '2250700000000');
    $whatsappUrl  = 'https://wa.me/' . $siteWhatsapp;
@endphp

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

        {{-- Infos de contact dynamiques --}}
        <div class="space-y-6">
            <div class="edc-card p-6">
                <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">📍 Nos coordonnées</h3>
                <ul class="space-y-4">
                    {{-- Adresse --}}
                    @if($siteAdresse || $siteVille)
                    <li class="flex items-start space-x-3">
                        <span class="text-2xl">📍</span>
                        <div>
                            <p class="font-semibold" style="color: var(--edc-text-primary);">Localisation</p>
                            <p style="color: var(--edc-text-muted);">
                                {{ $siteAdresse ?: $siteVille }}{{ $sitePays ? ', ' . $sitePays : '' }}
                            </p>
                        </div>
                    </li>
                    @endif

                    {{-- Téléphone --}}
                    @if($siteContact)
                    <li class="flex items-start space-x-3">
                        <span class="text-2xl">📲</span>
                        <div>
                            <p class="font-semibold" style="color: var(--edc-text-primary);">WhatsApp</p>
                            <a href="{{ $whatsappUrl }}"
                                class="font-medium hover:underline" style="color: var(--edc-secondary);" target="_blank">
                                {{ $siteContact }}
                            </a>
                        </div>
                    </li>
                    @endif

                    {{-- Email --}}
                    @if($siteEmail)
                    <li class="flex items-start space-x-3">
                        <span class="text-2xl">✉️</span>
                        <div>
                            <p class="font-semibold" style="color: var(--edc-text-primary);">Email</p>
                            <a href="mailto:{{ $siteEmail }}" style="color: var(--edc-text-muted);">
                                {{ $siteEmail }}
                            </a>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>

            {{-- CTA WhatsApp --}}
            @if($siteWhatsapp)
            <div class="edc-card p-6 text-center" style="background: linear-gradient(135deg, var(--edc-primary-dark), var(--edc-primary));">
                <p class="text-xl font-bold mb-2" style="color: #fff;">Besoin urgent ?</p>
                <p class="text-sm mb-4" style="color: rgba(255,255,255,0.7);">Contactez-nous directement sur WhatsApp</p>
                <a href="{{ $whatsappUrl }}" target="_blank" class="btn-success">
                    💬 WhatsApp maintenant
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
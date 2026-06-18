@extends('layouts.public')
@section('title', 'À propos — ' . \App\Models\Configuration::get('site_nom', 'Excellence Digital Center'))

@php
    $siteNom     = \App\Models\Configuration::get('site_nom', 'Excellence Digital Center');
    $siteDevise  = \App\Models\Configuration::get('site_devise', 'Former • Créer • Réussir');
    $siteContact = \App\Models\Configuration::get('site_contact', '');
    $siteWhatsapp= \App\Models\Configuration::get('site_whatsapp', '2250748746140');
    $whatsappUrl = 'https://wa.me/' . $siteWhatsapp;
@endphp

@section('content')

{{-- ═══════════════════════════════════════════════ --}}
{{-- SECTION 1 : HERO --}}
{{-- ═══════════════════════════════════════════════ --}}
<section class="relative text-white py-16 sm:py-24 px-4 overflow-hidden">
    <div class="absolute inset-0 z-0" style="background: linear-gradient(135deg, #0f2447 0%, #1a1642 50%, #1e1b4b 100%);">
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-10" style="background: radial-gradient(circle, #3B82F6, transparent);"></div>
    </div>
    <div class="max-w-4xl mx-auto text-center relative z-10">
        <h1 class="text-hero mb-4">À propos de <span class="text-gradient">{{ $siteNom }}</span></h1>
        <p class="text-lg" style="color: #94A3B8;">{{ $siteDevise }}</p>
    </div>
</section>

{{-- ═══════════════════════════════════════════════ --}}
{{-- SECTION 2 : MISSION --}}
{{-- ═══════════════════════════════════════════════ --}}
@if(!empty($mission))
<section class="py-16 px-4" style="background-color: #0F172A;">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-section mb-10">Notre Mission</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($mission as $item)
            <div class="edc-card p-6">
                <div class="text-4xl mb-3">{{ $item['icone'] ?? '🎯' }}</div>
                <h3 class="font-bold mb-2" style="color: #F1F5F9;">{{ $item['titre'] ?? '' }}</h3>
                <p class="text-sm" style="color: #64748B;">{{ $item['description'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════ --}}
{{-- SECTION 3 : VALEURS --}}
{{-- ═══════════════════════════════════════════════ --}}
@if(!empty($valeurs))
<section class="py-16 px-4" style="background-color: #111827;">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-section mb-10">Nos Valeurs</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($valeurs as $valeur)
            <div class="edc-card p-5 text-center">
                <div class="text-3xl mb-3">{{ $valeur['icone'] ?? '✨' }}</div>
                <h3 class="font-bold text-sm mb-1" style="color: #F1F5F9;">{{ $valeur['titre'] ?? '' }}</h3>
                <p class="text-xs" style="color: #64748B;">{{ $valeur['description'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════ --}}
{{-- SECTION 4 : ÉQUIPE --}}
{{-- ═══════════════════════════════════════════════ --}}
@if($equipes->count())
<section class="py-16 px-4" style="background-color: #0F172A;">
    <div class="max-w-5xl mx-auto text-center">
        <h2 class="text-section mb-4">Notre Équipe</h2>
        <p class="section-subtitle mb-10">Des passionnés à votre service</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($equipes as $membre)
            <div class="edc-card p-5 text-center">
                @if($membre->photo)
                <img src="{{ asset('storage/' . $membre->photo) }}" alt="{{ $membre->nom }}"
                    class="w-20 h-20 rounded-full object-cover mx-auto mb-3 border-2" style="border-color: var(--edc-primary);">
                @else
                <div class="w-20 h-20 rounded-full mx-auto mb-3 flex items-center justify-center font-bold text-white"
                    style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                    {{ strtoupper(substr($membre->nom, 0, 1)) }}
                </div>
                @endif
                <h3 class="font-bold text-sm" style="color: #F1F5F9;">{{ $membre->nom }}</h3>
                <p class="text-xs" style="color: #60A5FA;">{{ $membre->poste }}</p>
                @if($membre->email || $membre->linkedin)
                <div class="flex justify-center space-x-2 mt-3">
                    @if($membre->email)
                    <a href="mailto:{{ $membre->email }}" class="text-sm" style="color: #64748B;">✉️</a>
                    @endif
                    @if($membre->linkedin)
                    <a href="{{ $membre->linkedin }}" target="_blank" class="text-sm" style="color: #64748B;">🔗</a>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════ --}}
{{-- SECTION 5 : GOOGLE MAPS --}}
{{-- ═══════════════════════════════════════════════ --}}
@if($mapsEmbed)
<section class="py-16 px-4" style="background-color: #111827;">
    <div class="max-w-5xl mx-auto text-center">
        <h2 class="text-section mb-10">Où nous trouver</h2>
        <div class="rounded-2xl overflow-hidden" style="border: 1px solid var(--edc-border);">
            {!! $mapsEmbed !!}
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════ --}}
{{-- SECTION 6 : CTA CONTACT --}}
{{-- ═══════════════════════════════════════════════ --}}
<section class="cta-gradient text-white py-16 px-4 text-center relative">
    <div class="max-w-2xl mx-auto relative z-10">
        <h2 class="text-section mb-4" style="color: #F1F5F9;">Envie d'en savoir plus ?</h2>
        <p class="mb-8 text-lg" style="color: #94A3B8;">
            Contactez-nous ou passez nous voir directement au centre.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact') }}" class="btn-primary">
                <span>📩</span><span>Nous contacter</span>
            </a>
            @if($siteWhatsapp)
            <a href="{{ $whatsappUrl }}" target="_blank" class="btn-secondary"
                style="border-color: rgba(255,255,255,0.30); color: #F1F5F9;">
                <span>💬</span><span>WhatsApp</span>
            </a>
            @endif
        </div>
    </div>
</section>

@endsection
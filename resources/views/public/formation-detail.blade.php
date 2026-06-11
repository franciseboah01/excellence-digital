@extends('layouts.public')
@section('title', $formation->titre . ' — Excellence Digital Center')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <a href="{{ route('formations.index') }}"
        class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
        style="color: var(--edc-primary-light);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Toutes les formations</span>
    </a>

    {{-- IMAGE --}}
    @if($formation->image)
    <img src="{{ asset('storage/' . $formation->image) }}" alt="{{ $formation->titre }}"
        class="w-full h-64 object-cover rounded-2xl mt-4 mb-6">
    @endif

    {{-- INFOS --}}
    <div class="edc-card p-6 mb-6">
        <div class="flex flex-wrap gap-2 mb-3">
            <span class="badge text-xs" style="background-color: rgba(59,130,246,0.12); color: #60A5FA;">
                {{ $formation->module->icone ?? '📚' }} {{ $formation->module->nom ?? '—' }}
            </span>
            @if($formation->duree)
            <span class="badge text-xs" style="background-color: rgba(148,163,184,0.10); color: #94A3B8;">⏱ {{ $formation->duree }}</span>
            @endif
            @if($formation->prix)
            <span class="badge text-xs" style="background-color: rgba(16,185,129,0.12); color: #34D399;">💰 {{ number_format($formation->prix, 0, ',', ' ') }} FCFA</span>
            @endif
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold mb-4" style="color: var(--edc-text-primary);">{{ $formation->titre }}</h1>
        <p class="text-base leading-relaxed" style="color: var(--edc-text-secondary);">{{ $formation->description }}</p>

        <div class="flex flex-col sm:flex-row gap-3 mt-6">
            <a href="{{ route('register') }}" class="btn-primary">
                ✨ S'inscrire gratuitement
            </a>
            <a href="https://wa.me/2250748746140" target="_blank" class="btn-secondary">
                💬 Demander sur WhatsApp
            </a>
        </div>
    </div>

    {{-- NIVEAUX --}}
    @if($niveaux->count())
    <div class="edc-card p-6">
        <h2 class="text-xl font-bold mb-4" style="color: var(--edc-text-primary);">📂 Programme</h2>
        <div class="space-y-3">
            @foreach($niveaux as $niveau)
            <div class="rounded-xl p-4" style="border: 1px solid var(--edc-border);">
                <div class="flex items-center space-x-3">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                        style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">{{ $niveau->ordre }}</span>
                    <div>
                        <h3 class="font-semibold" style="color: var(--edc-text-primary);">{{ $niveau->nom }}</h3>
                        @if($niveau->description)
                        <p class="text-xs mt-1" style="color: var(--edc-text-muted);">{{ $niveau->description }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
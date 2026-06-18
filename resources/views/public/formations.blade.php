@extends('layouts.public')
@section('title', 'Formations — ' . \App\Models\Configuration::get('site_nom', 'Excellence Digital Center'))

@section('content')
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="text-section">Nos Formations</h1>
        <p class="section-subtitle">Des formations pratiques, accessibles à tous</p>
    </div>

    @forelse($formations as $module => $liste)
    <div class="mb-12">
        {{-- En-tête avec titre + bouton Voir tout --}}
        <div class="flex items-center justify-between mb-6 pb-2" style="border-bottom: 2px solid var(--edc-border);">
            <h2 class="text-2xl font-bold" style="color: var(--edc-primary-light);">
                {{ $module }}
            </h2>
            @if($liste->count() == 4)
            <a href="{{ route('formations.module', ['slug' => Str::slug($module)]) }}"
               class="text-sm font-semibold transition hover:opacity-80"
               style="color: var(--edc-primary-light);">
                Voir tout →
            </a>
            @endif
        </div>

        <div class="grid-responsive-3">
            @foreach($liste as $formation)
            <div class="edc-card overflow-hidden">
                @if($formation->image)
                <img src="{{ asset('storage/' . $formation->image) }}"
                    alt="{{ $formation->titre }}" class="w-full h-44 object-cover">
                @else
                <div class="w-full h-44 flex items-center justify-center"
                    style="background: linear-gradient(135deg, var(--edc-primary-dark), var(--edc-primary));">
                    <span class="text-6xl">🎓</span>
                </div>
                @endif
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="badge badge-green">
                            {{ ucfirst($formation->niveau) }}
                        </span>
                        @if($formation->duree)
                        <span class="text-xs" style="color: var(--edc-text-muted);">⏱ {{ $formation->duree }}</span>
                        @endif
                    </div>
                    <h3 class="text-lg font-bold mb-2" style="color: var(--edc-text-primary);">{{ $formation->titre }}</h3>
                    <p class="text-sm leading-relaxed mb-4" style="color: var(--edc-text-secondary);">
                        {{ Str::limit($formation->description, 100) }}
                    </p>

                    {{-- Prix de la formation --}}
                    @if($formation->prix)
                    <p class="text-sm font-bold mb-3" style="color: #34D399;">
                        💰 {{ number_format($formation->prix, 0, ',', ' ') }} FCFA
                    </p>
                    @endif

                    <div class="flex items-center justify-between">
                        <span class="text-sm" style="color: var(--edc-text-muted);">
                            👥 {{ $formation->inscriptions_count }} inscrits
                        </span>
                        <a href="{{ route('formations.show', $formation) }}" class="btn-primary btn-sm">
                            Voir détails
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="text-center py-16" style="color: var(--edc-text-muted);">
        <p class="text-5xl mb-4">🎓</p>
        <p>Aucune formation disponible pour le moment.</p>
    </div>
    @endforelse
</div>
@endsection
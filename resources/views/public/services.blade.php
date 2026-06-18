@extends('layouts.public')
@section('title', 'Services — ' . \App\Models\Configuration::get('site_nom', 'Excellence Digital Center'))

@section('content')
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="text-section">Nos Services</h1>
        <p class="section-subtitle">Choisissez le service adapté à vos besoins</p>
    </div>

    @foreach($services as $categorie => $liste)
    <div class="mb-12">
        {{-- En-tête avec titre + bouton Voir tout --}}
        <div class="flex items-center justify-between mb-6 pb-2" style="border-bottom: 2px solid var(--edc-border);">
            <h2 class="text-2xl font-bold" style="color: var(--edc-primary-light);">
                {{ $categorie }}
            </h2>
            @if($liste->count() == 4)
            <a href="{{ route('services.categorie', ['categorie' => Str::slug($categorie)]) }}"
               class="text-sm font-semibold transition hover:opacity-80"
               style="color: var(--edc-primary-light);">
                Voir tout →
            </a>
            @endif
        </div>

        <div class="grid-responsive-3">
            @foreach($liste as $service)
            <div class="edc-card p-6">
                <div class="text-4xl mb-3">{{ $service->icone ?? '⚙️' }}</div>
                <h3 class="text-lg font-bold mb-2" style="color: var(--edc-text-primary);">{{ $service->titre }}</h3>
                <p class="text-sm leading-relaxed mb-4" style="color: var(--edc-text-secondary);">{{ $service->description }}</p>
                @if($service->prix)
                <p class="font-semibold mb-3" style="color: var(--edc-primary-light);">
                    À partir de {{ number_format($service->prix, 0, ',', ' ') }} FCFA
                </p>
                @endif
                <a href="{{ route('demande.form') }}?service={{ $service->id }}" class="btn-primary btn-sm">
                    Demander ce service
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
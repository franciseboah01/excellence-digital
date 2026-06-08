@extends('layouts.public')
@section('title', 'Services — Excellence Digital Center')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="text-section">Nos Services</h1>
        <p class="section-subtitle">Choisissez le service adapté à vos besoins</p>
    </div>

    @foreach($services as $categorie => $liste)
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-6 pb-2" style="color: var(--edc-primary-light); border-bottom: 2px solid var(--edc-border);">
            @if($categorie == 'bureautique') 💼 Bureautique
            @elseif($categorie == 'design') 🌐 Digital & Design
            @else 💻 Développement Web & Mobile
            @endif
        </h2>
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
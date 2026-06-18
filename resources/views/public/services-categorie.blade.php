@extends('layouts.public')
@section('title', $categorieNom . ' — ' . \App\Models\Configuration::get('site_nom', 'Excellence Digital Center'))

@section('content')
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="text-section">{{ $categorieNom }}</h1>
        <p class="section-subtitle">Tous les services de cette catégorie</p>
    </div>

    <div class="grid-responsive-3">
        @forelse($services as $service)
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
        @empty
        <p class="text-center col-span-3 py-10" style="color: #475569;">Aucun service trouvé.</p>
        @endforelse
    </div>

    <div class="text-center mt-10">
        <a href="{{ route('services.index') }}" class="btn-secondary">
            ← Retour aux services
        </a>
    </div>
</div>
@endsection
@extends('layouts.public')
@section('title', $moduleNom . ' — ' . \App\Models\Configuration::get('site_nom', 'Excellence Digital Center'))

@section('content')
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h1 class="text-section">{{ $moduleNom }}</h1>
        <p class="section-subtitle">Toutes les formations de ce module</p>
    </div>

    <div class="grid-responsive-3">
        @forelse($formations as $formation)
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
        @empty
        <div class="col-span-3 text-center py-16" style="color: var(--edc-text-muted);">
            <p class="text-5xl mb-4">📚</p>
            <p>Aucune formation trouvée dans ce module.</p>
        </div>
        @endforelse
    </div>

    <div class="text-center mt-10">
        <a href="{{ route('formations.index') }}" class="btn-secondary">
            ← Retour aux formations
        </a>
    </div>
</div>
@endsection
@extends('layouts.admin')
@section('title', 'Services')
@section('page_title', '💼 Gestion des Services')
@section('page_subtitle', 'Créez et gérez les services proposés')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
    @foreach([
        ['total',   'Total',   'var(--edc-primary)'],
        ['actifs',  'Actifs',  'var(--edc-secondary)'],
        ['inactifs','Inactifs','var(--edc-text-muted)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">{{ $stats[$stat[0]] }}</p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

{{-- BOUTONS --}}
<div class="flex justify-end mt-5 space-x-3">
    <a href="{{ route('admin.categories.index') }}" class="btn-tertiary btn-sm">📂 Gérer les catégories</a>
    <a href="{{ route('admin.services.create') }}" class="btn-primary btn-sm">➕ Nouveau service</a>
</div>

{{-- GRILLE SERVICES --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-4">
    @forelse($services as $service)
    <div class="edc-card overflow-hidden {{ !$service->actif ? 'opacity-60' : '' }}">
        <div class="p-5">
            <div class="flex items-start justify-between mb-3">
                <span class="text-3xl">{{ $service->icone }}</span>
                <div class="flex items-center space-x-2">
                    <span class="badge text-xs" style="{{ $service->actif
                        ? 'background-color: rgba(16,185,129,0.12); color: #34D399;'
                        : 'background-color: rgba(148,163,184,0.10); color: #94A3B8;' }}">
                        {{ $service->actif ? '✅ Actif' : '⏸️ Inactif' }}
                    </span>
                    <span class="badge badge-blue text-xs">{{ $service->demandes_count }} demande(s)</span>
                </div>
            </div>

            <h3 class="font-bold mb-1" style="color: var(--edc-text-primary);">{{ $service->titre }}</h3>

            <span class="badge text-xs" style="background-color: rgba(148,163,184,0.10); color: #94A3B8;">
                {{ $service->categorie->icone ?? '📂' }} {{ $service->categorie->nom ?? 'Sans catégorie' }}
            </span>

            <p class="text-sm mt-2 leading-relaxed" style="color: var(--edc-text-secondary);">
                {{ Str::limit($service->description, 80) }}
            </p>

            @if($service->prix)
            <p class="font-semibold mt-2 text-sm" style="color: var(--edc-primary-light);">
                {{ number_format($service->prix, 0, ',', ' ') }} FCFA
            </p>
            @endif
        </div>

        <div class="px-5 py-3 flex justify-between items-center" style="border-top: 1px solid var(--edc-border); background-color: var(--edc-bg-base);">
            <div class="flex space-x-3">
                <a href="{{ route('admin.services.edit', $service) }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">✏️ Modifier</a>
                <form method="POST" action="{{ route('admin.services.toggle', $service) }}">
                    @csrf
                    <button type="submit" class="text-xs font-medium hover:underline"
                        style="color: {{ $service->actif ? 'var(--edc-accent-gold)' : 'var(--edc-secondary)' }};">
                        {{ $service->actif ? '⏸️ Désactiver' : '▶️ Activer' }}
                    </button>
                </form>
            </div>
            <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
                onsubmit="return confirm('Supprimer ce service ?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs font-medium hover:underline" style="color: var(--edc-danger);">🗑️ Supprimer</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 edc-card text-center py-16" style="color: var(--edc-text-muted);">
        <p class="text-5xl mb-4">💼</p>
        <p class="font-medium">Aucun service créé.</p>
        <a href="{{ route('admin.services.create') }}" class="btn-primary btn-sm mt-4 inline-block">Créer le premier service</a>
    </div>
    @endforelse
</div>

<div class="mt-6">{{ $services->links() }}</div>
@endsection
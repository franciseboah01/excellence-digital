@extends('layouts.client')
@section('title', 'Mes Formations')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-extrabold" style="color: var(--edc-text-primary);">📚 Mes Formations</h1>
        <a href="{{ route('client.formations-disponibles') }}" class="btn-primary btn-sm">
            + Voir les formations disponibles
        </a>
    </div>

    @forelse($inscriptions as $inscription)
    <div class="edc-card mb-4 p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold" style="color: var(--edc-text-primary);">
                    {{ $inscription->formation->titre }}
                </h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-sm" style="color: var(--edc-text-secondary);">
                        {{ $inscription->formation->module->nom ?? '—' }}
                    </span>
                    <span class="badge text-xs" style="background-color: {{ $inscription->statut == 'valide' ? 'rgba(16,185,129,0.12)' : 'rgba(245,158,11,0.12)' }}; color: {{ $inscription->statut == 'valide' ? '#34D399' : '#F59E0B' }};">
                        {{ $inscription->statut == 'valide' ? '✅ Validé' : '⏳ En attente' }}
                    </span>
                </div>
            </div>
            
            @if($inscription->statut == 'valide')
            <a href="{{ route('client.formation.show', $inscription->formation->id) }}" 
               class="btn-primary btn-sm inline-flex items-center gap-2">
                <span>Accéder</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
            @endif
        </div>
    </div>
    @empty
    <div class="edc-card text-center py-12" style="color: var(--edc-text-muted);">
        <p class="text-4xl mb-3">📭</p>
        <p>Vous n'êtes inscrit à aucune formation.</p>
        <a href="{{ route('client.formations-disponibles') }}" class="btn-primary mt-4 inline-block">
            Voir les formations disponibles
        </a>
    </div>
    @endforelse
</div>
@endsection
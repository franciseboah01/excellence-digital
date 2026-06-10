@extends('layouts.client')
@section('title', 'Mes Demandes')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">📋 Mes Demandes de Service</h1>
    <a href="{{ route('client.demande.form') }}" class="btn-primary btn-sm">
        <span>➕</span><span>Nouvelle demande</span>
    </a>
</div>

<div class="edc-card overflow-hidden">
    @forelse($demandes as $demande)
    <div class="p-5 sm:p-6" style="border-bottom: 1px solid var(--edc-border);">

        {{-- En-tête demande --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="font-bold" style="color: var(--edc-text-primary);">{{ $demande->service->titre }}</h3>
                <p class="text-xs mt-1" style="color: var(--edc-text-muted);">
                    Demande #{{ $demande->id }} — {{ $demande->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            @include('client.partials.statut-badge', ['statut' => $demande->statut])
        </div>

        {{-- TIMELINE STATUT --}}
        <div class="flex items-center space-x-2 mt-4 overflow-x-auto pb-2">
            @php
                $etapes = [
                    'en_attente' => ['⏳', 'En attente'],
                    'en_cours'   => ['🔄', 'En cours'],
                    'termine'    => ['✅', 'Terminé'],
                ];
                $statuts = array_keys($etapes);
                $indexActuel = array_search($demande->statut, $statuts);
            @endphp

            @foreach($etapes as $key => $etape)
            @php $index = array_search($key, $statuts); @endphp
            <div class="flex items-center">
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition
                        {{ $index <= $indexActuel
                            ? 'active'
                            : '' }}"
                        style="{{ $index <= $indexActuel
                            ? 'background: linear-gradient(135deg, #3B82F6, #1D4ED8); color: #fff;'
                            : 'background-color: var(--edc-bg-elevated); color: var(--edc-text-muted);' }}">
                        {{ $etape[0] }}
                    </div>
                    <p class="text-xs mt-1 font-semibold" style="{{ $index <= $indexActuel ? 'color: var(--edc-primary-light);' : 'color: var(--edc-text-muted);' }}">
                        {{ $etape[1] }}
                    </p>
                </div>
                @if(!$loop->last)
                <div class="w-12 sm:w-16 h-1 mx-1 rounded transition"
                    style="{{ $index < $indexActuel ? 'background: linear-gradient(135deg, #3B82F6, #1D4ED8);' : 'background-color: var(--edc-bg-elevated);' }}">
                </div>
                @endif
            </div>
            @endforeach
        </div>

        @if($demande->message)
        <div class="mt-3 text-sm rounded-lg p-3"
            style="background-color: var(--edc-bg-base); color: var(--edc-text-secondary);">
            💬 {{ $demande->message }}
        </div>
        @endif
    </div>
    @empty
    <div class="text-center py-16" style="color: var(--edc-text-muted);">
        <p class="text-5xl mb-4">📋</p>
        <p class="font-medium">Aucune demande pour le moment.</p>
        <a href="{{ route('client.demande.form') }}" class="btn-primary btn-sm mt-4 inline-block">
            Faire une demande
        </a>
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $demandes->links() }}</div>
@endsection
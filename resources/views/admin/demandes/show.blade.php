@extends('layouts.admin')
@section('title', 'Demande #' . $demande->id)
@section('page_title', '📋 Détail de la Demande')
@section('page_subtitle', 'Demande #' . $demande->id)

@section('content')
<div class="mt-4">
    <a href="{{ route('admin.demandes.index') }}"
        class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
        style="color: var(--edc-primary-light);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Retour aux demandes</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">

    {{-- INFOS DEMANDE --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Client --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">👤 Informations du demandeur</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p style="color: var(--edc-text-muted);">Nom</p>
                    <p class="font-semibold" style="color: var(--edc-text-primary);">{{ $demande->user?->nom_complet ?? $demande->nom_visiteur }}</p>
                </div>
                <div>
                    <p style="color: var(--edc-text-muted);">Email</p>
                    <p class="font-semibold" style="color: var(--edc-text-primary);">{{ $demande->user?->email ?? $demande->email_visiteur }}</p>
                </div>
                <div>
                    <p style="color: var(--edc-text-muted);">Téléphone</p>
                    <p class="font-semibold" style="color: var(--edc-text-primary);">{{ $demande->user?->telephone ?? $demande->telephone_visiteur ?? '—' }}</p>
                </div>
                <div>
                    <p style="color: var(--edc-text-muted);">Type</p>
                    <span class="badge text-xs" style="{{ $demande->user_id
                        ? 'background-color: rgba(59,130,246,0.12); color: #60A5FA;'
                        : 'background-color: rgba(148,163,184,0.10); color: #94A3B8;' }}">
                        {{ $demande->user_id ? '👤 Client inscrit' : '🌐 Visiteur' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Service --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">💼 Service demandé</h3>
            <div class="flex items-center space-x-4">
                <span class="text-4xl">{{ $demande->service->icone }}</span>
                <div>
                    <p class="font-bold text-lg" style="color: var(--edc-text-primary);">{{ $demande->service->titre }}</p>
                    <p class="text-sm" style="color: var(--edc-text-secondary);">{{ $demande->service->description }}</p>
                    @if($demande->service->prix)
                    <p class="font-semibold mt-1" style="color: var(--edc-primary-light);">{{ number_format($demande->service->prix, 0, ',', ' ') }} FCFA</p>
                    @endif
                </div>
            </div>
            @if($demande->message)
            <div class="mt-4 rounded-lg p-4 text-sm" style="background-color: var(--edc-bg-base); color: var(--edc-text-secondary);">
                <p class="font-semibold mb-1" style="color: var(--edc-text-primary);">💬 Message du client :</p>
                {{ $demande->message }}
            </div>
            @endif
        </div>
    </div>

    {{-- PANEL STATUT --}}
    <div class="space-y-5">

        {{-- Statut actuel --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">📊 Statut actuel</h3>
            @php
                $badge = match($demande->statut) {
                    'en_attente' => 'background-color: rgba(245,158,11,0.12); color: #FBBF24; border-color: rgba(245,158,11,0.30);',
                    'en_cours'   => 'background-color: rgba(59,130,246,0.12); color: #60A5FA; border-color: rgba(59,130,246,0.30);',
                    'termine'    => 'background-color: rgba(16,185,129,0.12); color: #34D399; border-color: rgba(16,185,129,0.30);',
                    'annule'     => 'background-color: rgba(239,68,68,0.12); color: #F87171; border-color: rgba(239,68,68,0.30);',
                    default      => 'background-color: rgba(148,163,184,0.10); color: #94A3B8; border-color: rgba(148,163,184,0.20);',
                };
                $label = match($demande->statut) {
                    'en_attente' => '⏳ En attente',
                    'en_cours'   => '🔄 En cours de traitement',
                    'termine'    => '✅ Terminé',
                    'annule'     => '❌ Annulé',
                    default      => $demande->statut,
                };
            @endphp
            <div class="rounded-xl p-4 text-center text-lg font-bold" style="{{ $badge }}; border: 2px solid;">
                {{ $label }}
            </div>
            <p class="text-xs text-center mt-2" style="color: var(--edc-text-muted);">Créée le {{ $demande->created_at->format('d/m/Y à H:i') }}</p>
        </div>

        {{-- Changer le statut --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">🔄 Changer le statut</h3>

            <form method="POST" action="{{ route('admin.demandes.statut', $demande) }}" class="space-y-4">
                @csrf

                <div>
                    <label class="edc-label">Nouveau statut *</label>
                    <select name="statut" required class="edc-select">
                        <option value="en_attente" {{ $demande->statut == 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                        <option value="en_cours"   {{ $demande->statut == 'en_cours' ? 'selected' : '' }}>🔄 En cours</option>
                        <option value="termine"    {{ $demande->statut == 'termine' ? 'selected' : '' }}>✅ Terminé</option>
                        <option value="annule"     {{ $demande->statut == 'annule' ? 'selected' : '' }}>❌ Annulé</option>
                    </select>
                </div>

                <div>
                    <label class="edc-label">Message personnalisé (optionnel)</label>
                    <textarea name="message" rows="3" class="edc-input" placeholder="Message envoyé au client..."></textarea>
                </div>

                <button type="submit" class="btn-primary w-full">📤 Mettre à jour & Notifier</button>

                <p class="text-xs text-center mt-2" style="color: var(--edc-text-muted);">
                    📧 Un email sera automatiquement envoyé
                    @if($demande->user_id) + 🔔 notification interne @endif
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
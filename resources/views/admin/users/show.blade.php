@extends('layouts.admin')
@section('title', 'Détail — ' . $user->nom_complet)
@section('page_title', '👤 Fiche Utilisateur')
@section('page_subtitle', $user->nom_complet)

@section('content')
<div class="mt-6">
    <a href="{{ route('admin.users.index') }}"
        class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
        style="color: var(--edc-primary-light);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Retour à la liste</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">

    {{-- PROFIL --}}
    <div class="edc-card p-6">
        <div class="text-center mb-5">
            <div class="w-20 h-20 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-3"
                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                {{ strtoupper(substr($user->prenom, 0, 1)) }}
            </div>
            <h2 class="text-xl font-bold" style="color: var(--edc-text-primary);">{{ $user->nom_complet }}</h2>
            <p class="text-sm mt-1" style="color: var(--edc-text-muted);">{{ $user->email }}</p>
            @foreach($roles as $role)
            <span class="badge badge-blue mt-2">{{ ucfirst($role) }}</span>
            @endforeach
        </div>

        <ul class="space-y-3 text-sm">
            <li class="flex justify-between">
                <span style="color: var(--edc-text-muted);">Téléphone</span>
                <span class="font-medium" style="color: var(--edc-text-primary);">{{ $user->telephone ?? '—' }}</span>
            </li>
            <li class="flex justify-between">
                <span style="color: var(--edc-text-muted);">Statut</span>
                @php
                    $s = match($user->statut) {
                        'actif'    => 'background-color: rgba(16,185,129,0.12); color: #34D399;',
                        'suspendu' => 'background-color: rgba(239,68,68,0.12); color: #F87171;',
                        default    => 'background-color: rgba(148,163,184,0.10); color: #94A3B8;',
                    };
                @endphp
                <span class="badge text-xs" style="{{ $s }}">{{ ucfirst($user->statut) }}</span>
            </li>
            <li class="flex justify-between">
                <span style="color: var(--edc-text-muted);">Email vérifié</span>
                <span>{{ $user->email_verified_at ? '✅' : '❌' }}</span>
            </li>
            <li class="flex justify-between">
                <span style="color: var(--edc-text-muted);">Inscrit le</span>
                <span class="font-medium" style="color: var(--edc-text-primary);">{{ $user->created_at->format('d/m/Y') }}</span>
            </li>
        </ul>

        {{-- Actions --}}
        <div class="mt-5 space-y-2">
            <form method="POST" action="{{ route('admin.users.toggle-statut', $user) }}">
                @csrf
                <button type="submit" class="w-full btn-sm rounded-lg text-sm font-medium transition"
                    style="{{ $user->statut === 'actif'
                        ? 'background-color: rgba(239,68,68,0.10); color: #F87171;'
                        : 'background-color: rgba(16,185,129,0.10); color: #34D399;' }}">
                    {{ $user->statut === 'actif' ? '⛔ Suspendre le compte' : '✅ Réactiver le compte' }}
                </button>
            </form>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                onsubmit="return confirm('Supprimer définitivement ce compte ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger btn-sm w-full">🗑️ Supprimer le compte</button>
            </form>
        </div>
    </div>

    {{-- DÉTAILS --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Formations & Inscriptions --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">🎓 Formations & Inscriptions</h3>
            @forelse($user->inscriptions as $inscription)
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 py-3" style="border-bottom: 1px solid var(--edc-border);">
                <div>
                    <p class="font-medium text-sm" style="color: var(--edc-text-primary);">{{ $inscription->formation->titre }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--edc-text-muted);">Inscrit le {{ $inscription->date_inscription->format('d/m/Y') }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    @php
                        $si = match($inscription->statut) {
                            'valide'     => ['background-color: rgba(16,185,129,0.12); color: #34D399;', '✅ Validé'],
                            'en_attente' => ['background-color: rgba(245,158,11,0.12); color: #FBBF24;', '⏳ En attente'],
                            'refuse'     => ['background-color: rgba(239,68,68,0.12); color: #F87171;', '❌ Refusé'],
                            default      => ['background-color: rgba(148,163,184,0.10); color: #94A3B8;', $inscription->statut],
                        };
                    @endphp
                    <span class="badge text-xs" style="{{ $si[0] }}">{{ $si[1] }}</span>
                    @if($inscription->statut === 'en_attente')
                    <form method="POST" action="{{ route('admin.users.inscription.valider', $inscription) }}">
                        @csrf
                        <button type="submit" class="btn-success btn-xs">Valider</button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.inscription.rejeter', $inscription) }}">
                        @csrf
                        <button type="submit" class="btn-danger btn-xs">Rejeter</button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-sm text-center py-4" style="color: var(--edc-text-muted);">Aucune inscription.</p>
            @endforelse
        </div>

        {{-- Demandes de services --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">📋 Historique des demandes</h3>
            @forelse($user->demandesService as $demande)
            <div class="flex items-center justify-between py-3" style="border-bottom: 1px solid var(--edc-border);">
                <div>
                    <p class="font-medium text-sm" style="color: var(--edc-text-primary);">{{ $demande->service->titre }}</p>
                    <p class="text-xs" style="color: var(--edc-text-muted);">{{ $demande->created_at->format('d/m/Y') }}</p>
                </div>
                @php
                    $sd = match($demande->statut) {
                        'en_attente' => 'background-color: rgba(245,158,11,0.12); color: #FBBF24;',
                        'en_cours'   => 'background-color: rgba(59,130,246,0.12); color: #60A5FA;',
                        'termine'    => 'background-color: rgba(16,185,129,0.12); color: #34D399;',
                        'annule'     => 'background-color: rgba(239,68,68,0.12); color: #F87171;',
                        default      => 'background-color: rgba(148,163,184,0.10); color: #94A3B8;',
                    };
                @endphp
                <span class="badge text-xs" style="{{ $sd }}">{{ ucfirst(str_replace('_', ' ', $demande->statut)) }}</span>
            </div>
            @empty
            <p class="text-sm text-center py-4" style="color: var(--edc-text-muted);">Aucune demande.</p>
            @endforelse
        </div>

        {{-- Notifications récentes --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">🔔 Notifications récentes</h3>
            @forelse($user->notifications as $notif)
            <div class="py-2" style="border-bottom: 1px solid var(--edc-border);">
                <p class="text-sm font-medium" style="color: var(--edc-text-primary);">{{ $notif->titre }}</p>
                <p class="text-xs mt-0.5" style="color: var(--edc-text-secondary);">{{ $notif->message }}</p>
                <p class="text-xs mt-0.5" style="color: var(--edc-text-muted);">{{ $notif->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <p class="text-sm text-center py-4" style="color: var(--edc-text-muted);">Aucune notification.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
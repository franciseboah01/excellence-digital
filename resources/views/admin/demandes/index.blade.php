@extends('layouts.admin')
@section('title', 'Demandes')
@section('page_title', '📋 Gestion des Demandes')
@section('page_subtitle', 'Suivi et traitement des demandes de service')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-6">
    @foreach([
        ['total',       '📋 Total',      'var(--edc-text-muted)'],
        ['en_attente',  '⏳ En attente', 'var(--edc-accent-gold)'],
        ['en_cours',    '🔄 En cours',   'var(--edc-primary)'],
        ['terminees',   '✅ Terminées',  'var(--edc-secondary)'],
        ['annulees',    '❌ Annulées',   'var(--edc-danger)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">{{ $stats[$stat[0]] }}</p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

{{-- FILTRES --}}
<div class="edc-card p-5 mt-5">
    <form method="GET" action="{{ route('admin.demandes.index') }}"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Nom, email..." class="edc-input">

        <select name="statut" class="edc-select">
            <option value="">Tous les statuts</option>
            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
            <option value="en_cours"   {{ request('statut') == 'en_cours' ? 'selected' : '' }}>🔄 En cours</option>
            <option value="termine"    {{ request('statut') == 'termine' ? 'selected' : '' }}>✅ Terminé</option>
            <option value="annule"     {{ request('statut') == 'annule' ? 'selected' : '' }}>❌ Annulé</option>
        </select>

        <select name="service_id" class="edc-select">
            <option value="">Tous les services</option>
            @foreach($services as $service)
            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>{{ $service->titre }}</option>
            @endforeach
        </select>

        <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="edc-input">

        <div class="flex space-x-2">
            <button type="submit" class="btn-primary btn-sm flex-1">Filtrer</button>
            <a href="{{ route('admin.demandes.index') }}" class="btn-tertiary btn-sm flex-1 text-center">Reset</a>
        </div>
    </form>
</div>

{{-- TABLEAU --}}
<div class="edc-card mt-5 overflow-hidden">
    <div class="px-6 py-4" style="border-bottom: 1px solid var(--edc-border);">
        <h3 class="font-bold" style="color: var(--edc-text-primary);">📋 {{ $demandes->total() }} demande(s) trouvée(s)</h3>
    </div>
    <div class="table-responsive">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($demandes as $demande)
                <tr>
                    <td style="color: var(--edc-text-muted);">{{ $demande->id }}</td>
                    <td>
                        <p class="font-medium" style="color: var(--edc-text-primary);">{{ $demande->user?->nom_complet ?? $demande->nom_visiteur }}</p>
                        <p class="text-xs" style="color: var(--edc-text-muted);">{{ $demande->user?->email ?? $demande->email_visiteur }}</p>
                        @if(!$demande->user_id)
                        <span class="badge badge-gray text-xs">Visiteur</span>
                        @endif
                    </td>
                    <td>
                        <p class="font-medium" style="color: var(--edc-text-primary);">{{ $demande->service->titre }}</p>
                        <p class="text-xs" style="color: var(--edc-text-muted);">{{ $demande->service->icone }} {{ ucfirst(str_replace('_', ' ', $demande->service->categorie)) }}</p>
                    </td>
                    <td style="color: var(--edc-text-muted);" class="text-xs">
                        {{ $demande->created_at->format('d/m/Y') }}<br>
                        <span>{{ $demande->created_at->diffForHumans() }}</span>
                    </td>
                    <td>
                        @php
                            $badge = match($demande->statut) {
                                'en_attente' => 'background-color: rgba(245,158,11,0.12); color: #FBBF24;',
                                'en_cours'   => 'background-color: rgba(59,130,246,0.12); color: #60A5FA;',
                                'termine'    => 'background-color: rgba(16,185,129,0.12); color: #34D399;',
                                'annule'     => 'background-color: rgba(239,68,68,0.12); color: #F87171;',
                                default      => 'background-color: rgba(148,163,184,0.10); color: #94A3B8;',
                            };
                            $label = match($demande->statut) {
                                'en_attente' => '⏳ En attente',
                                'en_cours'   => '🔄 En cours',
                                'termine'    => '✅ Terminé',
                                'annule'     => '❌ Annulé',
                                default      => $demande->statut,
                            };
                        @endphp
                        <span class="badge text-xs" style="{{ $badge }}">{{ $label }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.demandes.show', $demande) }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">👁️ Traiter</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center" style="color: var(--edc-text-muted);">
                        <p class="text-4xl mb-3">📋</p>
                        <p>Aucune demande trouvée.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4" style="border-top: 1px solid var(--edc-border);">{{ $demandes->links() }}</div>
</div>
@endsection
@extends('layouts.admin')
@section('title', 'Paiements')
@section('page_title', '💰 Gestion des Paiements')
@section('page_subtitle', 'Suivi et enregistrement des paiements')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mt-6">
    @foreach([
        ['total',       '📋 Total',        'var(--edc-text-muted)'],
        ['en_attente',  '⏳ En attente',   'var(--edc-accent-gold)'],
        ['partiel',     '⚠️ Partiel',     'var(--edc-primary)'],
        ['complete',    '✅ Complets',     'var(--edc-secondary)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value" style="font-size: 1.25rem;">{{ $stats[$stat[0]] }}</p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
    <div class="stat-card md:col-span-2" style="border-left-color: #A78BFA;">
        <p class="text-lg font-bold" style="color: #A78BFA;">
            {{ number_format($stats['total_percu'], 0, ',', ' ') }} FCFA
        </p>
        <p class="stat-label">💰 Total perçu / {{ number_format($stats['total_attendu'], 0, ',', ' ') }} FCFA</p>
        <div class="w-full rounded-full h-1.5 mt-2" style="background-color: var(--edc-bg-elevated);">
            @php $pct = $stats['total_attendu'] > 0 ? min(100, ($stats['total_percu'] / $stats['total_attendu']) * 100) : 0; @endphp
            <div class="h-1.5 rounded-full" style="width:{{ $pct }}%; background-color: #A78BFA;"></div>
        </div>
    </div>
</div>

{{-- FILTRES --}}
<div class="edc-card p-5 mt-5">
    <form method="GET" action="{{ route('admin.paiements.index') }}"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">

        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="🔍 Référence, nom..." class="edc-input">

        <select name="statut" class="edc-select">
            <option value="">Tous statuts</option>
            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
            <option value="partiel"    {{ request('statut') == 'partiel' ? 'selected' : '' }}>⚠️ Partiel</option>
            <option value="complete"   {{ request('statut') == 'complete' ? 'selected' : '' }}>✅ Complet</option>
        </select>

        <select name="mode_paiement" class="edc-select">
            <option value="">Tous modes</option>
            <option value="especes"      {{ request('mode_paiement') == 'especes' ? 'selected' : '' }}>💵 Espèces</option>
            <option value="mobile_money" {{ request('mode_paiement') == 'mobile_money' ? 'selected' : '' }}>📱 Mobile Money</option>
            <option value="virement"     {{ request('mode_paiement') == 'virement' ? 'selected' : '' }}>🏦 Virement</option>
        </select>

        <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="edc-input">
        <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="edc-input">

        <div class="flex space-x-2">
            <button type="submit" class="btn-primary btn-sm flex-1">Filtrer</button>
            <a href="{{ route('admin.paiements.index') }}" class="btn-tertiary btn-sm flex-1 text-center">Reset</a>
        </div>
    </form>
</div>

{{-- BOUTON AJOUTER --}}
<div class="flex justify-end mt-4">
    <a href="{{ route('admin.paiements.create') }}" class="btn-primary btn-sm">➕ Nouveau paiement</a>
</div>

{{-- TABLEAU --}}
<div class="edc-card mt-4 overflow-hidden">
    <div class="table-responsive">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Client</th>
                    <th>Objet</th>
                    <th>Montant</th>
                    <th>Progression</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paiements as $paiement)
                <tr>
                    <td>
                        <p class="font-mono text-xs font-bold" style="color: var(--edc-primary-light);">{{ $paiement->reference }}</p>
                        <p class="text-xs" style="color: var(--edc-text-muted);">{{ $paiement->created_at->format('d/m/Y') }}</p>
                    </td>
                    <td class="font-medium" style="color: var(--edc-text-primary);">
                        {{ $paiement->user->prenom }} {{ $paiement->user->nom }}
                    </td>
                    <td>
                        <p class="text-xs" style="color: var(--edc-text-secondary);">
                            @if($paiement->formation) 
                                🎓 {{ Str::limit($paiement->formation->titre, 25) }}
                            @elseif($paiement->service) 
                                💼 {{ Str::limit($paiement->service->titre, 25) }}
                            @elseif($paiement->notes && str_contains($paiement->notes, 'Duplicata')) 
                                📄 Duplicata certificat
                            @elseif($paiement->notes) 
                                📝 {{ Str::limit($paiement->notes, 25) }}
                            @else 
                                — 
                            @endif
                        </p>
                        <p class="text-xs mt-0.5" style="color: var(--edc-text-muted);">
                            {{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}
                        </p>
                    </td>
                    <td>
                        <p class="font-semibold" style="color: var(--edc-text-primary);">
                            {{ number_format($paiement->montant_paye, 0, ',', ' ') }}
                            <span style="color: var(--edc-text-muted); font-weight: normal; font-size: 0.7rem;">
                                / {{ number_format($paiement->montant_total, 0, ',', ' ') }} FCFA
                            </span>
                        </p>
                    </td>
                    <td>
                        <div class="w-24">
                            <div class="flex justify-between text-xs mb-1" style="color: var(--edc-text-muted);">
                                <span>{{ $paiement->pourcentage }}%</span>
                            </div>
                            <div class="w-full rounded-full h-2" style="background-color: var(--edc-bg-elevated);">
                                <div class="h-2 rounded-full" style="width:{{ $paiement->pourcentage }}%;
                                    background-color: {{ $paiement->pourcentage == 100 ? 'var(--edc-secondary)' : ($paiement->pourcentage > 0 ? 'var(--edc-primary)' : 'var(--edc-text-muted)') }};">
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $badge = match($paiement->statut) {
                                'complete'   => 'background-color: rgba(16,185,129,0.12); color: #34D399;',
                                'partiel'    => 'background-color: rgba(59,130,246,0.12); color: #60A5FA;',
                                default      => 'background-color: rgba(245,158,11,0.12); color: #FBBF24;',
                            };
                            $label = match($paiement->statut) {
                                'complete'   => '✅ Complet',
                                'partiel'    => '⚠️ Partiel',
                                default      => '⏳ En attente',
                            };
                        @endphp
                        <span class="badge text-xs" style="{{ $badge }}">{{ $label }}</span>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.paiements.show', $paiement) }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">👁️ Voir</a>
                            <a href="{{ route('admin.paiements.recu', $paiement) }}" class="text-xs font-medium hover:underline" style="color: var(--edc-secondary);">📄 Reçu</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center" style="color: var(--edc-text-muted);">
                        <p class="text-4xl mb-3">💰</p>
                        <p>Aucun paiement enregistré.</p>
                        <a href="{{ route('admin.paiements.create') }}" class="btn-primary btn-sm mt-4 inline-block">Enregistrer le premier paiement</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4" style="border-top: 1px solid var(--edc-border);">{{ $paiements->links() }}</div>
</div>
@endsection
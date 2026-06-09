@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('page_title', '🏠 Tableau de bord')
@section('page_subtitle', 'Vue d\'ensemble d\'Excellence Digital Center')

@section('content')

{{-- ALERTES --}}
@foreach($alertes as $alerte)
<div class="mt-4 px-4 py-3 rounded-xl font-medium text-sm
    {{ $alerte['type'] == 'warning' ? 'alert alert-warning' : 'alert alert-info' }}">
    <span>{{ $alerte['type'] == 'warning' ? '⚠️' : 'ℹ️' }}</span>
    <span>{{ $alerte['message'] }}</span>
</div>
@endforeach

{{-- STATS PRINCIPALES --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
    @foreach([
        ['clients',     '👥 Clients',      'var(--edc-primary)'],
        ['enseignants', '👨‍🏫 Enseignants', 'var(--edc-secondary)'],
        ['formations',  '🎓 Formations',   '#A78BFA'],
        ['services',    '💼 Services',     'var(--edc-accent-gold)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">{{ $stats[$stat[0]] }}</p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

{{-- STATS DEMANDES + REVENUS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
    @foreach([
        ['demandes',    '📋 Demandes totales', 'var(--edc-bg-card)', 'var(--edc-text-primary)'],
        ['en_attente',  '⏳ En attente',       'rgba(245,158,11,0.06)', '#FBBF24'],
        ['en_cours',    '🔄 En cours',         'rgba(59,130,246,0.06)', '#60A5FA'],
    ] as $stat)
    <div class="rounded-xl p-5 text-center transition hover:-translate-y-1"
        style="background-color: {{ $stat[2] }}; border: 1px solid var(--edc-border);">
        <p class="text-2xl font-bold" style="color: {{ $stat[3] }};">{{ $stats[$stat[0]] }}</p>
        <p class="text-xs mt-1" style="color: var(--edc-text-muted);">{{ $stat[1] }}</p>
    </div>
    @endforeach
    <div class="rounded-xl p-5 text-center transition hover:-translate-y-1"
        style="background-color: rgba(16,185,129,0.06); border: 1px solid var(--edc-border);">
        <p class="text-2xl font-bold" style="color: #34D399;">
            {{ number_format($stats['revenus'], 0, ',', ' ') }} FCFA
        </p>
        <p class="text-xs mt-1" style="color: var(--edc-text-muted);">💰 Revenus estimés</p>
    </div>
</div>

{{-- GRAPHIQUES --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- Inscriptions par mois --}}
    <div class="edc-card p-6">
        <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">
            📈 Inscriptions (6 derniers mois)
        </h3>
        <canvas id="chartInscriptions" height="120"></canvas>
    </div>

    {{-- Répartition services --}}
    <div class="edc-card p-6">
        <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">
            🥧 Répartition des demandes
        </h3>
        <canvas id="chartServices" height="120"></canvas>
    </div>
</div>

{{-- TABLEAUX --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- Inscriptions récentes --}}
    <div class="edc-card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold" style="color: var(--edc-text-primary);">🆕 Inscriptions récentes</h3>
            <a href="{{ route('admin.formations.index') }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">Voir tout</a>
        </div>
        @forelse($inscriptionsRecentes as $inscription)
        <div class="flex items-center justify-between py-3" style="border-bottom: 1px solid var(--edc-border);">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                    style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                    {{ strtoupper(substr($inscription->user->prenom, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium" style="color: var(--edc-text-primary);">{{ $inscription->user->nom_complet }}</p>
                    <p class="text-xs" style="color: var(--edc-text-muted);">{{ $inscription->formation->titre }}</p>
                </div>
            </div>
            @php
                $badgeStyle = match($inscription->statut) {
                    'valide'     => 'background-color: rgba(16,185,129,0.12); color: #34D399;',
                    'en_attente' => 'background-color: rgba(245,158,11,0.12); color: #FBBF24;',
                    'refuse'     => 'background-color: rgba(239,68,68,0.12); color: #F87171;',
                    default      => 'background-color: rgba(148,163,184,0.10); color: #94A3B8;',
                };
            @endphp
            <span class="badge text-xs" style="{{ $badgeStyle }}">{{ ucfirst($inscription->statut) }}</span>
        </div>
        @empty
        <p class="text-sm text-center py-4" style="color: var(--edc-text-muted);">Aucune inscription.</p>
        @endforelse
    </div>

    {{-- Demandes en attente --}}
    <div class="edc-card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold" style="color: var(--edc-text-primary);">⏳ Demandes en attente</h3>
            <a href="{{ route('admin.demandes.index') }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">Voir tout</a>
        </div>
        @forelse($demandesEnAttente as $demande)
        <div class="flex items-center justify-between py-3" style="border-bottom: 1px solid var(--edc-border);">
            <div>
                <p class="text-sm font-medium" style="color: var(--edc-text-primary);">
                    {{ $demande->user?->nom_complet ?? $demande->nom_visiteur }}
                </p>
                <p class="text-xs" style="color: var(--edc-text-muted);">{{ $demande->service->titre }}</p>
                <p class="text-xs mt-0.5" style="color: var(--edc-text-muted);">{{ $demande->created_at->diffForHumans() }}</p>
            </div>
            <a href="{{ route('admin.demandes.index') }}"
                class="badge badge-gold text-xs hover:scale-105 transition">
                Traiter →
            </a>
        </div>
        @empty
        <p class="text-sm text-center py-4" style="color: var(--edc-text-muted);">✅ Aucune demande en attente.</p>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ===== GRAPHIQUE INSCRIPTIONS PAR MOIS =====
    const ctxInscriptions = document.getElementById('chartInscriptions').getContext('2d');
    new Chart(ctxInscriptions, {
        type: 'bar',
        data: {
            labels: @json($labelsMois),
            datasets: [{
                label: 'Inscriptions',
                data: @json($dataMois),
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    ticks: { color: '#94A3B8' },
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#94A3B8' },
                    grid: { color: 'rgba(42, 53, 82, 0.5)' }
                }
            }
        }
    });

    // ===== GRAPHIQUE RÉPARTITION SERVICES =====
    const ctxServices = document.getElementById('chartServices').getContext('2d');
    new Chart(ctxServices, {
        type: 'doughnut',
        data: {
            labels: @json($labelsServices),
            datasets: [{
                data: @json($dataServices),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                ],
                borderColor: '#0B0F1A',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#94A3B8',
                        font: { size: 12 },
                        padding: 15
                    }
                }
            }
        }
    });
</script>
@endpush
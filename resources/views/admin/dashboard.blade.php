@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('page_title', 'Tableau de bord')
@section('page_subtitle', 'Vue d\'ensemble d\'Excellence Digital Center')

@section('content')

{{-- ALERTES --}}
@foreach($alertes as $alerte)
<div class="mt-4 px-4 py-3 rounded-xl font-medium text-sm
    {{ $alerte['type'] == 'warning' ? 'bg-yellow-100 border border-yellow-400 text-yellow-800' : 'bg-blue-100 border border-blue-400 text-blue-800' }}">
    {{ $alerte['type'] == 'warning' ? '⚠️' : 'ℹ️' }} {{ $alerte['message'] }}
</div>
@endforeach

{{-- STATS PRINCIPALES --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-600">
        <p class="text-3xl font-bold text-blue-700">{{ $stats['clients'] }}</p>
        <p class="text-gray-500 text-sm mt-1">👥 Clients</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-3xl font-bold text-green-600">{{ $stats['enseignants'] }}</p>
        <p class="text-gray-500 text-sm mt-1">👨‍🏫 Enseignants</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
        <p class="text-3xl font-bold text-purple-600">{{ $stats['formations'] }}</p>
        <p class="text-gray-500 text-sm mt-1">🎓 Formations</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
        <p class="text-3xl font-bold text-yellow-600">{{ $stats['services'] }}</p>
        <p class="text-gray-500 text-sm mt-1">💼 Services</p>
    </div>
</div>

{{-- STATS DEMANDES + REVENUS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
    <div class="bg-white rounded-xl shadow p-5 text-center">
        <p class="text-2xl font-bold text-gray-700">{{ $stats['demandes'] }}</p>
        <p class="text-gray-400 text-xs mt-1">📋 Demandes totales</p>
    </div>
    <div class="bg-yellow-50 rounded-xl shadow p-5 text-center border border-yellow-200">
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['en_attente'] }}</p>
        <p class="text-gray-400 text-xs mt-1">⏳ En attente</p>
    </div>
    <div class="bg-blue-50 rounded-xl shadow p-5 text-center border border-blue-200">
        <p class="text-2xl font-bold text-blue-600">{{ $stats['en_cours'] }}</p>
        <p class="text-gray-400 text-xs mt-1">🔄 En cours</p>
    </div>
    <div class="bg-green-50 rounded-xl shadow p-5 text-center border border-green-200">
        <p class="text-2xl font-bold text-green-600">
            {{ number_format($stats['revenus'], 0, ',', ' ') }} FCFA
        </p>
        <p class="text-gray-400 text-xs mt-1">💰 Revenus estimés</p>
    </div>
</div>

{{-- GRAPHIQUES --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- Inscriptions par mois --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-bold text-blue-900 mb-4">
            📈 Inscriptions (6 derniers mois)
        </h3>
        <canvas id="chartInscriptions" height="120"></canvas>
    </div>

    {{-- Répartition services --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-bold text-blue-900 mb-4">
            🥧 Répartition des demandes
        </h3>
        <canvas id="chartServices" height="120"></canvas>
    </div>
</div>

{{-- TABLEAUX --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- Inscriptions récentes --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-blue-900">🆕 Inscriptions récentes</h3>
            <a href="{{ route('admin.formations.index') }}"
                class="text-xs text-blue-600 hover:underline">Voir tout</a>
        </div>
        @forelse($inscriptionsRecentes as $inscription)
        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-blue-800 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr($inscription->user->prenom, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">
                        {{ $inscription->user->nom_complet }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $inscription->formation->titre }}</p>
                </div>
            </div>
            @php
                $badge = match($inscription->statut) {
                    'valide'     => 'bg-green-100 text-green-700',
                    'en_attente' => 'bg-yellow-100 text-yellow-700',
                    'refuse'     => 'bg-red-100 text-red-700',
                    default      => 'bg-gray-100 text-gray-600',
                };
            @endphp
            <span class="text-xs px-2 py-1 rounded-full font-medium {{ $badge }}">
                {{ ucfirst($inscription->statut) }}
            </span>
        </div>
        @empty
        <p class="text-gray-400 text-sm text-center py-4">Aucune inscription.</p>
        @endforelse
    </div>

    {{-- Demandes en attente --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-blue-900">⏳ Demandes en attente</h3>
            <a href="{{ route('admin.demandes.index') }}"
                class="text-xs text-blue-600 hover:underline">Voir tout</a>
        </div>
        @forelse($demandesEnAttente as $demande)
        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
            <div>
                <p class="text-sm font-medium text-gray-800">
                    {{ $demande->user?->nom_complet ?? $demande->nom_visiteur }}
                </p>
                <p class="text-xs text-gray-400">{{ $demande->service->titre }}</p>
                <p class="text-xs text-gray-300">{{ $demande->created_at->diffForHumans() }}</p>
            </div>
            <a href="{{ route('admin.demandes.index') }}"
                class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full hover:bg-yellow-200 transition font-medium">
                Traiter →
            </a>
        </div>
        @empty
        <p class="text-gray-400 text-sm text-center py-4">
            ✅ Aucune demande en attente.
        </p>
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
                backgroundColor: 'rgba(30, 58, 138, 0.7)',
                borderColor: 'rgba(30, 58, 138, 1)',
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
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
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
                    'rgba(30, 58, 138, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                ],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 12 } }
                }
            }
        }
    });
</script>
@endpush
@extends('layouts.admin')
@section('title', 'Témoignages')
@section('page_title', '⭐ Modération des Témoignages')
@section('page_subtitle', 'Validez ou refusez les avis des clients')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
    @foreach([
        ['total',       '⭐ Total',      'var(--edc-text-muted)'],
        ['en_attente',  '⏳ En attente', 'var(--edc-accent-gold)'],
        ['valides',     '✅ Publiés',    'var(--edc-secondary)'],
        ['refuses',     '❌ Refusés',    'var(--edc-danger)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">{{ $stats[$stat[0]] }}</p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

{{-- LISTE --}}
<div class="edc-card mt-6 overflow-hidden">
    <div class="px-6 py-4" style="border-bottom: 1px solid var(--edc-border);">
        <h3 class="font-bold" style="color: var(--edc-text-primary);">
            ⭐ {{ $temoignages->total() }} témoignage(s)
        </h3>
    </div>

    @forelse($temoignages as $temoignage)
    <div class="p-6 transition" style="border-bottom: 1px solid var(--edc-border);"
        onmouseover="this.style.backgroundColor='rgba(255,255,255,0.02)'"
        onmouseout="this.style.backgroundColor='transparent'">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-start space-x-4 flex-1">
                {{-- Avatar --}}
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                    style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                    {{ strtoupper(substr($temoignage->user->prenom, 0, 1)) }}
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-3 mb-1">
                        <p class="font-semibold text-sm" style="color: var(--edc-text-primary);">
                            {{ $temoignage->user->prenom }} {{ $temoignage->user->nom }}
                        </p>
                        <div class="text-sm" style="color: var(--edc-accent-gold);">
                            {!! $temoignage->etoiles_html !!}
                        </div>
                        <span class="text-xs" style="color: var(--edc-text-muted);">
                            {{ $temoignage->created_at->diffForHumans() }}
                        </span>
                    </div>

                    @if($temoignage->formation)
                    <span class="badge badge-blue">🎓 {{ $temoignage->formation->titre }}</span>
                    @elseif($temoignage->service)
                    <span class="badge badge-green">💼 {{ $temoignage->service->titre }}</span>
                    @endif

                    <p class="text-sm mt-2 leading-relaxed" style="color: var(--edc-text-secondary);">
                        "{{ $temoignage->contenu }}"
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex sm:flex-col items-end gap-2 flex-shrink-0">
                @php
                    $badgeStyle = match($temoignage->statut_validation) {
                        'valide' => 'background-color: rgba(16,185,129,0.12); color: #34D399;',
                        'refuse' => 'background-color: rgba(239,68,68,0.12); color: #F87171;',
                        default  => 'background-color: rgba(245,158,11,0.12); color: #FBBF24;',
                    };
                    $label = match($temoignage->statut_validation) {
                        'valide' => '✅ Publié',
                        'refuse' => '❌ Refusé',
                        default  => '⏳ En attente',
                    };
                @endphp
                <span class="badge text-xs" style="{{ $badgeStyle }}">{{ $label }}</span>

                <div class="flex space-x-2">
                    @if($temoignage->statut_validation !== 'valide')
                    <form method="POST" action="{{ route('admin.temoignages.valider', $temoignage) }}">
                        @csrf
                        <button type="submit" class="btn-success btn-xs">✅ Publier</button>
                    </form>
                    @endif

                    @if($temoignage->statut_validation !== 'refuse')
                    <form method="POST" action="{{ route('admin.temoignages.refuser', $temoignage) }}">
                        @csrf
                        <button type="submit" class="btn-danger btn-xs">❌ Refuser</button>
                    </form>
                    @endif

                    <form method="POST" action="{{ route('admin.temoignages.destroy', $temoignage) }}"
                        onsubmit="return confirm('Supprimer définitivement ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs hover:underline" style="color: var(--edc-danger);">🗑️</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-16" style="color: var(--edc-text-muted);">
        <p class="text-5xl mb-4">⭐</p>
        <p>Aucun témoignage pour le moment.</p>
    </div>
    @endforelse

    <div class="px-6 py-4" style="border-top: 1px solid var(--edc-border);">
        {{ $temoignages->links() }}
    </div>
</div>
@endsection
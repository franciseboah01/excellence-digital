@extends('layouts.admin')
@section('title', 'QCMs')
@section('page_title', '📝 Gestion des QCMs')
@section('page_subtitle', 'Supervision des QCMs et certificats')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-6">
    @foreach([
        ['total',          '📝 QCMs total',   'var(--edc-primary)'],
        ['actifs',         '✅ Actifs',       'var(--edc-secondary)'],
        ['sessions',       '🎯 Sessions',     '#60A5FA'],
        ['certificats',    '🏆 Certificats',  'var(--edc-accent-gold)'],
        ['taux_reussite',  '📈 Taux réussite','#A78BFA'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">{{ $stat[0] === 'taux_reussite' ? $stats[$stat[0]].'%' : $stats[$stat[0]] }}</p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

<div class="flex justify-end mt-5">
    <a href="{{ route('admin.certificats.index') }}" class="btn-sm rounded-lg text-sm font-bold transition"
        style="background: linear-gradient(135deg, #FBBF24, #F59E0B); color: #1a1a1a;">
        🏆 Voir les certificats
    </a>
</div>

{{-- TABLEAU --}}
<div class="edc-card mt-5 overflow-hidden">
    <div class="table-responsive">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th>QCM</th>
                    <th>Formation</th>
                    <th>Enseignant</th>
                    <th>Questions</th>
                    <th>Sessions</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($qcms as $qcm)
                <tr>
                    <td>
                        <p class="font-semibold" style="color: var(--edc-text-primary);">{{ $qcm->titre }}</p>
                        <p class="text-xs mt-0.5" style="color: var(--edc-text-muted);">
                            🎯 {{ $qcm->note_minimale }}/{{ $qcm->bareme }} requis • ⏱ {{ $qcm->duree_par_question }}s/Q
                        </p>
                    </td>
                    <td>
                        <span style="color: var(--edc-text-secondary);">{{ Str::limit($qcm->formation->titre, 25) }}</span>
                        @if($qcm->niveau)
                        <p class="text-xs" style="color: var(--edc-text-muted);">{{ $qcm->niveau->nom }}</p>
                        @endif
                    </td>
                    <td style="color: var(--edc-text-secondary);">{{ $qcm->createur->prenom }} {{ $qcm->createur->nom }}</td>
                    <td class="text-center">
                        <span class="text-sm font-bold" style="color: {{ $qcm->questions_count >= 10 ? 'var(--edc-secondary)' : 'var(--edc-accent-gold)' }};">
                            {{ $qcm->questions_count }}/10
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="text-sm font-bold" style="color: var(--edc-primary-light);">{{ $qcm->sessions_count }}</span>
                    </td>
                    <td>
                        <span class="badge text-xs" style="{{ $qcm->actif
                            ? 'background-color: rgba(16,185,129,0.12); color: #34D399;'
                            : 'background-color: rgba(148,163,184,0.10); color: #94A3B8;' }}">
                            {{ $qcm->actif ? '✅ Actif' : '⏸️ Inactif' }}
                        </span>
                    </td>
                    <td>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.qcms.show', $qcm) }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">👁️ Voir</a>
                            <form method="POST" action="{{ route('admin.qcms.toggle', $qcm) }}">
                                @csrf
                                <button class="text-xs font-medium hover:underline"
                                    style="color: {{ $qcm->actif ? 'var(--edc-accent-gold)' : 'var(--edc-secondary)' }};">
                                    {{ $qcm->actif ? '⏸️' : '▶️' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.qcms.destroy', $qcm) }}"
                                onsubmit="return confirm('Supprimer ce QCM ?')">
                                @csrf @method('DELETE')
                                <button class="text-xs font-medium hover:underline" style="color: var(--edc-danger);">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center" style="color: var(--edc-text-muted);">
                        <p class="text-4xl mb-3">📝</p>
                        <p>Aucun QCM créé par les enseignants.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4" style="border-top: 1px solid var(--edc-border);">{{ $qcms->links() }}</div>
</div>
@endsection
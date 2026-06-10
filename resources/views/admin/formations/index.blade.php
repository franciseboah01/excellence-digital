@extends('layouts.admin')
@section('title', 'Formations')
@section('page_title', '🎓 Gestion des Formations')
@section('page_subtitle', 'Créez et gérez les formations')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
    @foreach([
        ['total',     '🎓 Total',            'var(--edc-primary)'],
        ['publiees',  '✅ Publiées',         'var(--edc-secondary)'],
        ['brouillon', '📝 Brouillons',       'var(--edc-accent-gold)'],
        ['inscrits',  '👥 Inscrits validés', '#A78BFA'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">{{ $stats[$stat[0]] }}</p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

{{-- BOUTON AJOUTER --}}
<div class="flex justify-end mt-5">
    <a href="{{ route('admin.formations.create') }}" class="btn-primary btn-sm">➕ Nouvelle formation</a>
</div>

{{-- LISTE --}}
<div class="edc-card mt-4 overflow-hidden">
    <div class="table-responsive">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th>Formation</th>
                    <th>Module</th>
                    <th>Inscrits</th>
                    <th>Ressources</th>
                    <th>Prix</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($formations as $formation)
                <tr>
                    <td>
                        <div class="flex items-center space-x-3">
                            @if($formation->image)
                            <img src="{{ asset('storage/' . $formation->image) }}" class="w-10 h-10 rounded-lg object-cover">
                            @else
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl"
                                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">🎓</div>
                            @endif
                            <div>
                                <p class="font-semibold" style="color: var(--edc-text-primary);">{{ $formation->titre }}</p>
                                @if($formation->duree)
                                <p class="text-xs" style="color: var(--edc-text-muted);">⏱ {{ $formation->duree }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge text-xs" style="background-color: rgba(59,130,246,0.12); color: #60A5FA;">
                            {{ $formation->module->icone ?? '📚' }} {{ $formation->module->nom ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <div class="text-center">
                            <p class="font-bold" style="color: var(--edc-primary-light);">{{ $formation->inscrits_valides }}</p>
                            <p class="text-xs" style="color: var(--edc-text-muted);">/ {{ $formation->inscriptions_count }} total</p>
                        </div>
                    </td>
                    <td>
                        <span class="font-bold" style="color: #A78BFA;">{{ $formation->ressources_count }}</span>
                        <span class="text-xs" style="color: var(--edc-text-muted);"> fichier(s)</span>
                    </td>
                    <td>
                        <span class="font-semibold text-xs" style="color: var(--edc-primary-light);">
                            {{ $formation->prix ? number_format($formation->prix, 0, ',', ' ') . ' FCFA' : 'Gratuit' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge text-xs" style="{{ $formation->statut == 'publie'
                            ? 'background-color: rgba(16,185,129,0.12); color: #34D399;'
                            : 'background-color: rgba(245,158,11,0.12); color: #FBBF24;' }}">
                            {{ $formation->statut == 'publie' ? '✅ Publié' : '📝 Brouillon' }}
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.formations.show', $formation) }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">👁️ Gérer</a>
                            <a href="{{ route('admin.formations.edit', $formation) }}" class="text-xs font-medium hover:underline" style="color: var(--edc-accent-gold);">✏️ Modifier</a>
                            <form method="POST" action="{{ route('admin.formations.destroy', $formation) }}"
                                onsubmit="return confirm('Supprimer cette formation ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium hover:underline" style="color: var(--edc-danger);">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center" style="color: var(--edc-text-muted);">
                        <p class="text-5xl mb-4">🎓</p>
                        <p class="font-medium">Aucune formation créée.</p>
                        <a href="{{ route('admin.formations.create') }}" class="btn-primary btn-sm mt-4 inline-block">Créer la première formation</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4" style="border-top: 1px solid var(--edc-border);">{{ $formations->links() }}</div>
</div>
@endsection
@extends('layouts.admin')
@section('title', $formation->titre)
@section('page_title', '🎓 ' . $formation->titre)
@section('page_subtitle', 'Gestion complète de la formation')

@section('content')
<div class="mt-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
    <a href="{{ route('admin.formations.index') }}"
        class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
        style="color: var(--edc-primary-light);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Retour</span>
    </a>
    <a href="{{ route('admin.formations.edit', $formation) }}" class="btn-sm rounded-lg text-sm font-bold"
        style="background: linear-gradient(135deg, #FBBF24, #F59E0B); color: #1a1a1a;">
        ✏️ Modifier la formation
    </a>
</div>

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
    @foreach([
        ['inscriptions_count', '👥 Inscrits total', 'var(--edc-primary)'],
        ['inscrits_valides',   '✅ Validés',        'var(--edc-secondary)'],
        ['ressources_count',   '📚 Ressources',     '#A78BFA'],
        ['niveaux_count',      '📂 Niveaux',        'var(--edc-accent-gold)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">{{ $stat[0] === 'niveaux_count' ? $formation->niveaux->count() : $formation->{$stat[0]} }}</p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

    {{-- COLONNE GAUCHE --}}
    <div class="space-y-5">

        {{-- Infos --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">ℹ️ Informations</h3>
            @if($formation->image)
            <img src="{{ asset('storage/' . $formation->image) }}" class="w-full h-32 object-cover rounded-lg mb-4">
            @endif
            <ul class="space-y-2 text-sm">
                <li class="flex justify-between">
                    <span style="color: var(--edc-text-muted);">Niveau</span>
                    <span class="font-medium" style="color: var(--edc-text-primary);">{{ ucfirst($formation->niveau) }}</span>
                </li>
                <li class="flex justify-between">
                    <span style="color: var(--edc-text-muted);">Durée</span>
                    <span class="font-medium" style="color: var(--edc-text-primary);">{{ $formation->duree ?? '—' }}</span>
                </li>
                <li class="flex justify-between">
                    <span style="color: var(--edc-text-muted);">Statut</span>
                    <span class="badge text-xs" style="{{ $formation->statut == 'publie'
                        ? 'background-color: rgba(16,185,129,0.12); color: #34D399;'
                        : 'background-color: rgba(245,158,11,0.12); color: #FBBF24;' }}">
                        {{ $formation->statut == 'publie' ? '✅ Publié' : '📝 Brouillon' }}
                    </span>
                </li>
            </ul>
            <p class="text-sm mt-3 leading-relaxed" style="color: var(--edc-text-secondary);">{{ $formation->description }}</p>
        </div>

        {{-- Enseignants --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">👨‍🏫 Enseignants assignés</h3>
            <form method="POST" action="{{ route('admin.formations.assigner-enseignant', $formation) }}" class="mb-4">
                @csrf
                <div class="flex space-x-2">
                    <select name="enseignant_id" required class="edc-select flex-1">
                        <option value="">-- Assigner un enseignant --</option>
                        @foreach($enseignants as $enseignant)
                            @if(!$enseignantsFormation->contains('id', $enseignant->id))
                            <option value="{{ $enseignant->id }}">{{ $enseignant->prenom }} {{ $enseignant->nom }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary btn-sm">➕ Assigner</button>
                </div>
            </form>
            @forelse($enseignantsFormation as $enseignant)
            <div class="flex items-center justify-between py-3" style="border-bottom: 1px solid var(--edc-border);">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                        style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                        {{ strtoupper(substr($enseignant->prenom, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium" style="color: var(--edc-text-primary);">{{ $enseignant->prenom }} {{ $enseignant->nom }}</p>
                        <p class="text-xs" style="color: var(--edc-text-muted);">{{ $enseignant->email }}</p>
                        <p class="text-xs" style="color: #A78BFA;">📚 {{ $enseignant->ressources->where('formation_id', $formation->id)->count() }} ressource(s)</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.formations.retirer-enseignant', [$formation, $enseignant]) }}"
                    onsubmit="return confirm('Retirer cet enseignant ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-medium hover:underline" style="color: var(--edc-danger);">🗑️ Retirer</button>
                </form>
            </div>
            @empty
            <p class="text-sm text-center py-3" style="color: var(--edc-text-muted);">Aucun enseignant assigné.</p>
            @endforelse
        </div>

        {{-- Ajouter niveau --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">➕ Ajouter un niveau</h3>
            <form method="POST" action="{{ route('admin.formations.niveaux.store', $formation) }}" class="space-y-3">
                @csrf
                <input type="text" name="nom" placeholder="Nom du niveau *" required class="edc-input">
                <textarea name="description" rows="2" placeholder="Description (optionnel)" class="edc-input"></textarea>
                <button type="submit" class="btn-primary btn-sm w-full">➕ Ajouter</button>
            </form>
        </div>
    </div>

    {{-- COLONNE DROITE --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Niveaux --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">📂 Niveaux de la formation</h3>
            @forelse($formation->niveaux as $niveau)
            <div class="rounded-xl p-4 mb-3" style="border: 1px solid var(--edc-border);">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">{{ $niveau->ordre }}</span>
                            <h4 class="font-semibold" style="color: var(--edc-text-primary);">{{ $niveau->nom }}</h4>
                        </div>
                        @if($niveau->description)
                        <p class="text-xs mt-1 ml-9" style="color: var(--edc-text-muted);">{{ $niveau->description }}</p>
                        @endif
                        <p class="text-xs mt-1 ml-9" style="color: #A78BFA;">📚 {{ $niveau->ressources->count() }} ressource(s)</p>
                    </div>
                    <form method="POST" action="{{ route('admin.formations.niveaux.destroy', $niveau) }}"
                        onsubmit="return confirm('Supprimer ce niveau ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs hover:underline" style="color: var(--edc-danger);">🗑️ Supprimer</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-sm text-center py-4" style="color: var(--edc-text-muted);">Aucun niveau défini.</p>
            @endforelse
        </div>

        {{-- Inscriptions --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">👥 Inscriptions ({{ $formation->inscriptions->count() }})</h3>
            @forelse($formation->inscriptions as $inscription)
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 py-3" style="border-bottom: 1px solid var(--edc-border);">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                        style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                        {{ strtoupper(substr($inscription->user->prenom, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium" style="color: var(--edc-text-primary);">{{ $inscription->user->nom_complet }}</p>
                        <p class="text-xs" style="color: var(--edc-text-muted);">{{ $inscription->user->email }} • {{ $inscription->date_inscription->format('d/m/Y') }}</p>
                    </div>
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
                    <form method="POST" action="{{ route('admin.formations.inscription.valider', $inscription) }}">
                        @csrf
                        <button type="submit" class="btn-success btn-xs">✅</button>
                    </form>
                    <form method="POST" action="{{ route('admin.formations.inscription.rejeter', $inscription) }}">
                        @csrf
                        <button type="submit" class="btn-danger btn-xs">❌</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('admin.formations.inscription.desinscrire', $inscription) }}"
                        onsubmit="return confirm('Désinscrire ce client ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs hover:underline" style="color: var(--edc-danger);">🗑️</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-sm text-center py-4" style="color: var(--edc-text-muted);">Aucun inscrit pour le moment.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
@extends('layouts.enseignant')
@section('title', 'Dashboard Enseignant')

@section('content')

{{-- EN-TÊTE --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">
            Bonjour {{ auth()->user()->prenom }} 👋
        </h1>
        <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">
            Espace Enseignant — {{ now()->format('d/m/Y') }}
        </p>
    </div>
    <a href="{{ route('enseignant.ressources.create') }}" class="btn-primary">
        <span>➕</span><span>Ajouter une ressource</span>
    </a>
</div>

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['formations',       '🎓 Formations',       'var(--edc-primary)'],
        ['apprenants',       '👥 Apprenants',       'var(--edc-secondary)'],
        ['ressources',       '📚 Ressources',       '#A78BFA'],
        ['notifs_envoyees',  '🔔 Notifs envoyées',  'var(--edc-accent-gold)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">{{ $stats[$stat[0]] }}</p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- COLONNE PRINCIPALE --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- MES FORMATIONS --}}
        <div class="edc-card p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-lg font-bold" style="color: var(--edc-text-primary);">🎓 Mes Formations</h2>
                <a href="{{ route('enseignant.ressources.create') }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">
                    + Ajouter ressource
                </a>
            </div>

            @forelse($formations as $formation)
            <div class="rounded-xl p-4 mb-3 transition" style="border: 1px solid var(--edc-border);">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3">
                        @if($formation->image)
                        <img src="{{ asset('storage/' . $formation->image) }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                        @else
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl flex-shrink-0"
                            style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                            🎓
                        </div>
                        @endif

                        <div>
                            <h3 class="font-semibold text-sm" style="color: var(--edc-text-primary);">{{ $formation->titre }}</h3>
                            <div class="flex items-center space-x-3 mt-1">
                                <span class="badge badge-green">{{ ucfirst($formation->niveau) }}</span>
                                @if($formation->duree)
                                <span class="text-xs" style="color: var(--edc-text-muted);">⏱ {{ $formation->duree }}</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="text-xs font-medium" style="color: var(--edc-secondary);">
                                    👥 {{ $formation->total_apprenants }} apprenant(s)
                                </span>
                                <span class="text-xs font-medium" style="color: #A78BFA;">
                                    📚 {{ $formation->ressources_count }} ressource(s)
                                </span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('enseignant.ressources.create') }}?formation={{ $formation->id }}"
                        class="btn-primary btn-xs flex-shrink-0">
                        + Ressource
                    </a>
                </div>

                {{-- Barre de progression --}}
                @php
                    $maxRessources = 10;
                    $pct = min(100, ($formation->ressources_count / $maxRessources) * 100);
                @endphp
                <div class="mt-3">
                    <div class="flex justify-between text-xs mb-1" style="color: var(--edc-text-muted);">
                        <span>Ressources ajoutées</span>
                        <span>{{ $formation->ressources_count }} / {{ $maxRessources }}</span>
                    </div>
                    <div class="w-full rounded-full h-1.5" style="background-color: var(--edc-bg-elevated);">
                        <div class="h-1.5 rounded-full transition-all" style="width: {{ $pct }}%; background: linear-gradient(135deg, #3B82F6, #1D4ED8);"></div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10" style="color: var(--edc-text-muted);">
                <p class="text-4xl mb-3">🎓</p>
                <p class="text-sm font-medium">Aucune formation assignée.</p>
                <p class="text-xs mt-1">Contactez l'administrateur pour être assigné à une formation.</p>
            </div>
            @endforelse
        </div>

        {{-- DERNIÈRES RESSOURCES --}}
        <div class="edc-card p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-lg font-bold" style="color: var(--edc-text-primary);">📚 Dernières ressources ajoutées</h2>
                <a href="{{ route('enseignant.ressources.index') }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">Voir tout</a>
            </div>

            @forelse($dernieres_ressources as $ressource)
            @php
                $configR = match($ressource->type) {
                    'pdf'      => ['rgba(239,68,68,0.08)', '📄', '#F87171'],
                    'ebook'    => ['rgba(168,85,247,0.08)', '📖', '#C084FC'],
                    'lien'     => ['rgba(16,185,129,0.08)', '🔗', '#34D399'],
                    'video'    => ['rgba(245,158,11,0.08)', '🎬', '#FBBF24'],
                    'document' => ['rgba(59,130,246,0.08)', '📝', '#60A5FA'],
                    default    => ['rgba(148,163,184,0.08)', '📎', '#94A3B8'],
                };
            @endphp
            <div class="flex items-center justify-between py-3" style="border-bottom: 1px solid var(--edc-border);">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center text-lg flex-shrink-0"
                        style="background-color: {{ $configR[0] }}; border: 1px solid {{ $configR[2] }}33;">
                        {{ $configR[1] }}
                    </div>
                    <div>
                        <p class="text-sm font-medium" style="color: var(--edc-text-primary);">
                            {{ Str::limit($ressource->titre, 40) }}
                        </p>
                        <div class="flex items-center space-x-2 mt-0.5">
                            <span class="text-xs" style="color: var(--edc-primary-light);">
                                {{ $ressource->formation->titre }}
                            </span>
                            @if($ressource->niveau)
                            <span class="text-xs" style="color: var(--edc-text-muted);">
                                • {{ $ressource->niveau->nom }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3 flex-shrink-0">
                    <span class="text-xs" style="color: var(--edc-text-muted);">
                        {{ $ressource->created_at->diffForHumans() }}
                    </span>
                    <a href="{{ route('enseignant.ressources.edit', $ressource) }}" class="text-xs hover:underline" style="color: var(--edc-primary-light);">
                        ✏️
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-8" style="color: var(--edc-text-muted);">
                <p class="text-4xl mb-3">📭</p>
                <p class="text-sm">Aucune ressource ajoutée.</p>
                <a href="{{ route('enseignant.ressources.create') }}" class="btn-primary btn-sm mt-3 inline-block">
                    Ajouter ma première ressource
                </a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- COLONNE DROITE --}}
    <div class="space-y-6">

        {{-- PROFIL RAPIDE --}}
        <div class="edc-card p-6 text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-3"
                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-full h-full rounded-full object-cover">
                @else
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                @endif
            </div>
            <h3 class="font-bold" style="color: var(--edc-text-primary);">{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</h3>
            <p class="text-xs mt-1" style="color: var(--edc-text-muted);">{{ auth()->user()->email }}</p>
            <span class="badge badge-green mt-2">👨‍🏫 Enseignant</span>
            @if(auth()->user()->telephone)
            <p class="text-xs mt-2" style="color: var(--edc-text-muted);">📲 {{ auth()->user()->telephone }}</p>
            @endif
        </div>

        {{-- ACTIONS RAPIDES --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">⚡ Actions rapides</h3>
            <div class="space-y-2">
                @foreach([
                    ['enseignant.ressources.create',       '📤', 'Uploader une ressource'],
                    ['enseignant.notifications.form',      '🔔', 'Envoyer une notification'],
                    ['enseignant.notifications.form',      '✉️', 'Envoyer un email'],
                    ['enseignant.ressources.index',        '📚', 'Gérer mes ressources'],
                ] as $a)
                <a href="{{ route($a[0]) }}"
                    class="flex items-center space-x-3 w-full px-4 py-3 rounded-xl transition text-sm font-medium"
                    style="background-color: var(--edc-bg-base); color: var(--edc-text-secondary); border: 1px solid var(--edc-border);"
                    onmouseover="this.style.backgroundColor='var(--edc-bg-elevated)'; this.style.color='var(--edc-primary-light)'; this.style.borderColor='var(--edc-primary)';"
                    onmouseout="this.style.backgroundColor='var(--edc-bg-base)'; this.style.color='var(--edc-text-secondary)'; this.style.borderColor='var(--edc-border)';">
                    <span>{{ $a[1] }}</span><span>{{ $a[2] }}</span>
                </a>
                @endforeach
            </div>
        </div>

        {{-- RÉPARTITION PAR TYPE --}}
        @if($stats['ressources'] > 0)
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">📊 Mes ressources par type</h3>
            <div class="space-y-3">
                @foreach($repartitionTypes as $type => $count)
                @php
                    $typeConfig = match($type) {
                        'pdf'      => ['📄', '#EF4444', 'PDF'],
                        'ebook'    => ['📖', '#A855F7', 'Ebooks'],
                        'lien'     => ['🔗', '#10B981', 'Liens'],
                        'video'    => ['🎬', '#F59E0B', 'Vidéos'],
                        'document' => ['📝', '#3B82F6', 'Documents'],
                        default    => ['📎', '#64748B', $type],
                    };
                    $pctType = $stats['ressources'] > 0 ? round(($count / $stats['ressources']) * 100) : 0;
                @endphp
                <div>
                    <div class="flex justify-between text-xs mb-1" style="color: var(--edc-text-secondary);">
                        <span>{{ $typeConfig[0] }} {{ $typeConfig[2] }}</span>
                        <span class="font-semibold">{{ $count }} ({{ $pctType }}%)</span>
                    </div>
                    <div class="w-full rounded-full h-2" style="background-color: var(--edc-bg-elevated);">
                        <div class="h-2 rounded-full transition-all" style="width: {{ $pctType }}%; background-color: {{ $typeConfig[1] }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- APPRENANTS PAR FORMATION --}}
        <div class="edc-card p-6">
            <h3 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">👥 Apprenants par formation</h3>
            @forelse($formations as $formation)
            <div class="flex items-center justify-between py-2" style="border-bottom: 1px solid var(--edc-border);">
                <p class="text-sm truncate flex-1 mr-3" style="color: var(--edc-text-secondary);">
                    {{ Str::limit($formation->titre, 25) }}
                </p>
                <div class="flex items-center space-x-1 flex-shrink-0">
                    @php $total = min($formation->total_apprenants, 5); @endphp
                    @for($i = 0; $i < $total; $i++)
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold"
                        style="background-color: {{ ['#3B82F6','#2563EB','#1D4ED8','#1E40AF','#1E3A8A'][$i] }}; border: 2px solid var(--edc-bg-card); margin-left: -8px;">
                        {{ $i + 1 }}
                    </div>
                    @endfor
                    @if($formation->total_apprenants > 5)
                    <span class="text-xs ml-1" style="color: var(--edc-text-muted);">+{{ $formation->total_apprenants - 5 }}</span>
                    @endif
                    @if($formation->total_apprenants == 0)
                    <span class="text-xs" style="color: var(--edc-text-muted);">Aucun</span>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-sm text-center py-3" style="color: var(--edc-text-muted);">Aucune formation.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
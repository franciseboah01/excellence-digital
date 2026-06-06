@extends('layouts.enseignant')
@section('title', 'Dashboard Enseignant')

@section('content')

{{-- EN-TÊTE --}}
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-blue-900">
            Bonjour {{ auth()->user()->prenom }} 👋
        </h1>
        <p class="text-gray-500 mt-1 text-sm">
            Espace Enseignant — {{ now()->format('d/m/Y') }}
        </p>
    </div>
    <a href="{{ route('enseignant.ressources.create') }}"
        class="mt-4 md:mt-0 inline-flex items-center space-x-2 bg-blue-800 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-blue-900 transition text-sm">
        <span>➕</span><span>Ajouter une ressource</span>
    </a>
</div>

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-600">
        <p class="text-3xl font-bold text-blue-700">{{ $stats['formations'] }}</p>
        <p class="text-gray-500 text-sm mt-1">🎓 Formations</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-3xl font-bold text-green-600">{{ $stats['apprenants'] }}</p>
        <p class="text-gray-500 text-sm mt-1">👥 Apprenants</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
        <p class="text-3xl font-bold text-purple-600">{{ $stats['ressources'] }}</p>
        <p class="text-gray-500 text-sm mt-1">📚 Ressources</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
        <p class="text-3xl font-bold text-yellow-600">{{ $stats['notifs_envoyees'] }}</p>
        <p class="text-gray-500 text-sm mt-1">🔔 Notifs envoyées</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- COLONNE PRINCIPALE --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- MES FORMATIONS --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-lg font-bold text-blue-900">🎓 Mes Formations</h2>
                <a href="{{ route('enseignant.ressources.create') }}"
                    class="text-xs text-blue-600 hover:underline">
                    + Ajouter ressource
                </a>
            </div>

            @forelse($formations as $formation)
            <div class="border border-gray-200 rounded-xl p-4 mb-3 hover:border-blue-300 transition">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3">
                        {{-- Image ou icône --}}
                        @if($formation->image)
                        <img src="{{ asset('storage/' . $formation->image) }}"
                            class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                        @else
                        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-2xl flex-shrink-0">
                            🎓
                        </div>
                        @endif

                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $formation->titre }}</h3>
                            <div class="flex items-center space-x-3 mt-1">
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                                    {{ ucfirst($formation->niveau) }}
                                </span>
                                @if($formation->duree)
                                <span class="text-xs text-gray-400">⏱ {{ $formation->duree }}</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="text-xs text-green-600 font-medium">
                                    👥 {{ $formation->total_apprenants }} apprenant(s)
                                </span>
                                <span class="text-xs text-purple-600 font-medium">
                                    📚 {{ $formation->ressources_count }} ressource(s)
                                </span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('enseignant.ressources.create') }}?formation={{ $formation->id }}"
                        class="text-xs bg-blue-800 text-white px-3 py-1.5 rounded-lg hover:bg-blue-900 transition font-medium flex-shrink-0">
                        + Ressource
                    </a>
                </div>

                {{-- Barre de progression ressources --}}
                @php
                    $maxRessources = 10;
                    $pct = min(100, ($formation->ressources_count / $maxRessources) * 100);
                @endphp
                <div class="mt-3">
                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>Ressources ajoutées</span>
                        <span>{{ $formation->ressources_count }} / {{ $maxRessources }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-blue-600 h-1.5 rounded-full transition-all"
                            style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-gray-400">
                <p class="text-4xl mb-3">🎓</p>
                <p class="text-sm font-medium">Aucune formation assignée.</p>
                <p class="text-xs mt-1">Contactez l'administrateur pour être assigné à une formation.</p>
            </div>
            @endforelse
        </div>

        {{-- DERNIÈRES RESSOURCES --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-lg font-bold text-blue-900">📚 Dernières ressources ajoutées</h2>
                <a href="{{ route('enseignant.ressources.index') }}"
                    class="text-xs text-blue-600 hover:underline">Voir tout</a>
            </div>

            @forelse($dernieres_ressources as $ressource)
            @php
                $config = match($ressource->type) {
                    'pdf'      => ['bg-red-50 border-red-200',    '📄', 'text-red-700'],
                    'ebook'    => ['bg-purple-50 border-purple-200','📖','text-purple-700'],
                    'lien'     => ['bg-green-50 border-green-200', '🔗', 'text-green-700'],
                    'video'    => ['bg-yellow-50 border-yellow-200','🎬','text-yellow-700'],
                    'document' => ['bg-blue-50 border-blue-200',  '📝', 'text-blue-700'],
                    default    => ['bg-gray-50 border-gray-200',  '📎', 'text-gray-700'],
                };
            @endphp
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg {{ $config[0] }} border flex items-center justify-center text-lg flex-shrink-0">
                        {{ $config[1] }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            {{ Str::limit($ressource->titre, 40) }}
                        </p>
                        <div class="flex items-center space-x-2 mt-0.5">
                            <span class="text-xs text-blue-600">
                                {{ $ressource->formation->titre }}
                            </span>
                            @if($ressource->niveau)
                            <span class="text-xs text-gray-400">
                                • {{ $ressource->niveau->nom }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3 flex-shrink-0">
                    <span class="text-xs text-gray-300">
                        {{ $ressource->created_at->diffForHumans() }}
                    </span>
                    <a href="{{ route('enseignant.ressources.edit', $ressource) }}"
                        class="text-xs text-blue-600 hover:underline">
                        ✏️
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">
                <p class="text-4xl mb-3">📭</p>
                <p class="text-sm">Aucune ressource ajoutée.</p>
                <a href="{{ route('enseignant.ressources.create') }}"
                    class="inline-block mt-3 bg-blue-800 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-900 transition">
                    Ajouter ma première ressource
                </a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- COLONNE DROITE --}}
    <div class="space-y-6">

        {{-- PROFIL RAPIDE --}}
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-green-700 flex items-center justify-center text-white text-2xl font-bold mx-auto mb-3">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                        class="w-full h-full rounded-full object-cover">
                @else
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                @endif
            </div>
            <h3 class="font-bold text-blue-900">{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</h3>
            <p class="text-xs text-gray-400 mt-1">{{ auth()->user()->email }}</p>
            <span class="inline-block mt-2 text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-medium">
                👨‍🏫 Enseignant
            </span>
            @if(auth()->user()->telephone)
            <p class="text-xs text-gray-400 mt-2">📲 {{ auth()->user()->telephone }}</p>
            @endif
        </div>

        {{-- ACTIONS RAPIDES --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">⚡ Actions rapides</h3>
            <div class="space-y-2">
                <a href="{{ route('enseignant.ressources.create') }}"
                    class="flex items-center space-x-3 w-full px-4 py-3 bg-blue-50 text-blue-800 rounded-xl hover:bg-blue-100 transition text-sm font-medium">
                    <span>📤</span><span>Uploader une ressource</span>
                </a>
                <a href="{{ route('enseignant.notifications.form') }}"
                    class="flex items-center space-x-3 w-full px-4 py-3 bg-yellow-50 text-yellow-800 rounded-xl hover:bg-yellow-100 transition text-sm font-medium">
                    <span>🔔</span><span>Envoyer une notification</span>
                </a>
                <a href="{{ route('enseignant.notifications.form') }}"
                    class="flex items-center space-x-3 w-full px-4 py-3 bg-green-50 text-green-800 rounded-xl hover:bg-green-100 transition text-sm font-medium">
                    <span>✉️</span><span>Envoyer un email</span>
                </a>
                <a href="{{ route('enseignant.ressources.index') }}"
                    class="flex items-center space-x-3 w-full px-4 py-3 bg-purple-50 text-purple-800 rounded-xl hover:bg-purple-100 transition text-sm font-medium">
                    <span>📚</span><span>Gérer mes ressources</span>
                </a>
            </div>
        </div>

        {{-- RÉPARTITION PAR TYPE --}}
        @if($stats['ressources'] > 0)
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">📊 Mes ressources par type</h3>
            <div class="space-y-3">
                @foreach($repartitionTypes as $type => $count)
                @php
                    $typeConfig = match($type) {
                        'pdf'      => ['📄', 'bg-red-500',    'PDF'],
                        'ebook'    => ['📖', 'bg-purple-500', 'Ebooks'],
                        'lien'     => ['🔗', 'bg-green-500',  'Liens'],
                        'video'    => ['🎬', 'bg-yellow-500', 'Vidéos'],
                        'document' => ['📝', 'bg-blue-500',   'Documents'],
                        default    => ['📎', 'bg-gray-500',   $type],
                    };
                    $pctType = $stats['ressources'] > 0
                        ? round(($count / $stats['ressources']) * 100)
                        : 0;
                @endphp
                <div>
                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                        <span>{{ $typeConfig[0] }} {{ $typeConfig[2] }}</span>
                        <span class="font-semibold">{{ $count }} ({{ $pctType }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="{{ $typeConfig[1] }} h-2 rounded-full transition-all"
                            style="width: {{ $pctType }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- APPRENANTS PAR FORMATION --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">👥 Apprenants par formation</h3>
            @forelse($formations as $formation)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <p class="text-sm text-gray-700 truncate flex-1 mr-3">
                    {{ Str::limit($formation->titre, 25) }}
                </p>
                <div class="flex items-center space-x-1 flex-shrink-0">
                    @php $total = min($formation->total_apprenants, 5); @endphp
                    @for($i = 0; $i < $total; $i++)
                    <div class="w-6 h-6 rounded-full bg-blue-{{ 400 + ($i * 100) }} border-2 border-white -ml-1 first:ml-0 flex items-center justify-center text-white text-xs font-bold">
                        {{ $i + 1 }}
                    </div>
                    @endfor
                    @if($formation->total_apprenants > 5)
                    <span class="text-xs text-gray-400 ml-1">
                        +{{ $formation->total_apprenants - 5 }}
                    </span>
                    @endif
                    @if($formation->total_apprenants == 0)
                    <span class="text-xs text-gray-300">Aucun</span>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-3">Aucune formation.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
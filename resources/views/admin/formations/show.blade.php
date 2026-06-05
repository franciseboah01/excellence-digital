@extends('layouts.admin')
@section('title', $formation->titre)
@section('page_title', $formation->titre)
@section('page_subtitle', 'Gestion complète de la formation')

@section('content')
<div class="mt-4 flex justify-between items-center">
    <a href="{{ route('admin.formations.index') }}"
        class="text-blue-600 hover:underline text-sm">← Retour</a>
    <a href="{{ route('admin.formations.edit', $formation) }}"
        class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-yellow-600 transition">
        ✏️ Modifier la formation
    </a>
</div>

{{-- STATS FORMATION --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-blue-600">
        <p class="text-2xl font-bold text-blue-700">{{ $formation->inscriptions_count }}</p>
        <p class="text-gray-500 text-xs mt-1">👥 Inscrits total</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-green-500">
        <p class="text-2xl font-bold text-green-600">{{ $formation->inscrits_valides }}</p>
        <p class="text-gray-500 text-xs mt-1">✅ Validés</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-purple-500">
        <p class="text-2xl font-bold text-purple-600">{{ $formation->ressources_count }}</p>
        <p class="text-gray-500 text-xs mt-1">📚 Ressources</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-yellow-500">
        <p class="text-2xl font-bold text-yellow-600">{{ $formation->niveaux->count() }}</p>
        <p class="text-gray-500 text-xs mt-1">📂 Niveaux</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

    {{-- COLONNE GAUCHE --}}
    <div class="space-y-5">

        {{-- Infos formation --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">ℹ️ Informations</h3>
            @if($formation->image)
            <img src="{{ asset('storage/' . $formation->image) }}"
                class="w-full h-32 object-cover rounded-lg mb-4">
            @endif
            <ul class="space-y-2 text-sm">
                <li class="flex justify-between">
                    <span class="text-gray-500">Niveau</span>
                    <span class="font-medium">{{ ucfirst($formation->niveau) }}</span>
                </li>
                <li class="flex justify-between">
                    <span class="text-gray-500">Durée</span>
                    <span class="font-medium">{{ $formation->duree ?? '—' }}</span>
                </li>
                <li class="flex justify-between">
                    <span class="text-gray-500">Statut</span>
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                        {{ $formation->statut == 'publie' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $formation->statut == 'publie' ? '✅ Publié' : '📝 Brouillon' }}
                    </span>
                </li>
            </ul>
            <p class="text-sm text-gray-500 mt-3 leading-relaxed">
                {{ $formation->description }}
            </p>
        </div>

        {{-- Enseignants assignés --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">👨‍🏫 Enseignants assignés</h3>

            {{-- Formulaire assignation --}}
            <form method="POST"
                action="{{ route('admin.formations.assigner-enseignant', $formation) }}"
                class="mb-4">
                @csrf
                <div class="flex space-x-2">
                    <select name="enseignant_id" required
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Assigner un enseignant --</option>
                        @foreach($enseignants as $enseignant)
                            @if(!$enseignantsFormation->contains('id', $enseignant->id))
                            <option value="{{ $enseignant->id }}">
                                {{ $enseignant->prenom }} {{ $enseignant->nom }}
                            </option>
                            @endif
                        @endforeach
                    </select>
                    <button type="submit"
                        class="bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
                        ➕ Assigner
                    </button>
                </div>
            </form>

            {{-- Liste enseignants assignés --}}
            @forelse($enseignantsFormation as $enseignant)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-green-700 flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr($enseignant->prenom, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            {{ $enseignant->prenom }} {{ $enseignant->nom }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $enseignant->email }}</p>
                        <p class="text-xs text-purple-600">
                            📚 {{ $enseignant->ressources->where('formation_id', $formation->id)->count() }} ressource(s)
                        </p>
                    </div>
                </div>
                <form method="POST"
                    action="{{ route('admin.formations.retirer-enseignant', [$formation, $enseignant]) }}"
                    onsubmit="return confirm('Retirer cet enseignant ? Ses ressources seront supprimées.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="text-xs text-red-600 hover:underline font-medium">
                        🗑️ Retirer
                    </button>
                </form>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-3">
                Aucun enseignant assigné.<br>
                <span class="text-xs">Utilisez le formulaire ci-dessus.</span>
            </p>
            @endforelse
        </div>

        {{-- Ajouter un niveau --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">➕ Ajouter un niveau</h3>
            <form method="POST"
                action="{{ route('admin.formations.niveaux.store', $formation) }}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="nom" placeholder="Nom du niveau *" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-3">
                    <textarea name="description" rows="2" placeholder="Description (optionnel)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <button type="submit"
                    class="w-full bg-blue-800 text-white py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
                    ➕ Ajouter
                </button>
            </form>
        </div>
    </div>

    {{-- COLONNE DROITE --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Niveaux de la formation --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">📂 Niveaux de la formation</h3>
            @forelse($formation->niveaux as $niveau)
            <div class="border border-gray-200 rounded-xl p-4 mb-3 hover:border-blue-300 transition">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="w-7 h-7 rounded-full bg-blue-800 text-white text-xs flex items-center justify-center font-bold">
                                {{ $niveau->ordre }}
                            </span>
                            <h4 class="font-semibold text-gray-800">{{ $niveau->nom }}</h4>
                        </div>
                        @if($niveau->description)
                        <p class="text-xs text-gray-400 mt-1 ml-9">{{ $niveau->description }}</p>
                        @endif
                        <p class="text-xs text-purple-600 mt-1 ml-9">
                            📚 {{ $niveau->ressources->count() }} ressource(s)
                        </p>
                    </div>
                    <form method="POST"
                        action="{{ route('admin.formations.niveaux.destroy', $niveau) }}"
                        onsubmit="return confirm('Supprimer ce niveau ?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="text-xs text-red-500 hover:underline">
                            🗑️ Supprimer
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">Aucun niveau défini.</p>
            @endforelse
        </div>

        {{-- Inscriptions --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">
                👥 Inscriptions ({{ $formation->inscriptions->count() }})
            </h3>

            @forelse($formation->inscriptions as $inscription)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-blue-700 flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($inscription->user->prenom, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            {{ $inscription->user->nom_complet }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $inscription->user->email }} •
                            {{ $inscription->date_inscription->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    @php
                        $si = match($inscription->statut) {
                            'valide'     => ['bg-green-100 text-green-700', '✅ Validé'],
                            'en_attente' => ['bg-yellow-100 text-yellow-700', '⏳ En attente'],
                            'refuse'     => ['bg-red-100 text-red-700', '❌ Refusé'],
                            default      => ['bg-gray-100 text-gray-600', $inscription->statut],
                        };
                    @endphp
                    <span class="text-xs px-2 py-1 rounded-full font-medium {{ $si[0] }}">
                        {{ $si[1] }}
                    </span>
                    @if($inscription->statut === 'en_attente')
                    <form method="POST"
                        action="{{ route('admin.formations.inscription.valider', $inscription) }}">
                        @csrf
                        <button type="submit"
                            class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">
                            ✅
                        </button>
                    </form>
                    <form method="POST"
                        action="{{ route('admin.formations.inscription.rejeter', $inscription) }}">
                        @csrf
                        <button type="submit"
                            class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                            ❌
                        </button>
                    </form>
                    @endif
                    <form method="POST"
                        action="{{ route('admin.formations.inscription.desinscrire', $inscription) }}"
                        onsubmit="return confirm('Désinscrire ce client ?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="text-xs text-red-500 hover:underline font-medium">
                            🗑️
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">
                Aucun inscrit pour le moment.
            </p>
            @endforelse
        </div>
    </div>
</div>
@endsection
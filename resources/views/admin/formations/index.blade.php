@extends('layouts.admin')
@section('title', 'Formations')
@section('page_title', 'Gestion des Formations')
@section('page_subtitle', 'Créez et gérez les formations')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-blue-600">
        <p class="text-2xl font-bold text-blue-700">{{ $stats['total'] }}</p>
        <p class="text-gray-500 text-xs mt-1">🎓 Total</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-green-500">
        <p class="text-2xl font-bold text-green-600">{{ $stats['publiees'] }}</p>
        <p class="text-gray-500 text-xs mt-1">✅ Publiées</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-yellow-500">
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['brouillon'] }}</p>
        <p class="text-gray-500 text-xs mt-1">📝 Brouillons</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-purple-500">
        <p class="text-2xl font-bold text-purple-600">{{ $stats['inscrits'] }}</p>
        <p class="text-gray-500 text-xs mt-1">👥 Inscrits validés</p>
    </div>
</div>

{{-- BOUTON AJOUTER --}}
<div class="flex justify-end mt-5">
    <a href="{{ route('admin.formations.create') }}"
        class="bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
        ➕ Nouvelle formation
    </a>
</div>

{{-- LISTE --}}
<div class="bg-white rounded-xl shadow mt-4 overflow-hidden">
    <div class="table-responsive">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Formation</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Niveau</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Inscrits</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ressources</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($formations as $formation)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <div class="flex items-center space-x-3">
                            @if($formation->image)
                            <img src="{{ asset('storage/' . $formation->image) }}"
                                class="w-10 h-10 rounded-lg object-cover">
                            @else
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-xl">
                                🎓
                            </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-800">{{ $formation->titre }}</p>
                                @if($formation->duree)
                                <p class="text-xs text-gray-400">⏱ {{ $formation->duree }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $niveauBadge = match($formation->niveau) {
                                'debutant'      => 'bg-green-100 text-green-700',
                                'intermediaire' => 'bg-yellow-100 text-yellow-700',
                                'avance'        => 'bg-red-100 text-red-700',
                                default         => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="text-xs px-2 py-1 rounded-full font-medium {{ $niveauBadge }}">
                            {{ ucfirst($formation->niveau) }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="text-center">
                            <p class="font-bold text-blue-700">{{ $formation->inscrits_valides }}</p>
                            <p class="text-xs text-gray-400">/ {{ $formation->inscriptions_count }} total</p>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-purple-700 font-bold">{{ $formation->ressources_count }}</span>
                        <span class="text-gray-400 text-xs"> fichier(s)</span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-xs px-2 py-1 rounded-full font-medium
                            {{ $formation->statut == 'publie'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $formation->statut == 'publie' ? '✅ Publié' : '📝 Brouillon' }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.formations.show', $formation) }}"
                                class="text-xs text-blue-600 hover:underline font-medium">
                                👁️ Gérer
                            </a>
                            <a href="{{ route('admin.formations.edit', $formation) }}"
                                class="text-xs text-yellow-600 hover:underline font-medium">
                                ✏️ Modifier
                            </a>
                            <form method="POST"
                                action="{{ route('admin.formations.destroy', $formation) }}"
                                onsubmit="return confirm('Supprimer cette formation ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-xs text-red-600 hover:underline font-medium">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center text-gray-400">
                        <p class="text-5xl mb-4">🎓</p>
                        <p class="font-medium">Aucune formation créée.</p>
                        <a href="{{ route('admin.formations.create') }}"
                            class="inline-block mt-4 bg-blue-800 text-white px-6 py-2 rounded-lg text-sm">
                            Créer la première formation
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $formations->links() }}
    </div>
</div>
@endsection
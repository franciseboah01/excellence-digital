@extends('layouts.admin')
@section('title', 'Notifications')
@section('page_title', 'Système de Notifications')
@section('page_subtitle', 'Envoyez des notifications aux utilisateurs')

@section('content')

    {{-- STATS --}}
    <div class="grid grid-cols-3 gap-4 mt-6">
        <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-blue-600">
            <p class="text-2xl font-bold text-blue-700">{{ $stats['total'] }}</p>
            <p class="text-gray-500 text-xs mt-1">📢 Total envoyées</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-red-500">
            <p class="text-2xl font-bold text-red-600">{{ $stats['non_lues'] }}</p>
            <p class="text-gray-500 text-xs mt-1">🔴 Non lues</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-green-500">
            <p class="text-2xl font-bold text-green-600">{{ $stats['aujourdhui'] }}</p>
            <p class="text-gray-500 text-xs mt-1">📅 Aujourd'hui</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

        {{-- NOTIFICATION CIBLÉE --}}
        <div class="bg-white rounded-xl shadow p-6" x-data="{ onglet: 'cible' }">
            <h3 class="text-lg font-bold text-blue-900 mb-1">📤 Envoyer une notification</h3>
            <p class="text-gray-400 text-sm mb-5">Ciblée, groupée ou diffusion générale</p>

            {{-- ONGLETS --}}
            <div class="flex space-x-1 bg-gray-100 rounded-xl p-1 mb-5">
                <button @click="onglet = 'cible'" :class="onglet === 'cible'
                        ? 'bg-white shadow text-blue-800 font-semibold'
                        : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-2 rounded-lg text-xs transition">
                    👤 Ciblée
                </button>
                <button @click="onglet = 'groupe'" :class="onglet === 'groupe'
                        ? 'bg-white shadow text-blue-800 font-semibold'
                        : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-2 rounded-lg text-xs transition">
                    🎓 Par formation
                </button>
                <button @click="onglet = 'tous'" :class="onglet === 'tous'
                        ? 'bg-white shadow text-blue-800 font-semibold'
                        : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-2 rounded-lg text-xs transition">
                    📢 Diffusion
                </button>
            </div>

            {{-- ONGLET 1 : CIBLÉE --}}
            <div x-show="onglet === 'cible'">
                <form method="POST" action="{{ route('admin.notifications.cible') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Destinataire *
                        </label>
                        <select name="user_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Choisir un utilisateur --</option>
                            <optgroup label="👥 Clients">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->nom_complet }} ({{ $client->email }})
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="👨‍🏫 Enseignants">
                                @foreach($enseignants as $enseignant)
                                    <option value="{{ $enseignant->id }}">
                                        {{ $enseignant->nom_complet }} ({{ $enseignant->email }})
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    @include('admin.partials.notif-form-fields')
                    <button type="submit"
                        class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition text-sm">
                        📤 Envoyer
                    </button>
                </form>
            </div>

            {{-- ONGLET 2 : PAR FORMATION --}}
            <div x-show="onglet === 'groupe'">
                <form method="POST" action="{{ route('admin.notifications.groupe') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Formation *
                        </label>
                        <select name="formation_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Choisir une formation --</option>
                            @foreach($formations as $formation)
                                <option value="{{ $formation->id }}">
                                    {{ $formation->titre }}
                                    ({{ $formation->inscrits_valides }} apprenant(s))
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @include('admin.partials.notif-form-fields')
                    <button type="submit"
                        class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition text-sm">
                        📤 Envoyer au groupe
                    </button>
                </form>
            </div>

            {{-- ONGLET 3 : DIFFUSION --}}
            <div x-show="onglet === 'tous'">
                <form method="POST" action="{{ route('admin.notifications.tous') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Cible *
                        </label>
                        <select name="cible" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="clients">👥 Tous les clients</option>
                            <option value="enseignants">👨‍🏫 Tous les enseignants</option>
                            <option value="tous">🌐 Tout le monde</option>
                        </select>
                    </div>
                    @include('admin.partials.notif-form-fields')
                    <button type="submit"
                        class="w-full bg-red-700 text-white py-3 rounded-xl font-bold hover:bg-red-800 transition text-sm">
                        📢 Diffuser à tous
                    </button>
                </form>
            </div>
        </div>

        {{-- HISTORIQUE --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-5">📋 Historique récent</h3>

            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                @forelse($historique as $notif)
                    <div class="flex items-start justify-between p-3 rounded-xl border
                        {{ !$notif->lu ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }}">
                        <div class="flex items-start space-x-3">
                            <span class="text-xl mt-0.5">
                                @if($notif->type == 'success') ✅
                                @elseif($notif->type == 'warning') ⚠️
                                @elseif($notif->type == 'error') ❌
                                @else 📢
                                @endif
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $notif->titre }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $notif->message }}</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="text-xs text-blue-600 font-medium">
                                        → {{ $notif->user->nom_complet }}
                                    </span>
                                    <span class="text-xs text-gray-300">
                                        {{ $notif->created_at->diffForHumans() }}
                                    </span>
                                    @if(!$notif->lu)
                                        <span class="text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full">
                                            Non lue
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.notifications.destroy', $notif) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-300 hover:text-red-500 transition text-sm ml-2">
                                ✕
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <p class="text-4xl mb-3">🔔</p>
                        <p class="text-sm">Aucune notification envoyée.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $historique->links() }}</div>
        </div>
    </div>
@endsection
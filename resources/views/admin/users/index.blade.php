@extends('layouts.admin')
@section('title', 'Clients')
@section('page_title', '👥 Gestion des Clients')
@section('page_subtitle', 'Liste et gestion des clients inscrits')

@section('content')

{{-- FILTRES --}}
<div class="edc-card p-5 mt-6">
    <form method="GET" action="{{ route('admin.users.index') }}"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="🔍 Nom, prénom, email..."
            class="edc-input lg:col-span-1">

        <select name="statut" class="edc-select">
            <option value="">Tous les statuts</option>
            <option value="actif"    {{ request('statut') == 'actif' ? 'selected' : '' }}>✅ Actif</option>
            <option value="inactif"  {{ request('statut') == 'inactif' ? 'selected' : '' }}>⏸️ Inactif</option>
            <option value="suspendu" {{ request('statut') == 'suspendu' ? 'selected' : '' }}>⛔ Suspendu</option>
        </select>

        <select name="formation_id" class="edc-select">
            <option value="">Toutes les formations</option>
            @foreach($formations as $formation)
            <option value="{{ $formation->id }}" {{ request('formation_id') == $formation->id ? 'selected' : '' }}>
                {{ $formation->titre }}
            </option>
            @endforeach
        </select>

        <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="edc-input">

        <div class="flex space-x-2">
            <button type="submit" class="btn-primary btn-sm flex-1">Filtrer</button>
            <a href="{{ route('admin.users.index') }}" class="btn-tertiary btn-sm flex-1 text-center">Reset</a>
        </div>
    </form>
</div>

{{-- TABLEAU --}}
<div class="edc-card mt-5 overflow-hidden">
    <div class="px-6 py-4 flex justify-between items-center" style="border-bottom: 1px solid var(--edc-border);">
        <h3 class="font-bold" style="color: var(--edc-text-primary);">👥 {{ $clients->total() }} client(s) trouvé(s)</h3>
        <a href="{{ route('admin.enseignants.index') }}" class="btn-success btn-xs">👨‍🏫 Gérer les enseignants</a>
    </div>

    <div class="table-responsive">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Formations</th>
                    <th>Inscription</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td style="color: var(--edc-text-muted);">{{ $client->id }}</td>
                    <td>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                                {{ strtoupper(substr($client->prenom, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium" style="color: var(--edc-text-primary);">{{ $client->nom_complet }}</p>
                                @if($client->telephone)
                                <p class="text-xs" style="color: var(--edc-text-muted);">📲 {{ $client->telephone }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="color: var(--edc-text-secondary);">{{ $client->email }}</td>
                    <td>
                        @forelse($client->inscriptions->take(2) as $inscription)
                        @php
                            $bi = match($inscription->statut) {
                                'valide'     => 'background-color: rgba(16,185,129,0.12); color: #34D399;',
                                'en_attente' => 'background-color: rgba(245,158,11,0.12); color: #FBBF24;',
                                'refuse'     => 'background-color: rgba(239,68,68,0.12); color: #F87171;',
                                default      => 'background-color: rgba(148,163,184,0.10); color: #94A3B8;',
                            };
                        @endphp
                        <span class="badge text-xs mb-1" style="{{ $bi }}">{{ Str::limit($inscription->formation->titre, 20) }}</span>
                        @empty
                        <span style="color: var(--edc-text-muted);" class="text-xs">Aucune</span>
                        @endforelse
                    </td>
                    <td style="color: var(--edc-text-muted);" class="text-xs">{{ $client->created_at->format('d/m/Y') }}</td>
                    <td>
                        @php
                            $sb = match($client->statut) {
                                'actif'    => 'background-color: rgba(16,185,129,0.12); color: #34D399;',
                                'suspendu' => 'background-color: rgba(239,68,68,0.12); color: #F87171;',
                                default    => 'background-color: rgba(148,163,184,0.10); color: #94A3B8;',
                            };
                        @endphp
                        <span class="badge text-xs" style="{{ $sb }}">{{ ucfirst($client->statut) }}</span>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.users.show', $client) }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">👁️ Détails</a>
                            <form method="POST" action="{{ route('admin.users.toggle-statut', $client) }}">
                                @csrf
                                <button type="submit" class="text-xs font-medium hover:underline"
                                    style="color: {{ $client->statut === 'actif' ? 'var(--edc-danger)' : 'var(--edc-secondary)' }};">
                                    {{ $client->statut === 'actif' ? '⛔' : '✅' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $client) }}"
                                onsubmit="return confirm('Supprimer ce client ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium hover:underline" style="color: var(--edc-danger);">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center" style="color: var(--edc-text-muted);">
                        <p class="text-4xl mb-3">👥</p>
                        <p>Aucun client trouvé.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4" style="border-top: 1px solid var(--edc-border);">
        {{ $clients->links() }}
    </div>
</div>
@endsection
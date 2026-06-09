@extends('layouts.admin')
@section('title', 'Enseignants')
@section('page_title', '👨‍🏫 Gestion des Enseignants')
@section('page_subtitle', 'Ajout, modification et supervision des enseignants')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

    {{-- FORMULAIRE AJOUT --}}
    <div class="edc-card p-6">
        <h2 class="text-lg font-bold mb-5" style="color: var(--edc-text-primary);">➕ Ajouter un enseignant</h2>

        <form method="POST" action="{{ route('admin.enseignants.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="edc-label">Prénom *</label>
                <input type="text" name="prenom" value="{{ old('prenom') }}" required class="edc-input">
                @error('prenom') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="edc-label">Nom *</label>
                <input type="text" name="nom" value="{{ old('nom') }}" required class="edc-input">
                @error('nom') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="edc-label">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="edc-input">
                @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="edc-label">Téléphone</label>
                <input type="text" name="telephone" value="{{ old('telephone') }}" class="edc-input">
            </div>

            <div>
                <label class="edc-label">Mot de passe *</label>
                <input type="password" name="password" required class="edc-input">
                @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="edc-label">Confirmer le mot de passe *</label>
                <input type="password" name="password_confirmation" required class="edc-input">
            </div>

            <button type="submit" class="btn-primary w-full">➕ Créer l'enseignant</button>
        </form>
    </div>

    {{-- LISTE ENSEIGNANTS --}}
    <div class="lg:col-span-2">
        <div class="edc-card overflow-hidden">
            <div class="px-6 py-4" style="border-bottom: 1px solid var(--edc-border);">
                <h2 class="text-lg font-bold" style="color: var(--edc-text-primary);">👨‍🏫 {{ $enseignants->count() }} enseignant(s)</h2>
            </div>

            @forelse($enseignants as $enseignant)
            <div class="p-5 transition" style="border-bottom: 1px solid var(--edc-border);"
                onmouseover="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                onmouseout="this.style.backgroundColor='transparent'"
                x-data="{ editOpen: false }">

                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                            style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                            {{ strtoupper(substr($enseignant->prenom, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-sm" style="color: var(--edc-text-primary);">{{ $enseignant->nom_complet }}</p>
                            <p class="text-xs" style="color: var(--edc-text-muted);">{{ $enseignant->email }}</p>
                            @if($enseignant->telephone)
                            <p class="text-xs" style="color: var(--edc-text-muted);">📲 {{ $enseignant->telephone }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="badge badge-purple text-xs">📚 {{ $enseignant->ressources_count }} ressource(s)</span>
                        @php
                            $se = match($enseignant->statut) {
                                'actif'    => 'background-color: rgba(16,185,129,0.12); color: #34D399;',
                                'suspendu' => 'background-color: rgba(239,68,68,0.12); color: #F87171;',
                                default    => 'background-color: rgba(148,163,184,0.10); color: #94A3B8;',
                            };
                        @endphp
                        <span class="badge text-xs" style="{{ $se }}">{{ ucfirst($enseignant->statut) }}</span>
                        <button @click="editOpen = !editOpen" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">✏️ Modifier</button>
                        <form method="POST" action="{{ route('admin.users.toggle-statut', $enseignant) }}">
                            @csrf
                            <button type="submit" class="text-xs font-medium hover:underline"
                                style="color: {{ $enseignant->statut === 'actif' ? 'var(--edc-danger)' : 'var(--edc-secondary)' }};">
                                {{ $enseignant->statut === 'actif' ? '⛔' : '✅' }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Formulaire de modification inline --}}
                <div x-show="editOpen" x-cloak class="mt-4 rounded-xl p-4" style="background-color: var(--edc-bg-base);">
                    <form method="POST" action="{{ route('admin.enseignants.update', $enseignant) }}" class="space-y-3">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="edc-label">Prénom</label>
                                <input type="text" name="prenom" value="{{ $enseignant->prenom }}" class="edc-input">
                            </div>
                            <div>
                                <label class="edc-label">Nom</label>
                                <input type="text" name="nom" value="{{ $enseignant->nom }}" class="edc-input">
                            </div>
                        </div>
                        <div>
                            <label class="edc-label">Téléphone</label>
                            <input type="text" name="telephone" value="{{ $enseignant->telephone }}" class="edc-input">
                        </div>
                        <button type="submit" class="btn-primary btn-sm">💾 Enregistrer</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-12" style="color: var(--edc-text-muted);">
                <p class="text-4xl mb-3">👨‍🏫</p>
                <p>Aucun enseignant enregistré.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
@extends('layouts.enseignant')
@section('title', 'Envoyer une notification')

@section('content')
<div class="mb-6">
    <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">🔔 Envoyer une notification</h1>
    <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">Notifiez les apprenants de vos formations</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Formulaire notification --}}
    <div class="edc-card p-6">
        <h2 class="text-lg font-bold mb-5" style="color: var(--edc-text-primary);">📢 Notification interne</h2>
        <form method="POST" action="{{ route('enseignant.notifications.envoyer') }}" class="space-y-5">
            @csrf

            <div>
                <label class="edc-label">Formation ciblée *</label>
                <select name="formation_id" required class="edc-select">
                    <option value="">-- Choisir une formation --</option>
                    @foreach($formations as $formation)
                    <option value="{{ $formation->id }}" {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                        {{ $formation->titre }} ({{ $formation->inscriptions->count() }} apprenant(s))
                    </option>
                    @endforeach
                </select>
                @error('formation_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="edc-label">Type *</label>
                <select name="type" required class="edc-select">
                    <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>📢 Information</option>
                    <option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>✅ Bonne nouvelle</option>
                    <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>⚠️ Avertissement</option>
                </select>
            </div>

            <div>
                <label class="edc-label">Titre *</label>
                <input type="text" name="titre" value="{{ old('titre') }}" required
                    class="edc-input" placeholder="Ex : Nouveau cours disponible !">
                @error('titre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="edc-label">Message *</label>
                <textarea name="message" rows="4" required class="edc-input"
                    placeholder="Écrivez votre message ici...">{{ old('message') }}</textarea>
                @error('message') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary w-full">
                📤 Envoyer la notification
            </button>
        </form>
    </div>

    {{-- Apprenants par formation --}}
    <div class="edc-card p-6">
        <h2 class="text-lg font-bold mb-5" style="color: var(--edc-text-primary);">👥 Apprenants par formation</h2>
        @forelse($formations as $formation)
        <div class="mb-5 pb-4" style="border-bottom: 1px solid var(--edc-border);">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-sm" style="color: var(--edc-text-primary);">{{ $formation->titre }}</h3>
                <span class="badge badge-blue">
                    {{ $formation->inscriptions->count() }} inscrits
                </span>
            </div>
            @if($formation->inscriptions->count())
            <div class="flex flex-wrap gap-2">
                @foreach($formation->inscriptions->take(5) as $inscription)
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                    style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);"
                    title="{{ $inscription->user?->nom_complet ?? 'Utilisateur inconnu' }}">
                    {{ strtoupper(substr($inscription->user?->prenom ?? '?', 0, 1)) }}
                </div>
                @endforeach
                @if($formation->inscriptions->count() > 5)
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                    style="background-color: var(--edc-bg-elevated); color: var(--edc-text-muted);">
                    +{{ $formation->inscriptions->count() - 5 }}
                </div>
                @endif
            </div>
            @else
            <p class="text-sm" style="color: var(--edc-text-muted);">Aucun apprenant inscrit.</p>
            @endif
        </div>
        @empty
        <p class="text-sm text-center py-4" style="color: var(--edc-text-muted);">Aucune formation assignée.</p>
        @endforelse
    </div>

    {{-- SECTION EMAIL --}}
    <div class="edc-card p-6 lg:col-span-2">
        <h2 class="text-lg font-bold mb-5" style="color: var(--edc-text-primary);">✉️ Envoyer un email aux apprenants</h2>

        <form method="POST" action="{{ route('enseignant.emails.envoyer') }}" class="space-y-4">
            @csrf

            <div>
                <label class="edc-label">Formation *</label>
                <select name="formation_id" required class="edc-select">
                    <option value="">-- Choisir --</option>
                    @foreach($formations as $formation)
                    <option value="{{ $formation->id }}">{{ $formation->titre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="edc-label">Sujet *</label>
                <input type="text" name="sujet" required class="edc-input"
                    placeholder="Ex : Nouveau cours disponible">
            </div>

            <div>
                <label class="edc-label">Message *</label>
                <textarea name="message" rows="4" required class="edc-input"
                    placeholder="Votre message..."></textarea>
            </div>

            <button type="submit" class="btn-success w-full">
                📧 Envoyer l'email
            </button>
        </form>
    </div>
</div>
@endsection
@extends('layouts.admin')
@section('title', 'Emails')
@section('page_title', '✉️ Système d\'Emails')
@section('page_subtitle', 'Envoi d\'emails et journalisation')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-3 gap-4 mt-6">
    @foreach([
        ['total',    '✉️ Total envoyés', 'var(--edc-primary)'],
        ['envoyes',  '✅ Réussis',       'var(--edc-secondary)'],
        ['echoues',  '❌ Échoués',       'var(--edc-danger)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">{{ $stats[$stat[0]] }}</p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- FORMULAIRE --}}
    <div class="edc-card p-6">
        <h3 class="text-lg font-bold mb-1" style="color: var(--edc-text-primary);">✉️ Envoyer un email</h3>
        <p class="text-sm mb-5" style="color: var(--edc-text-muted);">Email personnalisé vers un client ou enseignant</p>

        <form method="POST" action="{{ route('admin.emails.envoyer') }}" class="space-y-4">
            @csrf

            <div>
                <label class="edc-label">Destinataire *</label>
                <select name="destinataire_id" required class="edc-select">
                    <option value="">-- Choisir --</option>
                    <optgroup label="👥 Clients">
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->nom_complet }} — {{ $client->email }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="👨‍🏫 Enseignants">
                        @foreach($enseignants as $enseignant)
                        <option value="{{ $enseignant->id }}">{{ $enseignant->nom_complet }} — {{ $enseignant->email }}</option>
                        @endforeach
                    </optgroup>
                </select>
                @error('destinataire_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="edc-label">Sujet *</label>
                <input type="text" name="sujet" value="{{ old('sujet') }}" required
                    class="edc-input" placeholder="Objet de votre email">
                @error('sujet') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="edc-label">Message *</label>
                <textarea name="message" rows="6" required class="edc-input"
                    placeholder="Écrivez votre message ici...">{{ old('message') }}</textarea>
                @error('message') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary w-full">📤 Envoyer l'email</button>
        </form>
    </div>

    {{-- JOURNAL DES EMAILS --}}
    <div class="edc-card p-6">
        <h3 class="text-lg font-bold mb-5" style="color: var(--edc-text-primary);">📋 Journal des emails</h3>

        <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
            @forelse($logs as $log)
            <div class="p-3 rounded-xl"
                style="{{ $log->statut == 'envoye'
                    ? 'background-color: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.20);'
                    : 'background-color: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.20);' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="text-sm">{{ $log->statut == 'envoye' ? '✅' : '❌' }}</span>
                            <p class="text-xs font-bold truncate" style="color: var(--edc-text-primary);">{{ $log->sujet }}</p>
                        </div>
                        <p class="text-xs" style="color: var(--edc-text-secondary);">
                            → {{ $log->destinataire?->nom_complet ?? $log->email_destinataire }}
                        </p>
                        @if($log->expediteur)
                        <p class="text-xs" style="color: var(--edc-text-muted);">De : {{ $log->expediteur->nom_complet }}</p>
                        @endif
                        <p class="text-xs mt-1" style="color: var(--edc-text-muted);">{{ $log->date_envoi?->diffForHumans() }}</p>
                    </div>
                    <span class="badge text-xs flex-shrink-0 ml-2"
                        style="{{ $log->statut == 'envoye'
                            ? 'background-color: rgba(16,185,129,0.12); color: #34D399;'
                            : 'background-color: rgba(239,68,68,0.12); color: #F87171;' }}">
                        {{ ucfirst($log->statut) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="text-center py-8" style="color: var(--edc-text-muted);">
                <p class="text-4xl mb-3">✉️</p>
                <p class="text-sm">Aucun email journalisé.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
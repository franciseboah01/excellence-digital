@extends('layouts.admin')
@section('title', 'Emails')
@section('page_title', 'Système d\'Emails')
@section('page_subtitle', 'Envoi d\'emails et journalisation')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-3 gap-4 mt-6">
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-blue-600">
        <p class="text-2xl font-bold text-blue-700">{{ $stats['total'] }}</p>
        <p class="text-gray-500 text-xs mt-1">✉️ Total envoyés</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-green-500">
        <p class="text-2xl font-bold text-green-600">{{ $stats['envoyes'] }}</p>
        <p class="text-gray-500 text-xs mt-1">✅ Réussis</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-red-500">
        <p class="text-2xl font-bold text-red-600">{{ $stats['echoues'] }}</p>
        <p class="text-gray-500 text-xs mt-1">❌ Échoués</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- FORMULAIRE --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-bold text-blue-900 mb-1">✉️ Envoyer un email</h3>
        <p class="text-gray-400 text-sm mb-5">Email personnalisé vers un client ou enseignant</p>

        <form method="POST" action="{{ route('admin.emails.envoyer') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Destinataire *
                </label>
                <select name="destinataire_id" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Choisir --</option>
                    <optgroup label="👥 Clients">
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}">
                            {{ $client->nom_complet }} — {{ $client->email }}
                        </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="👨‍🏫 Enseignants">
                        @foreach($enseignants as $enseignant)
                        <option value="{{ $enseignant->id }}">
                            {{ $enseignant->nom_complet }} — {{ $enseignant->email }}
                        </option>
                        @endforeach
                    </optgroup>
                </select>
                @error('destinataire_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Sujet *</label>
                <input type="text" name="sujet" value="{{ old('sujet') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Objet de votre email">
                @error('sujet')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Message *</label>
                <textarea name="message" rows="6" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Écrivez votre message ici...">{{ old('message') }}</textarea>
                @error('message')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition">
                📤 Envoyer l'email
            </button>
        </form>
    </div>

    {{-- JOURNAL DES EMAILS --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-bold text-blue-900 mb-5">📋 Journal des emails</h3>

        <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
            @forelse($logs as $log)
            <div class="p-3 rounded-xl border
                {{ $log->statut == 'envoye'
                    ? 'bg-green-50 border-green-200'
                    : 'bg-red-50 border-red-200' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="text-sm">
                                {{ $log->statut == 'envoye' ? '✅' : '❌' }}
                            </span>
                            <p class="text-xs font-bold text-gray-800 truncate">
                                {{ $log->sujet }}
                            </p>
                        </div>
                        <p class="text-xs text-gray-500">
                            → {{ $log->destinataire?->nom_complet ?? $log->email_destinataire }}
                        </p>
                        @if($log->expediteur)
                        <p class="text-xs text-gray-400">
                            De : {{ $log->expediteur->nom_complet }}
                        </p>
                        @endif
                        <p class="text-xs text-gray-300 mt-1">
                            {{ $log->date_envoi?->diffForHumans() }}
                        </p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium ml-2 flex-shrink-0
                        {{ $log->statut == 'envoye'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($log->statut) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">
                <p class="text-4xl mb-3">✉️</p>
                <p class="text-sm">Aucun email journalisé.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
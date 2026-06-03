@extends('layouts.admin')
@section('title', 'Demande #' . $demande->id)
@section('page_title', 'Détail de la Demande')
@section('page_subtitle', 'Demande #' . $demande->id)

@section('content')
<div class="mt-4">
    <a href="{{ route('admin.demandes.index') }}"
        class="text-blue-600 hover:underline text-sm">← Retour aux demandes</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">

    {{-- INFOS DEMANDE --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Client --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">👤 Informations du demandeur</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Nom</p>
                    <p class="font-semibold">
                        {{ $demande->user?->nom_complet ?? $demande->nom_visiteur }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Email</p>
                    <p class="font-semibold">
                        {{ $demande->user?->email ?? $demande->email_visiteur }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Téléphone</p>
                    <p class="font-semibold">
                        {{ $demande->user?->telephone ?? $demande->telephone_visiteur ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Type</p>
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                        {{ $demande->user_id ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $demande->user_id ? '👤 Client inscrit' : '🌐 Visiteur' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Service --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">💼 Service demandé</h3>
            <div class="flex items-center space-x-4">
                <span class="text-4xl">{{ $demande->service->icone }}</span>
                <div>
                    <p class="font-bold text-gray-800 text-lg">{{ $demande->service->titre }}</p>
                    <p class="text-gray-500 text-sm">{{ $demande->service->description }}</p>
                    @if($demande->service->prix)
                    <p class="text-blue-700 font-semibold mt-1">
                        {{ number_format($demande->service->prix, 0, ',', ' ') }} FCFA
                    </p>
                    @endif
                </div>
            </div>
            @if($demande->message)
            <div class="mt-4 bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
                <p class="font-semibold text-gray-700 mb-1">💬 Message du client :</p>
                {{ $demande->message }}
            </div>
            @endif
        </div>
    </div>

    {{-- PANEL STATUT --}}
    <div class="space-y-5">

        {{-- Statut actuel --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">📊 Statut actuel</h3>
            @php
                $badge = match($demande->statut) {
                    'en_attente' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                    'en_cours'   => 'bg-blue-100 text-blue-700 border-blue-300',
                    'termine'    => 'bg-green-100 text-green-700 border-green-300',
                    'annule'     => 'bg-red-100 text-red-700 border-red-300',
                    default      => 'bg-gray-100 text-gray-600 border-gray-300',
                };
                $label = match($demande->statut) {
                    'en_attente' => '⏳ En attente',
                    'en_cours'   => '🔄 En cours de traitement',
                    'termine'    => '✅ Terminé',
                    'annule'     => '❌ Annulé',
                    default      => $demande->statut,
                };
            @endphp
            <div class="border-2 rounded-xl p-4 text-center text-lg font-bold {{ $badge }}">
                {{ $label }}
            </div>
            <p class="text-xs text-gray-400 text-center mt-2">
                Créée le {{ $demande->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>

        {{-- Changer le statut --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-4">🔄 Changer le statut</h3>

            <form method="POST"
                action="{{ route('admin.demandes.statut', $demande) }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Nouveau statut *
                    </label>
                    <select name="statut" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="en_attente"
                            {{ $demande->statut == 'en_attente' ? 'selected' : '' }}>
                            ⏳ En attente
                        </option>
                        <option value="en_cours"
                            {{ $demande->statut == 'en_cours' ? 'selected' : '' }}>
                            🔄 En cours
                        </option>
                        <option value="termine"
                            {{ $demande->statut == 'termine' ? 'selected' : '' }}>
                            ✅ Terminé
                        </option>
                        <option value="annule"
                            {{ $demande->statut == 'annule' ? 'selected' : '' }}>
                            ❌ Annulé
                        </option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Message personnalisé (optionnel)
                    </label>
                    <textarea name="message" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Message envoyé au client..."></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition text-sm">
                    📤 Mettre à jour & Notifier
                </button>

                <p class="text-xs text-gray-400 text-center mt-2">
                    📧 Un email sera automatiquement envoyé
                    @if($demande->user_id) + 🔔 notification interne @endif
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
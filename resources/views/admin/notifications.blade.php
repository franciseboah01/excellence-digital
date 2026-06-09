@extends('layouts.admin')
@section('title', 'Notifications')
@section('page_title', '🔔 Système de Notifications')
@section('page_subtitle', 'Envoyez des notifications aux utilisateurs')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-3 gap-4 mt-6">
    @foreach([
        ['total',       '📢 Total envoyées', 'var(--edc-primary)'],
        ['non_lues',    '🔴 Non lues',       'var(--edc-danger)'],
        ['aujourdhui',  '📅 Aujourd\'hui',   'var(--edc-secondary)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">{{ $stats[$stat[0]] }}</p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- NOTIFICATION CIBLÉE --}}
    <div class="edc-card p-6" x-data="{ onglet: 'cible' }">
        <h3 class="text-lg font-bold mb-1" style="color: var(--edc-text-primary);">📤 Envoyer une notification</h3>
        <p class="text-sm mb-5" style="color: var(--edc-text-muted);">Ciblée, groupée ou diffusion générale</p>

        {{-- ONGLETS --}}
        <div class="flex space-x-1 rounded-xl p-1 mb-5" style="background-color: var(--edc-bg-base);">
            @foreach([
                ['cible',  '👤 Ciblée'],
                ['groupe', '🎓 Par formation'],
                ['tous',   '📢 Diffusion'],
            ] as $ong)
            <button @click="onglet = '{{ $ong[0] }}'"
                class="flex-1 py-2 rounded-lg text-xs transition"
                :style="onglet === '{{ $ong[0] }}'
                    ? 'background-color: var(--edc-primary); color: #fff; font-weight: 600;'
                    : 'color: var(--edc-text-muted);'"
                onmouseover="if(onglet!=='{{ $ong[0] }}'){this.style.color='var(--edc-text-secondary)'}"
                onmouseout="if(onglet!=='{{ $ong[0] }}'){this.style.color='var(--edc-text-muted)'}">
                {{ $ong[1] }}
            </button>
            @endforeach
        </div>

        {{-- ONGLET 1 : CIBLÉE --}}
        <div x-show="onglet === 'cible'">
            <form method="POST" action="{{ route('admin.notifications.cible') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="edc-label">Destinataire *</label>
                    <select name="user_id" required class="edc-select">
                        <option value="">-- Choisir un utilisateur --</option>
                        <optgroup label="👥 Clients">
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->nom_complet }} ({{ $client->email }})</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="👨‍🏫 Enseignants">
                            @foreach($enseignants as $enseignant)
                                <option value="{{ $enseignant->id }}">{{ $enseignant->nom_complet }} ({{ $enseignant->email }})</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                @include('admin.partials.notif-form-fields')
                <button type="submit" class="btn-primary w-full">📤 Envoyer</button>
            </form>
        </div>

        {{-- ONGLET 2 : PAR FORMATION --}}
        <div x-show="onglet === 'groupe'">
            <form method="POST" action="{{ route('admin.notifications.groupe') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="edc-label">Formation *</label>
                    <select name="formation_id" required class="edc-select">
                        <option value="">-- Choisir une formation --</option>
                        @foreach($formations as $formation)
                            <option value="{{ $formation->id }}">{{ $formation->titre }} ({{ $formation->inscrits_valides }} apprenant(s))</option>
                        @endforeach
                    </select>
                </div>
                @include('admin.partials.notif-form-fields')
                <button type="submit" class="btn-primary w-full">📤 Envoyer au groupe</button>
            </form>
        </div>

        {{-- ONGLET 3 : DIFFUSION --}}
        <div x-show="onglet === 'tous'">
            <form method="POST" action="{{ route('admin.notifications.tous') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="edc-label">Cible *</label>
                    <select name="cible" required class="edc-select">
                        <option value="clients">👥 Tous les clients</option>
                        <option value="enseignants">👨‍🏫 Tous les enseignants</option>
                        <option value="tous">🌐 Tout le monde</option>
                    </select>
                </div>
                @include('admin.partials.notif-form-fields')
                <button type="submit" class="btn-danger w-full">📢 Diffuser à tous</button>
            </form>
        </div>
    </div>

    {{-- HISTORIQUE --}}
    <div class="edc-card p-6">
        <h3 class="text-lg font-bold mb-5" style="color: var(--edc-text-primary);">📋 Historique récent</h3>

        <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
            @forelse($historique as $notif)
                <div class="flex items-start justify-between p-3 rounded-xl"
                    style="{{ !$notif->lu
                        ? 'background-color: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.20);'
                        : 'background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);' }}">
                    <div class="flex items-start space-x-3">
                        <span class="text-xl mt-0.5">
                            @if($notif->type == 'success') ✅
                            @elseif($notif->type == 'warning') ⚠️
                            @elseif($notif->type == 'error') ❌
                            @else 📢
                            @endif
                        </span>
                        <div>
                            <p class="text-sm font-semibold" style="color: var(--edc-text-primary);">{{ $notif->titre }}</p>
                            <p class="text-xs mt-0.5" style="color: var(--edc-text-secondary);">{{ $notif->message }}</p>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="text-xs font-medium" style="color: var(--edc-primary-light);">
                                    → {{ $notif->user->nom_complet }}
                                </span>
                                <span class="text-xs" style="color: var(--edc-text-muted);">{{ $notif->created_at->diffForHumans() }}</span>
                                @if(!$notif->lu)
                                    <span class="badge badge-red text-xs">Non lue</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.notifications.destroy', $notif) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm ml-2 transition" style="color: var(--edc-text-muted);"
                            onmouseover="this.style.color='#EF4444'"
                            onmouseout="this.style.color='#64748B'">✕</button>
                    </form>
                </div>
            @empty
                <div class="text-center py-8" style="color: var(--edc-text-muted);">
                    <p class="text-4xl mb-3">🔔</p>
                    <p class="text-sm">Aucune notification envoyée.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $historique->links() }}</div>
    </div>
</div>
@endsection
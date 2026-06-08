@extends('layouts.client')
@section('title', 'Mon Espace')

@section('content')

{{-- HEADER --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">
            Bonjour {{ auth()->user()->prenom }} 👋
        </h1>
        <p class="text-xs sm:text-sm mt-0.5" style="color: var(--edc-text-secondary);">
            Bienvenue dans votre espace personnel
        </p>
    </div>
    <a href="{{ route('demande.form') }}" class="btn-primary btn-touch">
        <span>➕</span><span>Nouvelle demande</span>
    </a>
</div>

{{-- STATS --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    @foreach([
        ['demandes',       '📋 Demandes',     'var(--edc-primary)'],
        ['demandes_cours', '🔄 En cours',     'var(--edc-secondary)'],
        ['formations',     '🎓 Formations',   '#A78BFA'],
        ['notifications',  '🔔 Non lues',     'var(--edc-danger)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">{{ $stats[$stat[0]] }}</p>
        <p class="stat-label">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

{{-- CONTENU --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- PRINCIPALE --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Demandes --}}
        <div class="edc-card p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold" style="color: var(--edc-text-primary);">📋 Dernières demandes</h2>
                <a href="{{ route('client.demandes') }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">Voir tout</a>
            </div>
            @forelse($dernieres_demandes as $d)
            <div class="flex items-center justify-between py-3" style="border-bottom: 1px solid var(--edc-border);">
                <div class="flex items-center space-x-3">
                    <span class="text-xl">{{ $d->service->icone ?? '💼' }}</span>
                    <div>
                        <p class="font-medium text-sm" style="color: var(--edc-text-primary);">
                            {{ Str::limit($d->service->titre, 28) }}
                        </p>
                        <p class="text-xs" style="color: var(--edc-text-muted);">
                            {{ $d->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                @include('client.partials.statut-badge', ['statut' => $d->statut])
            </div>
            @empty
            <div class="text-center py-6" style="color: var(--edc-text-muted);">
                <p class="text-3xl mb-2">📋</p>
                <p class="text-sm">Aucune demande.</p>
            </div>
            @endforelse
            <div class="mt-4">
                <a href="{{ route('demande.form') }}" class="btn-primary btn-sm w-full text-center">
                    + Nouvelle demande
                </a>
            </div>
        </div>

        {{-- Formations --}}
        <div class="edc-card p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold" style="color: var(--edc-text-primary);">🎓 Mes Formations</h2>
                <a href="{{ route('client.formations') }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">Voir tout</a>
            </div>
            @forelse($mes_formations as $i)
            <div class="flex items-center justify-between py-3" style="border-bottom: 1px solid var(--edc-border);">
                <div>
                    <p class="font-medium text-sm" style="color: var(--edc-text-primary);">
                        {{ $i->formation->titre }}
                    </p>
                    <span class="badge badge-green mt-0.5">
                        {{ ucfirst($i->formation->niveau) }}
                    </span>
                </div>
                <a href="{{ route('client.ressources', $i->formation) }}"
                    class="btn-success btn-xs flex-shrink-0 ml-2 btn-touch">
                    Accéder →
                </a>
            </div>
            @empty
            <div class="text-center py-6" style="color: var(--edc-text-muted);">
                <p class="text-3xl mb-2">🎓</p>
                <p class="text-sm">Aucune formation.</p>
                <a href="{{ route('formations.index') }}" class="btn-primary btn-xs mt-2 inline-block">
                    Voir les formations
                </a>
            </div>
            @endforelse
        </div>

        {{-- Notifications --}}
        <div class="edc-card p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold" style="color: var(--edc-text-primary);">🔔 Notifications récentes</h2>
                <a href="{{ route('client.notifications') }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">Tout voir</a>
            </div>
            @forelse($notifications->take(4) as $n)
            <div class="flex items-start space-x-2 py-2.5 transition" style="border-bottom: 1px solid var(--edc-border); {{ !$n->lu ? 'background-color: rgba(59,130,246,0.06); border-radius: 8px; padding-left: 8px; padding-right: 8px;' : '' }}">
                <span class="text-base mt-0.5">
                    @if($n->type=='success')✅@elseif($n->type=='warning')⚠️@elseif($n->type=='error')❌@else📢@endif
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold truncate" style="color: var(--edc-text-primary);">{{ $n->titre }}</p>
                    <p class="text-xs truncate" style="color: var(--edc-text-secondary);">{{ $n->message }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--edc-text-muted);">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                @if(!$n->lu)
                <div class="w-1.5 h-1.5 rounded-full flex-shrink-0 mt-1.5" style="background-color: var(--edc-primary);"></div>
                @endif
            </div>
            @empty
            <p class="text-xs text-center py-4" style="color: var(--edc-text-muted);">Aucune notification.</p>
            @endforelse
        </div>
    </div>

    {{-- COLONNE DROITE --}}
    <div class="space-y-4">

        {{-- Profil --}}
        <div class="edc-card p-5 text-center">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl font-bold mx-auto mb-3 overflow-hidden"
                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/'.auth()->user()->avatar) }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                @endif
            </div>
            <p class="font-bold text-sm" style="color: var(--edc-text-primary);">{{ auth()->user()->nom_complet }}</p>
            <p class="text-xs mt-0.5 truncate px-2" style="color: var(--edc-text-muted);">{{ auth()->user()->email }}</p>
            <span class="badge badge-blue mt-2">👤 Client</span>
            <a href="{{ route('client.profil') }}" class="btn-tertiary btn-sm w-full mt-3">
                ✏️ Modifier le profil
            </a>
        </div>

        {{-- Actions rapides --}}
        <div class="edc-card p-5">
            <h3 class="font-bold text-sm mb-3" style="color: var(--edc-text-primary);">⚡ Actions rapides</h3>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-1">
                @foreach([
                    ['demande.form',              '💼', 'Service'],
                    ['client.formations',         '🎓', 'Formations'],
                    ['client.qcms.index',         '📝', 'QCMs'],
                    ['messages.index',            '💬', 'Messages'],
                    ['client.temoignages.index',  '⭐', 'Avis'],
                ] as $a)
                <a href="{{ route($a[0]) }}"
                    class="flex items-center space-x-2 p-3 rounded-xl transition text-xs font-semibold btn-touch"
                    style="background-color: var(--edc-bg-base); color: var(--edc-text-secondary); border: 1px solid var(--edc-border);"
                    onmouseover="this.style.backgroundColor='var(--edc-bg-elevated)'; this.style.color='var(--edc-primary-light)'; this.style.borderColor='var(--edc-primary)';"
                    onmouseout="this.style.backgroundColor='var(--edc-bg-base)'; this.style.color='var(--edc-text-secondary)'; this.style.borderColor='var(--edc-border)';">
                    <span>{{ $a[1] }}</span><span>{{ $a[2] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
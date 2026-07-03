@extends('layouts.client')
@section('title', 'Mon Espace — ' . \App\Models\Configuration::get('site_nom', 'EDC'))

@section('content')

{{-- HEADER - Optimisé mobile --}}
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
    
    {{-- Boutons d'action --}}
    <div class="flex flex-col sm:flex-row gap-2">
        <a href="{{ route('client.demande.form') }}" class="btn-primary btn-touch justify-center">
            <span>➕</span><span>Nouvelle demande</span>
        </a>
        <a href="{{ route('client.formations.disponibles') }}" class="btn-secondary btn-touch justify-center">
            <span>🎓</span><span>Voir les formations</span>
        </a>
    </div>
</div>

{{-- STATS - 6 cases avec explications --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 sm:gap-3 mb-6">
    
    {{-- 1. Total demandes --}}
    <div class="stat-card group cursor-help" 
         style="border-left-color: var(--edc-primary);"
         title="Nombre total de toutes vos demandes de service">
        <div class="flex items-start justify-between mb-1">
            <p class="stat-value text-lg sm:text-2xl">{{ $stats['demandes_total'] }}</p>
            <span class="text-lg sm:text-xl opacity-50 group-hover:opacity-100 transition-opacity">📋</span>
        </div>
        <p class="stat-label text-[10px] sm:text-xs font-medium">Total demandes</p>
        <p class="text-[9px] sm:text-[10px] mt-1 opacity-0 group-hover:opacity-100 transition-opacity" 
           style="color: var(--edc-text-muted);">
            Toutes vos demandes confondues
        </p>
    </div>

    {{-- 2. En attente --}}
    <div class="stat-card group cursor-help" 
         style="border-left-color: var(--edc-accent-gold);"
         title="Demandes en attente de prise en charge">
        <div class="flex items-start justify-between mb-1">
            <p class="stat-value text-lg sm:text-2xl">{{ $stats['demandes_attente'] }}</p>
            <span class="text-lg sm:text-xl opacity-50 group-hover:opacity-100 transition-opacity">⏳</span>
        </div>
        <p class="stat-label text-[10px] sm:text-xs font-medium">En attente</p>
        <p class="text-[9px] sm:text-[10px] mt-1 opacity-0 group-hover:opacity-100 transition-opacity" 
           style="color: var(--edc-text-muted);">
            En attente de validation
        </p>
    </div>

    {{-- 3. En cours --}}
    <div class="stat-card group cursor-help" 
         style="border-left-color: var(--edc-secondary);"
         title="Demandes en cours de traitement">
        <div class="flex items-start justify-between mb-1">
            <p class="stat-value text-lg sm:text-2xl">{{ $stats['demandes_cours'] }}</p>
            <span class="text-lg sm:text-xl opacity-50 group-hover:opacity-100 transition-opacity">🔄</span>
        </div>
        <p class="stat-label text-[10px] sm:text-xs font-medium">En cours</p>
        <p class="text-[9px] sm:text-[10px] mt-1 opacity-0 group-hover:opacity-100 transition-opacity" 
           style="color: var(--edc-text-muted);">
            Actuellement traitées
        </p>
    </div>

    {{-- 4. Terminées --}}
    <div class="stat-card group cursor-help" 
         style="border-left-color: #10B981;"
         title="Demandes terminées avec succès">
        <div class="flex items-start justify-between mb-1">
            <p class="stat-value text-lg sm:text-2xl">{{ $stats['demandes_termine'] }}</p>
            <span class="text-lg sm:text-xl opacity-50 group-hover:opacity-100 transition-opacity">✅</span>
        </div>
        <p class="stat-label text-[10px] sm:text-xs font-medium">Terminées</p>
        <p class="text-[9px] sm:text-[10px] mt-1 opacity-0 group-hover:opacity-100 transition-opacity" 
           style="color: var(--edc-text-muted);">
            Services livrés
        </p>
    </div>

    {{-- 5. Annulées --}}
    <div class="stat-card group cursor-help" 
         style="border-left-color: var(--edc-danger);"
         title="Demandes annulées ou refusées">
        <div class="flex items-start justify-between mb-1">
            <p class="stat-value text-lg sm:text-2xl">{{ $stats['demandes_annule'] }}</p>
            <span class="text-lg sm:text-xl opacity-50 group-hover:opacity-100 transition-opacity">❌</span>
        </div>
        <p class="stat-label text-[10px] sm:text-xs font-medium">Annulées</p>
        <p class="text-[9px] sm:text-[10px] mt-1 opacity-0 group-hover:opacity-100 transition-opacity" 
           style="color: var(--edc-text-muted);">
            Demandes annulées
        </p>
    </div>

    {{-- 6. Formations (NOUVEAU) --}}
    <div class="stat-card group cursor-help" 
         style="border-left-color: #8B5CF6;"
         title="Nombre de formations auxquelles vous êtes inscrit">
        <div class="flex items-start justify-between mb-1">
            <p class="stat-value text-lg sm:text-2xl">{{ $stats['formations_total'] ?? 0 }}</p>
            <span class="text-lg sm:text-xl opacity-50 group-hover:opacity-100 transition-opacity">🎓</span>
        </div>
        <p class="stat-label text-[10px] sm:text-xs font-medium">Formations</p>
        <p class="text-[9px] sm:text-[10px] mt-1 opacity-0 group-hover:opacity-100 transition-opacity" 
           style="color: var(--edc-text-muted);">
            Formations suivies
        </p>
    </div>
</div>

{{-- Message d'aide pour les nouveaux utilisateurs --}}
@if($stats['demandes_total'] == 0 && ($stats['formations_total'] ?? 0) == 0)
<div class="alert alert-info mb-6 animate-fade-in-up">
    <span class="text-lg">💡</span>
    <div>
        <p class="font-semibold text-sm">Bienvenue ! Commencez votre parcours :</p>
        <p class="text-xs mt-1 opacity-80">
            Faites une <a href="{{ route('client.demande.form') }}" class="underline font-medium">demande de service</a> 
            ou explorez nos <a href="{{ route('client.formations.disponibles') }}" class="underline font-medium">formations disponibles</a>.
        </p>
    </div>
</div>
@endif

{{-- CONTENU --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- PRINCIPALE --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Demandes récentes --}}
        <div class="edc-card p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-4">
                <h2 class="font-bold text-sm sm:text-base" style="color: var(--edc-text-primary);">
                    📋 Dernières demandes
                </h2>
                <div class="flex items-center gap-3">
                    @if(count($dernieres_demandes) > 0)
                    <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full" 
                          style="background-color: rgba(59,130,246,0.1); color: var(--edc-primary-light);">
                        {{ count($dernieres_demandes) }} récente(s)
                    </span>
                    @endif
                    <a href="{{ route('client.demandes') }}" 
                       class="text-xs font-medium hover:underline flex-shrink-0" 
                       style="color: var(--edc-primary-light);">
                        Voir tout →
                    </a>
                </div>
            </div>
            
            @forelse($dernieres_demandes as $d)
            <div class="flex items-center justify-between py-3 gap-2 transition-colors hover:bg-white/[0.02] px-2 -mx-2 rounded-lg" 
                 style="border-bottom: 1px solid var(--edc-border);">
                <div class="flex items-center space-x-3 min-w-0 flex-1">
                    <span class="text-xl flex-shrink-0">{{ $d->service->icone ?? '💼' }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-sm truncate" style="color: var(--edc-text-primary);">
                            {{ $d->service->titre ?? 'Service sans titre' }}
                        </p>
                        <p class="text-xs" style="color: var(--edc-text-muted);">
                            📅 {{ $d->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @include('client.partials.statut-badge', ['statut' => $d->statut])
                    <a href="{{ route('client.demandes') }}" 
                       class="text-xs opacity-0 group-hover:opacity-100 transition-opacity hidden sm:inline"
                       style="color: var(--edc-text-muted);">
                        →
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-8" style="color: var(--edc-text-muted);">
                <p class="text-4xl mb-3">📋</p>
                <p class="text-sm font-medium mb-1">Aucune demande pour le moment</p>
                <p class="text-xs">Créez votre première demande de service</p>
                <a href="{{ route('client.demande.form') }}" 
                   class="btn-primary btn-sm mt-4 inline-block btn-touch">
                    + Créer une demande
                </a>
            </div>
            @endforelse
            
            @if(count($dernieres_demandes) > 0)
            <div class="mt-4">
                <a href="{{ route('client.demande.form') }}" 
                   class="btn-primary btn-touch w-full sm:w-auto text-center text-sm justify-center">
                    + Nouvelle demande
                </a>
            </div>
            @endif
        </div>

        {{-- Formations --}}
        <div class="edc-card p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-4">
                <h2 class="font-bold text-sm sm:text-base" style="color: var(--edc-text-primary);">
                    🎓 Mes Formations
                </h2>
                <div class="flex items-center gap-3">
                    @if(count($mes_formations) > 0)
                    <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full" 
                          style="background-color: rgba(16,185,129,0.1); color: var(--edc-secondary);">
                        {{ count($mes_formations) }} inscription(s)
                    </span>
                    @endif
                    <a href="{{ route('client.formations') }}" 
                       class="text-xs font-medium hover:underline flex-shrink-0" 
                       style="color: var(--edc-primary-light);">
                        Voir tout →
                    </a>
                </div>
            </div>
            
            @forelse($mes_formations as $i)
            <div class="flex items-center justify-between py-3 gap-2 transition-colors hover:bg-white/[0.02] px-2 -mx-2 rounded-lg" 
                 style="border-bottom: 1px solid var(--edc-border);">
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-sm truncate" style="color: var(--edc-text-primary);">
                        {{ $i->formation->titre }}
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="badge badge-green text-[10px]">
                            📚 {{ ucfirst($i->formation->module->nom ?? 'Module') }}
                        </span>
                        @if($i->statut == 'valide')
                        <span class="text-[10px]" style="color: var(--edc-success);">✅ Validé</span>
                        @else
                        <span class="text-[10px]" style="color: var(--edc-warning);">⏳ En attente</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('client.ressources', $i->formation) }}"
                   class="btn-success btn-xs flex-shrink-0 btn-touch">
                    Accéder →
                </a>
            </div>
            @empty
            <div class="text-center py-8" style="color: var(--edc-text-muted);">
                <p class="text-4xl mb-3">🎓</p>
                <p class="text-sm font-medium mb-1">Aucune formation en cours</p>
                <p class="text-xs">Découvrez notre catalogue de formations</p>
                <a href="{{ route('client.formations.disponibles') }}" 
                   class="btn-primary btn-xs mt-4 inline-block btn-touch">
                    Voir les formations
                </a>
            </div>
            @endforelse
        </div>

        {{-- Notifications récentes --}}
        <div class="edc-card p-4 sm:p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold text-sm sm:text-base" style="color: var(--edc-text-primary);">
                    🔔 Notifications récentes
                </h2>
                <a href="{{ route('client.notifications') }}" 
                   class="text-xs font-medium hover:underline" 
                   style="color: var(--edc-primary-light);">
                    Tout voir →
                </a>
            </div>
            
            @forelse($notifications->take(4) as $n)
            <div class="flex items-start space-x-2 py-2.5 px-2 -mx-2 rounded-lg transition-colors hover:bg-white/[0.02]" 
                 style="border-bottom: 1px solid var(--edc-border); {{ !$n->lu ? 'background-color: rgba(59,130,246,0.06);' : '' }}">
                <span class="text-base mt-0.5 flex-shrink-0">
                    @if($n->type=='success')✅
                    @elseif($n->type=='warning')⚠️
                    @elseif($n->type=='error')❌
                    @else📢
                    @endif
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold truncate" style="color: var(--edc-text-primary);">
                        {{ $n->titre }}
                    </p>
                    <p class="text-xs truncate mt-0.5" style="color: var(--edc-text-secondary);">
                        {{ $n->message }}
                    </p>
                    <p class="text-[10px] mt-1" style="color: var(--edc-text-muted);">
                        {{ $n->created_at->diffForHumans() }}
                    </p>
                </div>
                @if(!$n->lu)
                <div class="w-2 h-2 rounded-full flex-shrink-0 mt-1.5 animate-pulse" 
                     style="background-color: var(--edc-primary);"></div>
                @endif
            </div>
            @empty
            <div class="text-center py-6" style="color: var(--edc-text-muted);">
                <p class="text-3xl mb-2">🔔</p>
                <p class="text-xs">Aucune notification</p>
                <p class="text-[10px] mt-1">Vous serez notifié ici des mises à jour</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- COLONNE DROITE - Optimisée mobile --}}
    <div class="space-y-4">

        {{-- Profil --}}
        <div class="edc-card p-4 sm:p-5 text-center">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full flex items-center justify-center text-white text-xl sm:text-2xl font-bold mx-auto mb-3 overflow-hidden ring-2 ring-offset-2 ring-offset-[#1A2235]"
                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8); ring-color: var(--edc-primary);">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/'.auth()->user()->avatar) }}" 
                         class="w-full h-full object-cover" 
                         alt="Avatar de {{ auth()->user()->prenom }}">
                @else
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                @endif
            </div>
            <p class="font-bold text-sm" style="color: var(--edc-text-primary);">
                {{ auth()->user()->nom_complet }}
            </p>
            <p class="text-xs mt-0.5 truncate px-2" style="color: var(--edc-text-muted);">
                {{ auth()->user()->email }}
            </p>
            <span class="badge badge-blue mt-2 text-[10px]">👤 Client</span>
            
            {{-- Stats rapides du profil --}}
            <div class="grid grid-cols-2 gap-2 mt-4 pt-3" style="border-top: 1px solid var(--edc-border);">
                <div>
                    <p class="text-lg font-bold" style="color: var(--edc-text-primary);">
                        {{ $stats['demandes_total'] }}
                    </p>
                    <p class="text-[10px]" style="color: var(--edc-text-muted);">Demandes</p>
                </div>
                <div>
                    <p class="text-lg font-bold" style="color: var(--edc-text-primary);">
                        {{ $stats['formations_total'] ?? 0 }}
                    </p>
                    <p class="text-[10px]" style="color: var(--edc-text-muted);">Formations</p>
                </div>
            </div>
            
            <a href="{{ route('client.profil') }}" 
               class="btn-tertiary btn-sm w-full mt-4 btn-touch">
                ✏️ Modifier le profil
            </a>
        </div>

        {{-- Actions rapides - Optimisé mobile --}}
        <div class="edc-card p-4 sm:p-5">
            <h3 class="font-bold text-sm mb-3 flex items-center gap-2" style="color: var(--edc-text-primary);">
                <span>⚡</span> Actions rapides
            </h3>
            <div class="grid grid-cols-2 gap-2">
                @foreach([
                    ['client.demande.form',              '💼', 'Nouvelle demande', 'Créer une demande de service'],
                    ['client.formations.disponibles',    '🎓', 'Formations', 'Voir le catalogue'],
                    ['client.qcms.index',                '📝', 'QCMs', 'Tests disponibles'],
                    ['messages.index',                   '💬', 'Messages', 'Voir la messagerie'],
                    ['client.temoignages.index',         '⭐', 'Avis', 'Donner mon avis'],
                    ['client.paiements',                 '💰', 'Paiements', 'Voir mes factures'],
                ] as $a)
                <a href="{{ route($a[0]) }}"
                    class="flex flex-col items-center sm:flex-row sm:items-center gap-1.5 sm:gap-2 p-3 rounded-xl transition text-xs font-semibold btn-touch group hover:border-opacity-100"
                    style="background-color: var(--edc-bg-base); color: var(--edc-text-secondary); border: 1px solid var(--edc-border);"
                    title="{{ $a[3] }}">
                    <span class="text-lg sm:text-base flex-shrink-0">{{ $a[1] }}</span>
                    <span class="truncate text-center sm:text-left">{{ $a[2] }}</span>
                </a>
                @endforeach
            </div>
        </div>
        
        {{-- Conseil du jour --}}
        <div class="edc-card p-4" style="border-left: 3px solid var(--edc-accent-gold);">
            <p class="text-xs font-semibold mb-1" style="color: var(--edc-accent-gold);">💡 Le saviez-vous ?</p>
            <p class="text-xs" style="color: var(--edc-text-secondary);">
                Vous pouvez suivre l'avancement de vos demandes en temps réel depuis la section 
                <a href="{{ route('client.demandes') }}" class="underline" style="color: var(--edc-primary-light);">Mes demandes</a>.
            </p>
        </div>
    </div>
</div>
@endsection
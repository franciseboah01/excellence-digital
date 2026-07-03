@extends('layouts.client')
@section('title', 'Notifications')

@section('content')

{{-- HEADER - Optimisé mobile --}}
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">
                🔔 Mes Notifications
            </h1>
            <p class="text-xs sm:text-sm mt-0.5" style="color: var(--edc-text-secondary);">
                {{ $notifications->total() }} notification(s) • Marquées comme lues à l'ouverture
            </p>
        </div>
        
        {{-- Bouton Marquer tout comme lu --}}
        @if($notifications->where('lu', false)->count() > 0)
        <form method="POST" action="{{ route('notifications.marquer-lu') }}" class="flex-shrink-0">
            @csrf
            <button type="submit" 
                    class="btn-tertiary btn-touch text-xs w-full sm:w-auto justify-center">
                <span>✅</span>
                <span>Tout marquer comme lu</span>
            </button>
        </form>
        @endif
    </div>
    
    {{-- Filtres rapides --}}
    <div class="flex gap-2 mt-4 overflow-x-auto pb-2 scrollbar-hide">
        @php
            $filtres = [
                'tous' => ['📋', 'Toutes', null],
                'info' => ['📢', 'Infos', '#3B82F6'],
                'success' => ['✅', 'Succès', '#10B981'],
                'warning' => ['⚠️', 'Alertes', '#F59E0B'],
                'error' => ['❌', 'Erreurs', '#EF4444'],
            ];
            $currentFilter = request('type', 'tous');
        @endphp
        
        @foreach($filtres as $key => $filtre)
        <a href="{{ route('client.notifications', $key !== 'tous' ? ['type' => $key] : []) }}" 
           class="flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition flex-shrink-0"
           style="{{ $currentFilter === $key 
               ? 'background-color: '.($filtre[2] ?? 'var(--edc-primary)').'; color: white;' 
               : 'background-color: var(--edc-bg-card); color: var(--edc-text-secondary); border: 1px solid var(--edc-border);' }}">
            <span>{{ $filtre[0] }}</span>
            <span>{{ $filtre[1] }}</span>
        </a>
        @endforeach
    </div>
</div>

{{-- LISTE DES NOTIFICATIONS --}}
<div class="edc-card overflow-hidden">
    @forelse($notifications as $notif)
    <div class="flex items-start gap-3 sm:gap-4 p-4 sm:p-5 transition-all duration-200 relative"
        style="border-bottom: 1px solid var(--edc-border); {{ !$notif->lu ? 'background-color: rgba(59,130,246,0.04);' : '' }}"
        onmouseover="this.style.backgroundColor='{{ !$notif->lu ? 'rgba(59,130,246,0.08)' : 'rgba(255,255,255,0.02)' }}'"
        onmouseout="this.style.backgroundColor='{{ !$notif->lu ? 'rgba(59,130,246,0.04)' : 'transparent' }}'">
        
        {{-- Indicateur non lu --}}
        @if(!$notif->lu)
        <div class="absolute left-0 top-0 bottom-0 w-1" 
             style="background: linear-gradient(180deg, #3B82F6, #8B5CF6);">
        </div>
        @endif
        
        {{-- Icône --}}
        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center text-lg sm:text-xl flex-shrink-0"
            style="background-color: {{ $notif->type == 'success' ? 'rgba(16,185,129,0.12)' : 
                                         ($notif->type == 'warning' ? 'rgba(245,158,11,0.12)' : 
                                         ($notif->type == 'error' ? 'rgba(239,68,68,0.12)' : 'rgba(59,130,246,0.12)')) }};">
            @if($notif->type == 'success') ✅
            @elseif($notif->type == 'warning') ⚠️
            @elseif($notif->type == 'error') ❌
            @else 📢
            @endif
        </div>
        
        {{-- Contenu --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
                <p class="font-semibold text-sm sm:text-base" style="color: var(--edc-text-primary);">
                    {{ $notif->titre }}
                </p>
                
                {{-- Badge type (mobile) --}}
                <span class="badge text-[10px] sm:hidden flex-shrink-0"
                    style="background-color: {{ $notif->type == 'success' ? 'rgba(16,185,129,0.15)' : 
                                             ($notif->type == 'warning' ? 'rgba(245,158,11,0.15)' : 
                                             ($notif->type == 'error' ? 'rgba(239,68,68,0.15)' : 'rgba(59,130,246,0.15)')) }}; 
                           color: {{ $notif->type == 'success' ? '#34D399' : 
                                    ($notif->type == 'warning' ? '#FBBF24' : 
                                    ($notif->type == 'error' ? '#F87171' : '#60A5FA')) }};">
                    {{ $notif->type == 'success' ? 'Succès' : 
                       ($notif->type == 'warning' ? 'Alerte' : 
                       ($notif->type == 'error' ? 'Erreur' : 'Info')) }}
                </span>
            </div>
            
            <p class="text-sm mt-1 line-clamp-2 sm:line-clamp-3" style="color: var(--edc-text-secondary);">
                {{ $notif->message }}
            </p>
            
            <div class="flex items-center justify-between mt-2">
                <p class="text-xs" style="color: var(--edc-text-muted);">
                    {{ $notif->created_at->diffForHumans() }}
                    <span class="mx-1">•</span>
                    <span>{{ $notif->created_at->format('d/m/Y H:i') }}</span>
                </p>
                
                {{-- Statut lu/non lu --}}
                @if(!$notif->lu)
                <span class="flex items-center gap-1 text-xs font-medium" 
                      style="color: var(--edc-primary-light);">
                    <span class="w-1.5 h-1.5 rounded-full animate-pulse" 
                          style="background-color: var(--edc-primary);"></span>
                    <span>Nouveau</span>
                </span>
                @else
                <span class="text-xs" style="color: var(--edc-text-muted);">
                    ✓ Lu
                </span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-16 sm:py-20 px-4" style="color: var(--edc-text-muted);">
        <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-3xl sm:text-4xl"
             style="background-color: var(--edc-bg-base);">
            🔔
        </div>
        <p class="font-semibold text-sm sm:text-base mb-2" style="color: var(--edc-text-secondary);">
            Aucune notification
        </p>
        <p class="text-xs sm:text-sm">
            @if(request('type'))
                Aucune notification de type "{{ request('type') }}" pour le moment.
            @else
                Vous n'avez pas encore reçu de notifications.
            @endif
        </p>
    </div>
    @endforelse
</div>

{{-- PAGINATION - Optimisée mobile --}}
@if($notifications->hasPages())
<div class="mt-4 flex justify-center">
    {{ $notifications->onEachSide(1)->links() }}
</div>
@endif

{{-- Message d'aide --}}
@if($notifications->total() > 0)
<div class="mt-6 text-center">
    <p class="text-xs" style="color: var(--edc-text-muted);">
        💡 Les notifications sont automatiquement marquées comme lues à l'ouverture de cette page.
    </p>
</div>
@endif
@endsection
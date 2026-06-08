@extends('layouts.client')
@section('title', 'Notifications')

@section('content')
<div class="mb-6">
    <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">🔔 Mes Notifications</h1>
    <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">Toutes vos notifications sont marquées comme lues à l'ouverture.</p>
</div>

<div class="edc-card overflow-hidden">
    @forelse($notifications as $notif)
    <div class="flex items-start space-x-4 p-5 transition"
        style="border-bottom: 1px solid var(--edc-border);"
        onmouseover="this.style.backgroundColor='rgba(255,255,255,0.03)'"
        onmouseout="this.style.backgroundColor='transparent'">
        <div class="text-2xl mt-1 flex-shrink-0">
            @if($notif->type == 'success') ✅
            @elseif($notif->type == 'warning') ⚠️
            @elseif($notif->type == 'error') ❌
            @else 📢
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold" style="color: var(--edc-text-primary);">{{ $notif->titre }}</p>
            <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">{{ $notif->message }}</p>
            <p class="text-xs mt-2" style="color: var(--edc-text-muted);">{{ $notif->created_at->diffForHumans() }}</p>
        </div>
    </div>
    @empty
    <div class="text-center py-16" style="color: var(--edc-text-muted);">
        <p class="text-5xl mb-4">🔔</p>
        <p>Aucune notification pour le moment.</p>
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $notifications->links() }}</div>
@endsection
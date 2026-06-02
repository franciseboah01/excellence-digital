@extends('layouts.client')
@section('title', 'Notifications')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-blue-900">🔔 Mes Notifications</h1>
    <p class="text-gray-500 mt-1">Toutes vos notifications sont marquées comme lues à l'ouverture.</p>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    @forelse($notifications as $notif)
    <div class="flex items-start space-x-4 p-5 border-b border-gray-100 last:border-0
        hover:bg-gray-50 transition">
        <div class="text-2xl mt-1">
            @if($notif->type == 'success') ✅
            @elseif($notif->type == 'warning') ⚠️
            @elseif($notif->type == 'error') ❌
            @else 📢
            @endif
        </div>
        <div class="flex-1">
            <p class="font-semibold text-gray-800">{{ $notif->titre }}</p>
            <p class="text-gray-500 text-sm mt-1">{{ $notif->message }}</p>
            <p class="text-gray-300 text-xs mt-2">{{ $notif->created_at->diffForHumans() }}</p>
        </div>
    </div>
    @empty
    <div class="text-center py-16 text-gray-400">
        <p class="text-5xl mb-4">🔔</p>
        <p>Aucune notification pour le moment.</p>
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $notifications->links() }}</div>
@endsection
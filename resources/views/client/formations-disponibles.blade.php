@extends('layouts.client')
@section('title', 'Formations disponibles')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">🎓 Formations disponibles</h1>
        <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">Inscrivez-vous à une nouvelle formation</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($formations->sortByDesc('created_at') as $formation)
        <div class="edc-card overflow-hidden">
            @if($formation->image)
            <img src="{{ asset('storage/' . $formation->image) }}" class="w-full h-40 object-cover">
            @else
            <div class="w-full h-40 flex items-center justify-center" style="background: linear-gradient(135deg, #1e3a8a, #3B82F6);">
                <span class="text-4xl">🎓</span>
            </div>
            @endif
            <div class="p-5">
                <span class="badge badge-blue text-xs mb-2">{{ $formation->module->icone ?? '📚' }} {{ $formation->module->nom ?? '—' }}</span>
                <h3 class="font-bold mt-2" style="color: var(--edc-text-primary);">{{ $formation->titre }}</h3>
                <p class="text-xs mt-1" style="color: var(--edc-text-secondary);">{{ Str::limit($formation->description, 80) }}</p>
                <div class="flex items-center justify-between mt-3">
                    <span class="text-sm font-bold" style="color: var(--edc-primary-light);">
                        {{ $formation->prix ? number_format($formation->prix, 0, ',', ' ') . ' FCFA' : 'Gratuit' }}
                    </span>
                    <span class="text-xs" style="color: var(--edc-text-muted);">⏱ {{ $formation->duree ?? '—' }}</span>
                </div>
                <form method="POST" action="{{ route('client.formations.inscrire', $formation) }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn-primary btn-sm w-full">
                        ➕ S'inscrire
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 edc-card text-center py-16" style="color: var(--edc-text-muted);">
            <p class="text-5xl mb-4">🎓</p>
            <p>Aucune formation disponible pour le moment.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
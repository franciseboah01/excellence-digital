@extends('layouts.enseignant')
@section('title', 'Mes Ressources')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">📚 Mes Ressources</h1>
    <a href="{{ route('enseignant.ressources.create') }}" class="btn-primary btn-sm">
        + Ajouter une ressource
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($ressources as $ressource)
    @php
        $config = match($ressource->type) {
            'pdf'      => ['rgba(239,68,68,0.06)', 'rgba(239,68,68,0.20)',  '📄', '#F87171'],
            'ebook'    => ['rgba(168,85,247,0.06)', 'rgba(168,85,247,0.20)', '📖', '#C084FC'],
            'lien'     => ['rgba(16,185,129,0.06)', 'rgba(16,185,129,0.20)', '🔗', '#34D399'],
            'video'    => ['rgba(245,158,11,0.06)', 'rgba(245,158,11,0.20)', '🎬', '#FBBF24'],
            'document' => ['rgba(59,130,246,0.06)', 'rgba(59,130,246,0.20)', '📝', '#60A5FA'],
            default    => ['rgba(148,163,184,0.06)', 'rgba(148,163,184,0.20)', '📎', '#94A3B8'],
        };
    @endphp
    <div class="edc-card overflow-hidden">
        <div class="p-5">
            <div class="flex items-start justify-between mb-3">
                <span class="text-3xl">{{ $config[2] }}</span>
                <span class="badge badge-gray uppercase">{{ $ressource->type }}</span>
            </div>
            <h3 class="font-bold mb-1" style="color: var(--edc-text-primary);">{{ $ressource->titre }}</h3>
            <p class="text-xs font-medium mb-1" style="color: var(--edc-primary-light);">{{ $ressource->formation->titre }}</p>
            @if($ressource->niveau)
            <p class="text-xs" style="color: var(--edc-text-muted);">📂 {{ $ressource->niveau->nom }}</p>
            @endif
            @if($ressource->description)
            <p class="text-xs mt-2" style="color: var(--edc-text-secondary);">{{ Str::limit($ressource->description, 70) }}</p>
            @endif
        </div>
        <div class="px-5 py-3 flex justify-between items-center" style="border-top: 1px solid var(--edc-border); background-color: var(--edc-bg-base);">
            <span class="text-xs" style="color: var(--edc-text-muted);">{{ $ressource->created_at->format('d/m/Y') }}</span>
            <div class="flex space-x-3">
                <a href="{{ route('enseignant.ressources.edit', $ressource) }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">✏️ Modifier</a>
                <form method="POST" action="{{ route('enseignant.ressources.destroy', $ressource) }}"
                    onsubmit="return confirm('Supprimer cette ressource ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-medium hover:underline" style="color: var(--edc-danger);">🗑️ Supprimer</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 edc-card text-center py-16" style="color: var(--edc-text-muted);">
        <p class="text-5xl mb-4">📭</p>
        <p class="font-medium">Aucune ressource ajoutée pour le moment.</p>
        <a href="{{ route('enseignant.ressources.create') }}" class="btn-primary btn-sm mt-4 inline-block">
            Ajouter ma première ressource
        </a>
    </div>
    @endforelse
</div>

<div class="mt-6">{{ $ressources->links() }}</div>
@endsection
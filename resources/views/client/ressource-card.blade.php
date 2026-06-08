@php
    $config = match($ressource->type) {
        'pdf'      => ['rgba(239,68,68,0.06)', 'rgba(239,68,68,0.2)',  '📄', '#F87171'],
        'ebook'    => ['rgba(168,85,247,0.06)', 'rgba(168,85,247,0.2)', '📖', '#C084FC'],
        'lien'     => ['rgba(16,185,129,0.06)', 'rgba(16,185,129,0.2)', '🔗', '#34D399'],
        'video'    => ['rgba(245,158,11,0.06)', 'rgba(245,158,11,0.2)', '🎬', '#FBBF24'],
        'document' => ['rgba(59,130,246,0.06)', 'rgba(59,130,246,0.2)', '📝', '#60A5FA'],
        default    => ['rgba(148,163,184,0.06)', 'rgba(148,163,184,0.2)', '📎', '#94A3B8'],
    };

    $infos = $ressource->fichier_path
        ? \App\Services\FichierService::infos($ressource->fichier_path)
        : [];
@endphp

<div class="rounded-xl p-4 transition hover:scale-[1.01]"
    style="background-color: {{ $config[0] }}; border: 1px solid {{ $config[1] }};">
    <div class="flex items-start space-x-3">
        <span class="text-2xl flex-shrink-0">{{ $config[1] }}</span>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-sm truncate" style="color: var(--edc-text-primary);">{{ $ressource->titre }}</p>
            @if($ressource->description)
            <p class="text-xs mt-1" style="color: var(--edc-text-secondary);">{{ Str::limit($ressource->description, 60) }}</p>
            @endif

            {{-- Infos fichier --}}
            @if(!empty($infos))
            <p class="text-xs mt-1" style="color: var(--edc-text-muted);">
                📦 {{ $infos['taille_mb'] }} MB •
                {{ strtoupper($infos['extension']) }}
            </p>
            @endif

            <div class="mt-3">
                @if(in_array($ressource->type, ['lien', 'video']))
                    <a href="{{ $ressource->lien_url }}" target="_blank"
                        class="inline-flex items-center text-xs font-medium hover:underline"
                        style="color: {{ $config[3] }};">
                        Ouvrir le lien →
                    </a>
                @elseif($ressource->fichier_path)
                    <button
                        onclick="ouvrirFichierSecurise({{ $ressource->id }}, '{{ $ressource->type }}')"
                        class="inline-flex items-center text-xs font-medium hover:underline"
                        style="color: {{ $config[3] }};">
                        @if($ressource->type === 'pdf') 📖 Lire le PDF
                        @else 📥 Télécharger
                        @endif
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
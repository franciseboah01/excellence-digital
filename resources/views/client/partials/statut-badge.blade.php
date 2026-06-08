@php
    $config = match($statut) {
        'en_attente' => ['background-color: rgba(245,158,11,0.12); color: #FBBF24;', '⏳ En attente'],
        'en_cours'   => ['background-color: rgba(59,130,246,0.12); color: #60A5FA;', '🔄 En cours'],
        'termine'    => ['background-color: rgba(16,185,129,0.12); color: #34D399;', '✅ Terminé'],
        'annule'     => ['background-color: rgba(239,68,68,0.12); color: #F87171;', '❌ Annulé'],
        default      => ['background-color: rgba(148,163,184,0.10); color: #94A3B8;', $statut],
    };
@endphp
<span class="text-xs px-3 py-1 rounded-full font-medium" style="{{ $config[0] }}">
    {{ $config[1] }}
</span>
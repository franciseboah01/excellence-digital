@php
    $config = match($statut) {
        'en_attente' => ['bg-yellow-100 text-yellow-800', '⏳ En attente'],
        'en_cours'   => ['bg-blue-100 text-blue-800',   '🔄 En cours'],
        'termine'    => ['bg-green-100 text-green-800', '✅ Terminé'],
        'annule'     => ['bg-red-100 text-red-800',     '❌ Annulé'],
        default      => ['bg-gray-100 text-gray-600',   $statut],
    };
@endphp
<span class="text-xs px-3 py-1 rounded-full font-medium {{ $config[0] }}">
    {{ $config[1] }}
</span>
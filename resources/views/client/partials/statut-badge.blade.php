@php
    $config = match($statut) {
        'en_attente' => ['background-color: rgba(245,158,11,0.12); color: #FBBF24;', '⏳', 'En attente'],
        'en_cours'   => ['background-color: rgba(59,130,246,0.12); color: #60A5FA;', '🔄', 'En cours'],
        'termine'    => ['background-color: rgba(16,185,129,0.12); color: #34D399;', '✅', 'Terminé'],
        'annule'     => ['background-color: rgba(239,68,68,0.12); color: #F87171;', '❌', 'Annulé'],
        'valide'     => ['background-color: rgba(16,185,129,0.12); color: #34D399;', '✅', 'Validé'],
        'refuse'     => ['background-color: rgba(239,68,68,0.12); color: #F87171;', '❌', 'Refusé'],
        'paye'       => ['background-color: rgba(16,185,129,0.12); color: #34D399;', '💳', 'Payé'],
        'en_attente_paiement' => ['background-color: rgba(245,158,11,0.12); color: #FBBF24;', '⏳', 'Paiement en attente'],
        default      => ['background-color: rgba(148,163,184,0.10); color: #94A3B8;', '📋', ucfirst($statut)],
    };
@endphp
<span class="badge text-[10px] sm:text-xs inline-flex items-center gap-1 flex-shrink-0" 
      style="{{ $config[0] }}">
    <span>{{ $config[1] }}</span>
    <span>{{ $config[2] }}</span>
</span>
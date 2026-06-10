@extends('layouts.client')
@section('title', 'Mes Formations')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">🎓 Mes Formations</h1>
        <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">Accédez aux ressources de vos formations validées</p>
    </div>
    <a href="{{ route('client.formations.disponibles') }}" class="btn-primary btn-sm">
        ➕ S'inscrire à une formation
    </a>
</div>

@forelse($inscriptions as $inscription)
<div class="edc-card mb-6 overflow-hidden">
    <div class="p-5" style="background: linear-gradient(135deg, #1e3a8a, #2563eb);">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
            <div>
                <h2 class="text-xl font-bold" style="color: #fff;">{{ $inscription->formation->titre }}</h2>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.7);">
                    Module : {{ $inscription->formation->module->nom ?? '—' }}
                    @if($inscription->formation->duree)
                    • ⏱ {{ $inscription->formation->duree }}
                    @endif
                    @if($inscription->formation->prix)
                    • 💰 {{ number_format($inscription->formation->prix, 0, ',', ' ') }} FCFA
                    @endif
                </p>
            </div>
            @php
                $statutStyles = match($inscription->statut) {
                    'valide'     => ['background-color: rgba(16,185,129,0.2); color: #34D399;', '✅ Validé'],
                    'en_attente' => ['background-color: rgba(245,158,11,0.2); color: #FBBF24;', '⏳ En attente'],
                    'refuse'     => ['background-color: rgba(239,68,68,0.2); color: #F87171;', '❌ Refusé'],
                    default      => ['background-color: var(--edc-bg-elevated); color: var(--edc-text-muted);', $inscription->statut],
                };
            @endphp
            <span class="text-xs px-3 py-1.5 rounded-full font-semibold flex-shrink-0"
                style="{{ $statutStyles[0] }}">
                {{ $statutStyles[1] }}
            </span>
        </div>
    </div>

    <div class="p-5">
        @if($inscription->statut === 'valide')
            @php
                $aPaye = \App\Models\Paiement::where('user_id', auth()->id())
                    ->where('formation_id', $inscription->formation->id)
                    ->where('statut', 'complete')
                    ->exists();
            @endphp
            @if($inscription->formation->prix && !$aPaye)
            <a href="{{ route('client.paiement.form', ['formation', $inscription->formation->id]) }}" class="btn-success">
                <span>💳 Payer {{ number_format($inscription->formation->prix, 0, ',', ' ') }} FCFA</span>
            </a>
            @else
            <a href="{{ route('client.ressources', $inscription->formation) }}" class="btn-primary">
                <span>📚 Accéder aux ressources</span>
                <span>→</span>
            </a>
            @endif
        @else
        <div class="alert alert-warning">
            <span>⏳</span>
            <span>Votre inscription est en attente de validation par l'administrateur.</span>
        </div>
        @endif
    </div>
</div>
@empty
<div class="edc-card text-center py-16" style="color: var(--edc-text-muted);">
    <p class="text-5xl mb-4">🎓</p>
    <p class="font-medium">Vous n'êtes inscrit à aucune formation.</p>
    <a href="{{ route('client.formations.disponibles') }}" class="btn-primary btn-sm mt-4 inline-block">
        Voir les formations disponibles
    </a>
</div>
@endforelse
@endsection
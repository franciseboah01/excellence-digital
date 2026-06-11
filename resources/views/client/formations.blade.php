@extends('layouts.client')
@section('title', 'Mes Formations')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">🎓 Mes Formations</h1>
            <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">Accédez aux ressources de vos formations validées</p>
        </div>
        <a href="{{ route('client.formations.disponibles') }}" class="btn-primary btn-sm">
            ➕ S'inscrire à une formation
        </a>
    </div>

    @php
        $inscriptions = $inscriptions->sortByDesc('created_at');
    @endphp

    @if($inscriptions->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        @foreach($inscriptions as $inscription)
        <div class="edc-card overflow-hidden">
            {{-- Image --}}
            @if($inscription->formation->image)
            <img src="{{ asset('storage/' . $inscription->formation->image) }}"
                alt="{{ $inscription->formation->titre }}"
                class="w-full h-40 object-cover">
            @else
            <div class="w-full h-28 flex items-center justify-center"
                style="background: linear-gradient(135deg, #1e3a8a, #2563eb);">
                <span class="text-4xl">🎓</span>
            </div>
            @endif

            {{-- Infos --}}
            <div class="p-4" style="background: linear-gradient(135deg, #111827, #1a2332);">
                <div class="flex justify-between items-start gap-2">
                    <h2 class="text-base font-bold" style="color: #fff;">{{ $inscription->formation->titre }}</h2>
                    @php
                        $statutStyles = match($inscription->statut) {
                            'valide'     => ['background-color: rgba(16,185,129,0.3); color: #fff;', '✅ Validé'],
                            'en_attente' => ['background-color: rgba(245,158,11,0.3); color: #fff;', '⏳ En attente'],
                            'refuse'     => ['background-color: rgba(239,68,68,0.3); color: #fff;', '❌ Refusé'],
                            default      => ['background-color: rgba(148,163,184,0.3); color: #fff;', $inscription->statut],
                        };
                    @endphp
                    <span class="text-xs px-2 py-1 rounded-full font-semibold flex-shrink-0" style="{{ $statutStyles[0] }}">
                        {{ $statutStyles[1] }}
                    </span>
                </div>
                <p class="text-xs mt-2" style="color: rgba(255,255,255,0.7);">
                    {{ $inscription->formation->module->icone ?? '📚' }} {{ $inscription->formation->module->nom ?? '—' }}
                    @if($inscription->formation->duree) • ⏱ {{ $inscription->formation->duree }} @endif
                    @if($inscription->formation->prix) • 💰 {{ number_format($inscription->formation->prix, 0, ',', ' ') }} FCFA @endif
                </p>
            </div>

            {{-- Actions --}}
            <div class="p-4">
                @if($inscription->statut === 'valide')
                    @php
                        $aPaye = \App\Models\Paiement::where('user_id', auth()->id())
                            ->where('formation_id', $inscription->formation->id)
                            ->where('statut', 'complete')
                            ->exists();
                    @endphp
                    @if($inscription->formation->prix && !$aPaye)
                    <a href="{{ route('client.paiement.form', ['formation', $inscription->formation->id]) }}" class="btn-success btn-sm w-full text-center">
                        💳 Payer {{ number_format($inscription->formation->prix, 0, ',', ' ') }} FCFA
                    </a>
                    @else
                    <a href="{{ route('client.ressources', $inscription->formation) }}" class="btn-primary btn-sm w-full text-center">
                        📚 Accéder aux ressources →
                    </a>
                    @endif
                @else
                <div class="alert alert-warning text-xs">
                    <span>⏳</span><span>En attente de validation</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="edc-card text-center py-16" style="color: var(--edc-text-muted);">
        <p class="text-5xl mb-4">🎓</p>
        <p class="font-medium">Vous n'êtes inscrit à aucune formation.</p>
        <a href="{{ route('client.formations.disponibles') }}" class="btn-primary btn-sm mt-4 inline-block">
            Voir les formations disponibles
        </a>
    </div>
    @endif
</div>
@endsection
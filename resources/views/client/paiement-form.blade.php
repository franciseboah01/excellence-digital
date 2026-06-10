@extends('layouts.client')
@section('title', 'Payer')

@section('content')
<div class="max-w-lg mx-auto">
    <a href="{{ route('client.paiements') }}"
        class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
        style="color: var(--edc-primary-light);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Retour</span>
    </a>

    <h1 class="text-xl font-extrabold mt-2 mb-6" style="color: var(--edc-text-primary);">💳 Paiement</h1>

    <div class="edc-card p-6 mb-5">
        <p class="text-sm" style="color: var(--edc-text-secondary);">{{ $description }}</p>
        <p class="text-2xl font-extrabold mt-2" style="color: var(--edc-text-primary);">{{ number_format($montant, 0, ',', ' ') }} FCFA</p>
    </div>

    <div class="edc-card p-6">
        <form method="POST" action="{{ route('client.paiement.process') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="id" value="{{ $id }}">
            <input type="hidden" name="montant" value="{{ $montant }}">

            <div>
                <label class="edc-label">Moyen de paiement *</label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        'orange_money' => ['🟠', 'Orange Money'],
                        'mtn_money'    => ['🟡', 'MTN Mobile Money'],
                        'moov_money'   => ['🔵', 'Moov Money'],
                        'visa'         => ['💳', 'Carte Visa'],
                        'mastercard'   => ['🔴', 'Carte Mastercard'],
                    ] as $key => $m)
                    <label class="flex items-center space-x-2 p-3 rounded-xl cursor-pointer transition"
                        style="border: 2px solid var(--edc-border);"
                        onmouseover="this.style.borderColor='var(--edc-primary)'"
                        onmouseout="this.style.borderColor='var(--edc-border)'">
                        <input type="radio" name="mode_paiement" value="{{ $key }}" required class="flex-shrink-0" style="accent-color: #3B82F6;">
                        <span class="text-xs font-semibold" style="color: var(--edc-text-primary);">{{ $m[0] }} {{ $m[1] }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div id="champ_telephone" class="hidden">
                <label class="edc-label">Numéro de téléphone</label>
                <input type="text" name="telephone" class="edc-input" placeholder="+225 07 00 00 00 00">
            </div>

            <button type="submit" class="btn-primary w-full">💳 Payer {{ number_format($montant, 0, ',', ' ') }} FCFA</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('input[name="mode_paiement"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const tel = document.getElementById('champ_telephone');
            if (['orange_money','mtn_money','moov_money'].includes(this.value)) {
                tel.classList.remove('hidden');
            } else {
                tel.classList.add('hidden');
            }
        });
    });
</script>
@endpush
@endsection
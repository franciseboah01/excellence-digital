@extends('layouts.client')
@section('title', 'Paiement')

@section('content')
<div class="max-w-lg mx-auto">
    
    {{-- FIL D'ARIANE --}}
    <nav class="mb-4">
        <a href="{{ route('client.paiements') }}"
           class="inline-flex items-center space-x-1.5 text-sm font-medium hover:underline group transition"
           style="color: var(--edc-primary-light);">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retour à mes paiements</span>
        </a>
    </nav>

    {{-- TITRE --}}
    <h1 class="text-xl sm:text-2xl font-extrabold mt-2 mb-5" style="color: var(--edc-text-primary);">
        💳 Paiement
    </h1>

    {{-- RÉSUMÉ DE LA COMMANDE --}}
    <div class="edc-card p-5 sm:p-6 mb-5 relative overflow-hidden">
        {{-- Fond décoratif --}}
        <div class="absolute top-0 right-0 w-32 h-32 opacity-5" 
             style="background: radial-gradient(circle, #3B82F6, transparent);">
        </div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--edc-text-muted);">
                    Résumé de la commande
                </p>
                <span class="badge text-[10px]" style="background-color: rgba(59,130,246,0.12); color: #60A5FA;">
                    {{ $type === 'formation' ? '🎓 Formation' : '💼 Service' }}
                </span>
            </div>
            
            <p class="text-sm font-medium mb-3" style="color: var(--edc-text-secondary);">
                {{ $description }}
            </p>
            
            <div class="flex items-end justify-between pt-3" style="border-top: 1px solid var(--edc-border);">
                <span class="text-sm" style="color: var(--edc-text-muted);">Total à payer</span>
                <span class="text-2xl sm:text-3xl font-extrabold" style="color: var(--edc-text-primary);">
                    {{ number_format($montant, 0, ',', ' ') }} <span class="text-lg font-medium" style="color: var(--edc-text-muted);">FCFA</span>
                </span>
            </div>
            
            @if($montant == 0)
            <div class="mt-3 rounded-xl p-3 flex items-start gap-2"
                 style="background-color: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.25);">
                <span>ℹ️</span>
                <p class="text-xs" style="color: #34D399;">
                    Cette prestation est gratuite. Aucun paiement ne sera requis.
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- FORMULAIRE DE PAIEMENT --}}
    <div class="edc-card p-5 sm:p-6">
        <form method="POST" action="{{ route('client.paiement.process') }}" class="space-y-5" id="paiementForm">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="id" value="{{ $id }}">
            <input type="hidden" name="montant" value="{{ $montant }}">

            {{-- Choix du moyen de paiement --}}
            <div>
                <label class="edc-label flex items-center gap-1.5 mb-3">
                    <span>💳</span>
                    <span>Moyen de paiement <span style="color: var(--edc-danger);">*</span></span>
                </label>
                
                {{-- Mobile Money --}}
                <div class="space-y-2 mb-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider" style="color: var(--edc-text-muted);">
                        📱 Mobile Money
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        @foreach([
                            'orange_money' => ['🟠', 'Orange Money', '#FF6600'],
                            'mtn_money'    => ['🟡', 'MTN Money', '#FFCC00'],
                            'moov_money'   => ['🔵', 'Moov Money', '#0099FF'],
                        ] as $key => $m)
                        <label class="paiement-option flex items-center gap-2.5 p-3 rounded-xl cursor-pointer transition-all duration-200"
                               data-paiement="{{ $key }}">
                            <input type="radio" name="mode_paiement" value="{{ $key }}" 
                                   class="hidden peer" required>
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all duration-200"
                                 style="border-color: var(--edc-border);">
                                <div class="w-2.5 h-2.5 rounded-full opacity-0 transition-all duration-200"
                                     style="background-color: {{ $m[2] }};"></div>
                            </div>
                            <span class="text-xs font-semibold" style="color: var(--edc-text-primary);">
                                {{ $m[0] }} {{ $m[1] }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                
                {{-- Cartes bancaires --}}
                <div class="space-y-2">
                    <p class="text-[10px] font-semibold uppercase tracking-wider" style="color: var(--edc-text-muted);">
                        💳 Carte bancaire
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach([
                            'visa'       => ['💳', 'Visa', '#1A1F71'],
                            'mastercard' => ['🔴', 'Mastercard', '#EB001B'],
                        ] as $key => $m)
                        <label class="paiement-option flex items-center gap-2.5 p-3 rounded-xl cursor-pointer transition-all duration-200"
                               data-paiement="{{ $key }}">
                            <input type="radio" name="mode_paiement" value="{{ $key }}" 
                                   class="hidden peer" required>
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all duration-200"
                                 style="border-color: var(--edc-border);">
                                <div class="w-2.5 h-2.5 rounded-full opacity-0 transition-all duration-200"
                                     style="background-color: {{ $m[2] }};"></div>
                            </div>
                            <span class="text-xs font-semibold" style="color: var(--edc-text-primary);">
                                {{ $m[0] }} {{ $m[1] }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                
                @error('mode_paiement') 
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <span>⚠️</span><span>{{ $message }}</span>
                </p>
                @enderror
            </div>

            {{-- Champ téléphone (Mobile Money) --}}
            <div id="champ_telephone" class="hidden transition-all duration-300">
                <label class="edc-label flex items-center gap-1.5">
                    <span>📱</span>
                    <span>Numéro de téléphone</span>
                </label>
                <input type="text" 
                       name="telephone" 
                       class="edc-input @error('telephone') border-red-500 @enderror" 
                       placeholder="+225 07 00 00 00 00"
                       inputmode="tel">
                @error('telephone') 
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <span>⚠️</span><span>{{ $message }}</span>
                </p>
                @enderror
                <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--edc-text-muted);">
                    <span>🔒</span>
                    <span>Le paiement sera effectué via votre compte Mobile Money</span>
                </p>
            </div>
            
            {{-- Champ carte bancaire --}}
            <div id="champ_carte" class="hidden transition-all duration-300">
                <div class="rounded-xl p-4" style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
                    <p class="text-xs flex items-center gap-1.5" style="color: var(--edc-text-secondary);">
                        <span>🔒</span>
                        <span>Vous serez redirigé vers une page de paiement sécurisée après validation.</span>
                    </p>
                </div>
            </div>

            {{-- Bouton de paiement --}}
            <button type="submit" 
                    class="btn-primary w-full btn-touch text-sm font-bold group relative overflow-hidden"
                    id="submitBtn">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <span class="transition-transform duration-300 group-hover:scale-125">💳</span>
                    <span>Payer {{ number_format($montant, 0, ',', ' ') }} FCFA</span>
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
            </button>
            
            {{-- Sécurité --}}
            <div class="flex items-center justify-center gap-4 text-[10px]" style="color: var(--edc-text-muted);">
                <span class="flex items-center gap-1">🔒 SSL Sécurisé</span>
                <span class="flex items-center gap-1">🛡️ 3D Secure</span>
                <span class="flex items-center gap-1">📜 PCI DSS</span>
            </div>
        </form>
    </div>
    
    {{-- Aide --}}
    <div class="mt-6 text-center">
        <p class="text-xs" style="color: var(--edc-text-muted);">
            💡 Un problème avec le paiement ? 
            <a href="{{ route('contact') }}" class="underline" style="color: var(--edc-primary-light);">Contactez-nous</a>
        </p>
    </div>
</div>

@push('scripts')
<script>
    // Gestion de la sélection du moyen de paiement
    document.querySelectorAll('.paiement-option').forEach(label => {
        const radio = label.querySelector('input[type="radio"]');
        const indicator = label.querySelector('.w-5.h-5');
        const dot = indicator.querySelector('div');
        
        radio.addEventListener('change', function() {
            // Réinitialiser tous les indicateurs
            document.querySelectorAll('.paiement-option').forEach(l => {
                const ind = l.querySelector('.w-5.h-5');
                const d = ind.querySelector('div');
                ind.style.borderColor = 'var(--edc-border)';
                ind.style.backgroundColor = 'transparent';
                d.style.opacity = '0';
                l.style.borderColor = 'var(--edc-border)';
                l.style.backgroundColor = 'transparent';
            });
            
            // Activer l'indicateur sélectionné
            if (this.checked) {
                indicator.style.borderColor = 'var(--edc-primary)';
                indicator.style.backgroundColor = 'rgba(59,130,246,0.1)';
                dot.style.opacity = '1';
                label.style.borderColor = 'var(--edc-primary)';
                label.style.backgroundColor = 'rgba(59,130,246,0.05)';
                label.style.border = '2px solid var(--edc-primary)';
                
                // Afficher/masquer les champs selon le type
                const champTel = document.getElementById('champ_telephone');
                const champCarte = document.getElementById('champ_carte');
                
                if (['orange_money','mtn_money','moov_money'].includes(this.value)) {
                    champTel.classList.remove('hidden');
                    champTel.style.opacity = '0';
                    champTel.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        champTel.style.opacity = '1';
                        champTel.style.transform = 'translateY(0)';
                    }, 50);
                    champCarte.classList.add('hidden');
                } else if (['visa','mastercard'].includes(this.value)) {
                    champTel.classList.add('hidden');
                    champCarte.classList.remove('hidden');
                    champCarte.style.opacity = '0';
                    champCarte.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        champCarte.style.opacity = '1';
                        champCarte.style.transform = 'translateY(0)';
                    }, 50);
                }
            }
        });
        
        // Effet hover
        label.addEventListener('mouseenter', function() {
            if (!radio.checked) {
                label.style.borderColor = 'var(--edc-border-hover)';
                label.style.backgroundColor = 'rgba(255,255,255,0.02)';
            }
        });
        
        label.addEventListener('mouseleave', function() {
            if (!radio.checked) {
                label.style.borderColor = 'var(--edc-border)';
                label.style.backgroundColor = 'transparent';
            }
        });
    });
</script>
@endpush
@endsection
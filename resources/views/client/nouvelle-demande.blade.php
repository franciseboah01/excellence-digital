@extends('layouts.client')
@section('title', 'Nouvelle demande')

@section('content')
<div class="max-w-2xl mx-auto">
    
    {{-- FIL D'ARIANE --}}
    <nav class="mb-4 sm:mb-6">
        <a href="{{ route('client.dashboard') }}" 
           class="inline-flex items-center space-x-1.5 text-sm font-medium hover:underline group transition"
           style="color: var(--edc-primary-light);">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retour au dashboard</span>
        </a>
        
        <h1 class="text-xl sm:text-2xl font-extrabold mt-2" style="color: var(--edc-text-primary);">
            📋 Nouvelle demande de service
        </h1>
        <p class="text-xs sm:text-sm mt-1" style="color: var(--edc-text-secondary);">
            Remplissez le formulaire ci-dessous pour soumettre votre demande
        </p>
    </div>

    {{-- ÉTAPES (indicateur visuel) --}}
    <div class="flex items-center justify-center gap-2 mb-6">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white"
                 style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                1
            </div>
            <span class="text-xs font-semibold hidden sm:inline" style="color: var(--edc-text-primary);">Formulaire</span>
        </div>
        <div class="w-8 sm:w-12 h-0.5" style="background-color: var(--edc-border);"></div>
        <div class="flex items-center gap-2 opacity-50">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                 style="background-color: var(--edc-bg-elevated); color: var(--edc-text-muted);">
                2
            </div>
            <span class="text-xs font-semibold hidden sm:inline" style="color: var(--edc-text-muted);">Paiement</span>
        </div>
        <div class="w-8 sm:w-12 h-0.5" style="background-color: var(--edc-border);"></div>
        <div class="flex items-center gap-2 opacity-50">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                 style="background-color: var(--edc-bg-elevated); color: var(--edc-text-muted);">
                3
            </div>
            <span class="text-xs font-semibold hidden sm:inline" style="color: var(--edc-text-muted);">Confirmation</span>
        </div>
    </div>

    {{-- FORMULAIRE --}}
    <div class="edc-card p-5 sm:p-8">
        <form method="POST" action="{{ route('client.demande.store') }}" class="space-y-5" id="demandeForm">
            @csrf

            {{-- Profil info (carte résumé) --}}
            <div class="flex items-center gap-3 p-4 rounded-xl mb-2" 
                 style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0"
                     style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold truncate" style="color: var(--edc-text-primary);">
                        {{ auth()->user()->nom_complet }}
                    </p>
                    <p class="text-xs truncate" style="color: var(--edc-text-muted);">
                        {{ auth()->user()->email }}
                    </p>
                </div>
            </div>

            {{-- Téléphone --}}
            <div>
                <label class="edc-label flex items-center gap-1.5">
                    <span>📱</span>
                    <span>Téléphone / WhatsApp</span>
                </label>
                <input type="text" 
                       name="telephone_visiteur" 
                       value="{{ old('telephone_visiteur', auth()->user()->telephone) }}"
                       class="edc-input @error('telephone_visiteur') border-red-500 @enderror" 
                       placeholder="+225 07 00 00 00 00"
                       inputmode="tel">
                @error('telephone_visiteur') 
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <span>⚠️</span><span>{{ $message }}</span>
                </p>
                @enderror
                <p class="text-[10px] mt-1" style="color: var(--edc-text-muted);">
                    Nous vous contacterons sur ce numéro pour le suivi
                </p>
            </div>

            {{-- Service souhaité --}}
            <div>
                <label class="edc-label flex items-center gap-1.5">
                    <span>⚙️</span>
                    <span>Service souhaité <span style="color: var(--edc-danger);">*</span></span>
                </label>
                <select name="service_id" 
                        id="service_id" 
                        class="edc-select @error('service_id') border-red-500 @enderror" 
                        required 
                        onchange="afficherPrix()">
                    <option value="">-- Choisir un service --</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}"
                        data-prix="{{ $service->prix }}"
                        data-icone="{{ $service->icone ?? '⚙️' }}"
                        {{ old('service_id', request('service')) == $service->id ? 'selected' : '' }}>
                        {{ $service->icone ?? '⚙️' }} {{ $service->titre }}
                    </option>
                    @endforeach
                </select>
                @error('service_id') 
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <span>⚠️</span><span>{{ $message }}</span>
                </p>
                @enderror
            </div>

            {{-- Prix affiché dynamiquement --}}
            <div id="prix_affiche" class="hidden rounded-xl p-4 transition-all duration-300"
                style="background-color: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.20);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium" style="color: var(--edc-text-muted);">Prix du service</p>
                        <p class="text-2xl sm:text-3xl font-extrabold mt-1" style="color: var(--edc-primary-light);" id="prix_texte"></p>
                    </div>
                    <div id="prix_icone" class="text-3xl sm:text-4xl opacity-30">
                        💰
                    </div>
                </div>
                <div id="prix_gratuit_info" class="hidden mt-3 pt-3" style="border-top: 1px solid var(--edc-border);">
                    <p class="text-xs flex items-center gap-1.5" style="color: var(--edc-text-muted);">
                        <span>ℹ️</span>
                        <span>Ce service est gratuit, vous serez redirigé directement vers la confirmation.</span>
                    </p>
                </div>
            </div>

            {{-- Message --}}
            <div>
                <label class="edc-label flex items-center gap-1.5">
                    <span>💬</span>
                    <span>Message / Détails</span>
                </label>
                <textarea name="message" 
                          id="message"
                          rows="4" 
                          class="edc-input @error('message') border-red-500 @enderror resize-none"
                          placeholder="Décrivez votre besoin en détail..."
                          maxlength="1000"
                          oninput="updateCharCount()">{{ old('message') }}</textarea>
                <div class="flex justify-between items-center mt-1.5">
                    @error('message') 
                    <p class="text-red-400 text-xs flex items-center gap-1">
                        <span>⚠️</span><span>{{ $message }}</span>
                    </p>
                    @else
                    <span></span>
                    @enderror
                    <span id="charCount" class="text-[10px]" style="color: var(--edc-text-muted);">
                        0/1000
                    </span>
                </div>
            </div>

            {{-- Bouton de soumission --}}
            <button type="submit" 
                    class="btn-primary w-full btn-touch text-sm font-bold group relative overflow-hidden"
                    id="submitBtn">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <span class="transition-transform duration-300 group-hover:scale-125">📩</span>
                    <span>Envoyer et procéder au paiement</span>
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
            </button>
            
            {{-- Message de sécurité --}}
            <p class="text-center text-[10px] flex items-center justify-center gap-1" 
               style="color: var(--edc-text-muted);">
                <span>🔒</span>
                <span>Vos informations sont sécurisées et chiffrées</span>
            </p>
        </form>
    </div>
    
    {{-- Aide --}}
    <div class="mt-6 text-center">
        <p class="text-xs" style="color: var(--edc-text-muted);">
            💡 Besoin d'aide ? <a href="{{ route('contact') }}" class="underline" style="color: var(--edc-primary-light);">Contactez-nous</a>
        </p>
    </div>
</div>

@push('scripts')
<script>
    function afficherPrix() {
        const select = document.getElementById('service_id');
        const option = select.options[select.selectedIndex];
        const prix = option.getAttribute('data-prix');
        const icone = option.getAttribute('data-icone') || '⚙️';
        const div = document.getElementById('prix_affiche');
        const texte = document.getElementById('prix_texte');
        const iconeDiv = document.getElementById('prix_icone');
        const gratuitInfo = document.getElementById('prix_gratuit_info');
        const submitBtn = document.getElementById('submitBtn');

        if (prix !== null && prix !== undefined && prix !== '') {
            // Mettre à jour l'icône
            iconeDiv.textContent = icone;
            
            if (parseFloat(prix) > 0) {
                texte.textContent = new Intl.NumberFormat('fr-FR').format(prix) + ' FCFA';
                gratuitInfo.classList.add('hidden');
                submitBtn.querySelector('span span:last-child').textContent = 'Envoyer et procéder au paiement';
            } else {
                texte.textContent = 'Gratuit';
                gratuitInfo.classList.remove('hidden');
                submitBtn.querySelector('span span:last-child').textContent = 'Envoyer la demande';
            }
            
            // Animation d'apparition
            div.classList.remove('hidden');
            div.style.opacity = '0';
            div.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                div.style.opacity = '1';
                div.style.transform = 'translateY(0)';
            }, 50);
        } else {
            div.classList.add('hidden');
            submitBtn.querySelector('span span:last-child').textContent = 'Envoyer et procéder au paiement';
        }
    }

    // Compteur de caractères
    function updateCharCount() {
        const textarea = document.getElementById('message');
        const charCount = document.getElementById('charCount');
        const current = textarea.value.length;
        charCount.textContent = current + '/1000';
        
        if (current > 900) {
            charCount.style.color = '#F59E0B';
        } else if (current >= 1000) {
            charCount.style.color = '#EF4444';
        } else {
            charCount.style.color = '#64748B';
        }
    }

    // Afficher au chargement si un service est déjà sélectionné
    document.addEventListener('DOMContentLoaded', function() {
        afficherPrix();
        updateCharCount();
    });
</script>
@endpush
@endsection
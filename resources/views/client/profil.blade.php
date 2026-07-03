@extends('layouts.client')
@section('title', 'Mon Profil')

@section('content')

{{-- HEADER - Optimisé mobile --}}
<div class="mb-6">
    <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">
        👤 Mon Profil
    </h1>
    <p class="text-xs sm:text-sm mt-0.5" style="color: var(--edc-text-secondary);">
        Gérez vos informations personnelles et votre sécurité
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">

    {{-- INFORMATIONS PERSONNELLES --}}
    <div class="edc-card p-5 sm:p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm"
                 style="background-color: rgba(59,130,246,0.12);">
                👤
            </div>
            <h2 class="text-base sm:text-lg font-bold" style="color: var(--edc-text-primary);">
                Informations personnelles
            </h2>
        </div>

        <form method="POST" action="{{ route('client.profil.update') }}" enctype="multipart/form-data" id="profilForm">
            @csrf

            {{-- Avatar - Section améliorée --}}
            <div class="flex flex-col sm:flex-row items-center gap-4 mb-6 p-4 rounded-xl"
                 style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
                <div class="relative group">
                    <div class="w-20 h-20 sm:w-16 sm:h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold overflow-hidden flex-shrink-0 ring-2 ring-offset-2 ring-offset-[#1A2235] transition-all duration-300 group-hover:ring-blue-500"
                         style="background: linear-gradient(135deg, #3B82F6, #1D4ED8); ring-color: var(--edc-primary);">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                 class="w-full h-full object-cover" 
                                 alt="Avatar">
                        @else
                            {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                        @endif
                        
                        {{-- Overlay au hover --}}
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="text-xs font-medium">📷</span>
                        </div>
                    </div>
                </div>
                
                <div class="text-center sm:text-left">
                    <label class="cursor-pointer inline-flex items-center gap-1.5 text-sm font-medium transition-colors hover:underline px-3 py-2 rounded-lg"
                           style="color: var(--edc-primary-light);"
                           onmouseover="this.style.backgroundColor='rgba(59,130,246,0.08)'"
                           onmouseout="this.style.backgroundColor='transparent'">
                        <span>📷</span>
                        <span>Changer la photo</span>
                        <input type="file" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(event)">
                    </label>
                    <p class="text-xs mt-1.5" style="color: var(--edc-text-muted);">
                        JPG, PNG ou WebP — max 2 Mo
                    </p>
                </div>
            </div>

            {{-- Prénom & Nom --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="edc-label flex items-center gap-1.5">
                        <span>✏️</span><span>Prénom</span>
                    </label>
                    <input type="text" 
                           name="prenom" 
                           value="{{ old('prenom', auth()->user()->prenom) }}" 
                           class="edc-input @error('prenom') border-red-500 @enderror"
                           placeholder="Votre prénom">
                    @error('prenom') 
                    <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                        <span>⚠️</span><span>{{ $message }}</span>
                    </p>
                    @enderror
                </div>
                <div>
                    <label class="edc-label flex items-center gap-1.5">
                        <span>✏️</span><span>Nom</span>
                    </label>
                    <input type="text" 
                           name="nom" 
                           value="{{ old('nom', auth()->user()->nom) }}" 
                           class="edc-input @error('nom') border-red-500 @enderror"
                           placeholder="Votre nom">
                    @error('nom') 
                    <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                        <span>⚠️</span><span>{{ $message }}</span>
                    </p>
                    @enderror
                </div>
            </div>

            {{-- Email (lecture seule) --}}
            <div class="mb-4">
                <label class="edc-label flex items-center gap-1.5">
                    <span>📧</span><span>Email</span>
                </label>
                <input type="email" 
                       value="{{ auth()->user()->email }}"
                       class="edc-input cursor-not-allowed"
                       style="opacity: 0.5;" 
                       disabled>
                <p class="text-[10px] mt-1 flex items-center gap-1" style="color: var(--edc-text-muted);">
                    <span>🔒</span>
                    <span>L'email ne peut pas être modifié</span>
                </p>
            </div>

            {{-- Téléphone --}}
            <div class="mb-6">
                <label class="edc-label flex items-center gap-1.5">
                    <span>📱</span><span>Téléphone / WhatsApp</span>
                </label>
                <input type="text" 
                       name="telephone" 
                       value="{{ old('telephone', auth()->user()->telephone) }}"
                       class="edc-input @error('telephone') border-red-500 @enderror" 
                       placeholder="+225 07 00 00 00 00"
                       inputmode="tel">
                @error('telephone') 
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <span>⚠️</span><span>{{ $message }}</span>
                </p>
                @enderror
            </div>

            {{-- Bouton enregistrer --}}
            <button type="submit" 
                    class="btn-primary w-full btn-touch text-sm font-bold group relative overflow-hidden">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <span class="transition-transform duration-300 group-hover:scale-125">💾</span>
                    <span>Enregistrer les modifications</span>
                </span>
            </button>
        </form>
    </div>

    {{-- SÉCURITÉ --}}
    <div class="space-y-5 sm:space-y-6">
        
        {{-- Changer mot de passe --}}
        <div class="edc-card p-5 sm:p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm"
                     style="background-color: rgba(245,158,11,0.12);">
                    🔒
                </div>
                <h2 class="text-base sm:text-lg font-bold" style="color: var(--edc-text-primary);">
                    Changer le mot de passe
                </h2>
            </div>

            <form method="POST" action="{{ route('client.password.update') }}" id="passwordForm">
                @csrf

                {{-- Mot de passe actuel --}}
                <div class="mb-4">
                    <label class="edc-label flex items-center gap-1.5">
                        <span>🔑</span><span>Mot de passe actuel</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="current_password" 
                               id="current_password"
                               class="edc-input pr-10 @error('current_password') border-red-500 @enderror"
                               placeholder="••••••••">
                        <button type="button" 
                                onclick="togglePassword('current_password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-lg opacity-50 hover:opacity-100 transition-opacity"
                                style="color: var(--edc-text-muted);">
                            👁️
                        </button>
                    </div>
                    @error('current_password') 
                    <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                        <span>⚠️</span><span>{{ $message }}</span>
                    </p>
                    @enderror
                </div>

                {{-- Nouveau mot de passe --}}
                <div class="mb-4">
                    <label class="edc-label flex items-center gap-1.5">
                        <span>🆕</span><span>Nouveau mot de passe</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="password"
                               class="edc-input pr-10 @error('password') border-red-500 @enderror"
                               placeholder="••••••••"
                               oninput="checkPasswordStrength()">
                        <button type="button" 
                                onclick="togglePassword('password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-lg opacity-50 hover:opacity-100 transition-opacity"
                                style="color: var(--edc-text-muted);">
                            👁️
                        </button>
                    </div>
                    
                    {{-- Indicateur de force --}}
                    <div id="passwordStrength" class="hidden mt-2">
                        <div class="flex gap-1 mb-1">
                            <div class="flex-1 h-1 rounded-full" id="strength1" style="background-color: var(--edc-bg-elevated);"></div>
                            <div class="flex-1 h-1 rounded-full" id="strength2" style="background-color: var(--edc-bg-elevated);"></div>
                            <div class="flex-1 h-1 rounded-full" id="strength3" style="background-color: var(--edc-bg-elevated);"></div>
                            <div class="flex-1 h-1 rounded-full" id="strength4" style="background-color: var(--edc-bg-elevated);"></div>
                        </div>
                        <p id="strengthText" class="text-[10px]" style="color: var(--edc-text-muted);"></p>
                    </div>
                    
                    @error('password') 
                    <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                        <span>⚠️</span><span>{{ $message }}</span>
                    </p>
                    @enderror
                </div>

                {{-- Confirmer mot de passe --}}
                <div class="mb-5">
                    <label class="edc-label flex items-center gap-1.5">
                        <span>✅</span><span>Confirmer le mot de passe</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation"
                               class="edc-input pr-10"
                               placeholder="••••••••">
                        <button type="button" 
                                onclick="togglePassword('password_confirmation')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-lg opacity-50 hover:opacity-100 transition-opacity"
                                style="color: var(--edc-text-muted);">
                            👁️
                        </button>
                    </div>
                </div>

                <button type="submit" 
                        class="btn-tertiary w-full btn-touch text-sm font-bold">
                    <span class="flex items-center justify-center gap-2">
                        <span>🔑</span><span>Changer le mot de passe</span>
                    </span>
                </button>
            </form>
        </div>
        
        {{-- Conseils de sécurité --}}
        <div class="edc-card p-4 sm:p-5" style="border-left: 3px solid var(--edc-primary);">
            <h3 class="text-xs font-bold mb-2 flex items-center gap-1.5" style="color: var(--edc-text-primary);">
                <span>🛡️</span> Conseils de sécurité
            </h3>
            <ul class="space-y-1.5 text-xs" style="color: var(--edc-text-muted);">
                <li class="flex items-start gap-1.5">
                    <span>•</span>
                    <span>Utilisez un mot de passe unique d'au moins 8 caractères</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span>•</span>
                    <span>Mélangez lettres, chiffres et caractères spéciaux</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span>•</span>
                    <span>Ne partagez jamais votre mot de passe</span>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- Message de confirmation --}}
@if(session('status') === 'profile-updated' || session('status') === 'password-updated')
<div class="fixed bottom-4 right-4 z-50 animate-fade-in-up" id="successToast">
    <div class="alert alert-success shadow-2xl flex items-center gap-2 px-4 py-3">
        <span>✅</span>
        <span class="text-sm">{{ session('status') === 'password-updated' ? 'Mot de passe mis à jour !' : 'Profil mis à jour !' }}</span>
        <button onclick="document.getElementById('successToast').remove()" class="ml-2 text-lg opacity-70 hover:opacity-100">✕</button>
    </div>
</div>

{{-- Auto-suppression du toast après 5 secondes --}}
@if(session('status'))
<script>
    setTimeout(() => {
        const toast = document.getElementById('successToast');
        if (toast) {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(() => toast.remove(), 300);
        }
    }, 5000);
</script>
@endif
@endif

@push('scripts')
<script>
    // Prévisualisation de l'avatar
    function previewAvatar(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const avatarImg = document.querySelector('.w-20.h-20 img, .w-16.h-16 img');
                if (avatarImg) {
                    avatarImg.src = e.target.result;
                } else {
                    // Créer une balise img si c'était une initiale
                    const avatarContainer = document.querySelector('.w-20.h-20, .w-16.h-16');
                    if (avatarContainer) {
                        avatarContainer.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover" alt="Avatar">';
                    }
                }
            };
            reader.readAsDataURL(file);
        }
    }
    
    // Afficher/masquer le mot de passe
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        if (input.type === 'password') {
            input.type = 'text';
            button.textContent = '🙈';
        } else {
            input.type = 'password';
            button.textContent = '👁️';
        }
    }
    
    // Indicateur de force du mot de passe
    function checkPasswordStrength() {
        const password = document.getElementById('password').value;
        const strengthDiv = document.getElementById('passwordStrength');
        const strengthText = document.getElementById('strengthText');
        const s1 = document.getElementById('strength1');
        const s2 = document.getElementById('strength2');
        const s3 = document.getElementById('strength3');
        const s4 = document.getElementById('strength4');
        
        if (!password) {
            strengthDiv.classList.add('hidden');
            return;
        }
        
        strengthDiv.classList.remove('hidden');
        
        let score = 0;
        if (password.length >= 8) score++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) score++;
        if (password.match(/\d/)) score++;
        if (password.match(/[^a-zA-Z\d]/)) score++;
        
        const strengths = [
            { color: '#EF4444', text: 'Très faible' },
            { color: '#F59E0B', text: 'Faible' },
            { color: '#3B82F6', text: 'Bon' },
            { color: '#10B981', text: 'Excellent' },
        ];
        
        [s1, s2, s3, s4].forEach((s, i) => {
            s.style.backgroundColor = i < score ? strengths[score-1].color : 'var(--edc-bg-elevated)';
        });
        
        strengthText.textContent = score > 0 ? strengths[score-1].text : '';
    }
</script>
@endpush
@endsection
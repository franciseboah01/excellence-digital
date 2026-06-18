@php
    $siteNom  = \App\Models\Configuration::get('site_nom', 'Excellence Digital Center');
    $initiales = collect(explode(' ', $siteNom))
        ->map(fn($m) => strtoupper(substr($m, 0, 1)))
        ->take(3)
        ->implode('') ?: 'EDC';
@endphp

<x-guest-layout>
    <div class="mb-6 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2.5">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center font-black text-white text-lg"
                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                {{ $initiales }}
            </div>
            <span class="font-extrabold text-lg" style="color: #F1F5F9;">{{ $siteNom }}</span>
        </a>
        <p class="text-sm mt-2" style="color: #94A3B8;">Créez votre compte gratuitement</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="edc-label" for="prenom">Prénom</label>
            <input id="prenom" type="text" name="prenom" value="{{ old('prenom') }}"
                class="edc-input" placeholder="Votre prénom" required autofocus>
            @error('prenom') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="edc-label" for="nom">Nom</label>
            <input id="nom" type="text" name="nom" value="{{ old('nom') }}"
                class="edc-input" placeholder="Votre nom" required>
            @error('nom') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="edc-label" for="email">Adresse email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="edc-input" placeholder="votre@email.com" required>
            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="edc-label" for="telephone">Téléphone (WhatsApp)</label>
            <input id="telephone" type="text" name="telephone" value="{{ old('telephone') }}"
                class="edc-input" placeholder="+225 07 00 00 00 00">
            @error('telephone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="edc-label" for="formation_id">Choisir une formation (optionnel)</label>
            <select name="formation_id" id="formation_id" class="edc-select">
                <option value="">-- Aucune formation --</option>
                @foreach($formations as $formation)
                    <option value="{{ $formation->id }}" {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                        {{ $formation->titre }} ({{ $formation->niveau }})
                    </option>
                @endforeach
            </select>
            @error('formation_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="edc-label" for="password">Mot de passe</label>
            <input id="password" type="password" name="password"
                class="edc-input" placeholder="Minimum 8 caractères" required autocomplete="new-password">
            @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="edc-label" for="password_confirmation">Confirmer le mot de passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                class="edc-input" placeholder="Répétez le mot de passe" required autocomplete="new-password">
            @error('password_confirmation') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="btn-primary w-full mt-2">S'inscrire</button>
    </form>

    <p class="text-center text-sm mt-6" style="color: #64748B;">
        Déjà un compte ?
        <a href="{{ route('login') }}" class="font-semibold hover:underline" style="color: #60A5FA;">Se connecter</a>
    </p>

    <div class="text-center mt-4">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-1 text-sm transition"
            style="color: #64748B;" onmouseover="this.style.color='#60A5FA'" onmouseout="this.style.color='#64748B'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retour à l'accueil</span>
        </a>
    </div>
</x-guest-layout>
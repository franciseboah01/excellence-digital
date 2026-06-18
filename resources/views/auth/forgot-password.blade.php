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
        <p class="text-sm mt-2" style="color: #94A3B8;">Mot de passe oublié ?</p>
    </div>

    <p class="text-sm text-center mb-6" style="color: #94A3B8;">
        Indiquez votre adresse email et nous vous enverrons un lien de réinitialisation.
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <label class="edc-label" for="email">Adresse email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="edc-input" placeholder="votre@email.com" required autofocus>
            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="btn-primary w-full">Envoyer le lien de réinitialisation</button>
    </form>

    <div class="text-center mt-6">
        <a href="{{ route('login') }}" class="inline-flex items-center space-x-1 text-sm transition"
            style="color: #64748B;" onmouseover="this.style.color='#60A5FA'" onmouseout="this.style.color='#64748B'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retour à la connexion</span>
        </a>
    </div>
</x-guest-layout>
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
        <p class="text-sm mt-2" style="color: #94A3B8;">Connectez-vous à votre espace</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div>
            <label class="edc-label" for="email">Adresse email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="edc-input" placeholder="votre@email.com" required autofocus autocomplete="username">
            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="edc-label" for="password">Mot de passe</label>
            <input id="password" type="password" name="password"
                class="edc-input" placeholder="••••••••" required autocomplete="current-password">
            @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-gray-600 bg-gray-800 text-blue-600 focus:ring-blue-500">
                <span class="ms-2 text-sm" style="color: #94A3B8;">Se souvenir de moi</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium hover:underline" style="color: #60A5FA;">
                    Mot de passe oublié ?
                </a>
            @endif
        </div>
        <button type="submit" class="btn-primary w-full">Se connecter</button>
    </form>

    <p class="text-center text-sm mt-6" style="color: #64748B;">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="font-semibold hover:underline" style="color: #60A5FA;">
            S'inscrire gratuitement
        </a>
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
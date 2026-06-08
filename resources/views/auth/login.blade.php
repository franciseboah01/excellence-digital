<x-guest-layout>
    <div class="mb-6 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center font-black text-white text-lg"
                style="background:linear-gradient(135deg,#1e3a8a,#2563eb)">
                EDC
            </div>
            <span class="font-bold text-blue-900 text-lg">Excellence Digital Center</span>
        </a>
        <p class="text-gray-500 text-sm mt-2">Connectez-vous à votre espace</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Adresse email')" class="text-gray-700 font-medium" />
            <x-text-input id="email" class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl"
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                placeholder="votre@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Mot de passe --}}
        <div>
            <x-input-label for="password" :value="__('Mot de passe')" class="text-gray-700 font-medium" />
            <x-text-input id="password" class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl"
                type="password" name="password" required autocomplete="current-password"
                placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Se souvenir de moi --}}
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-blue-700 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Se souvenir de moi') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-blue-700 hover:text-blue-900 underline font-medium"
                    href="{{ route('password.request') }}">
                    {{ __('Mot de passe oublié ?') }}
                </a>
            @endif
        </div>

        {{-- Bouton --}}
        <button type="submit"
            class="w-full py-3 bg-blue-800 hover:bg-blue-900 text-white font-bold rounded-xl
                   transition duration-200 shadow-lg shadow-blue-200 text-sm">
            {{ __('Se connecter') }}
        </button>
    </form>

    {{-- Lien inscription --}}
    <p class="text-center text-sm text-gray-500 mt-6">
        {{ __("Pas encore de compte ?") }}
        <a href="{{ route('register') }}" class="text-blue-700 font-semibold hover:underline">
            {{ __("S'inscrire gratuitement") }}
        </a>
    </p>

    {{-- Retour accueil --}}
    <div class="text-center mt-4">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-1 text-sm text-gray-500 hover:text-blue-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>{{ __('Retour à l\'accueil') }}</span>
        </a>
    </div>
</x-guest-layout>
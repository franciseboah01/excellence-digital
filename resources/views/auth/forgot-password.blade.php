<x-guest-layout>
    <div class="mb-6 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center font-black text-white text-lg"
                style="background:linear-gradient(135deg,#1e3a8a,#2563eb)">
                EDC
            </div>
            <span class="font-bold text-blue-900 text-lg">Excellence Digital Center</span>
        </a>
        <p class="text-gray-500 text-sm mt-2">{{ __('Mot de passe oublié ?') }}</p>
    </div>

    <p class="text-sm text-gray-600 mb-6 text-center">
        {{ __('Pas de souci. Indiquez votre adresse email et nous vous enverrons un lien de réinitialisation.') }}
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Adresse email')" class="text-gray-700 font-medium" />
            <x-text-input id="email" class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl"
                type="email" name="email" :value="old('email')" required autofocus
                placeholder="votre@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit"
            class="w-full py-3 bg-blue-800 hover:bg-blue-900 text-white font-bold rounded-xl
                   transition duration-200 shadow-lg shadow-blue-200 text-sm">
            {{ __('Envoyer le lien de réinitialisation') }}
        </button>
    </form>

    <div class="text-center mt-6 space-y-3">
        <a href="{{ route('login') }}" class="inline-flex items-center space-x-1 text-sm text-gray-500 hover:text-blue-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>{{ __('Retour à la connexion') }}</span>
        </a>
    </div>
</x-guest-layout>
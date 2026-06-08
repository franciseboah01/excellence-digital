<x-guest-layout>
    <div class="mb-6 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center font-black text-white text-lg"
                style="background:linear-gradient(135deg,#1e3a8a,#2563eb)">
                EDC
            </div>
            <span class="font-bold text-blue-900 text-lg">Excellence Digital Center</span>
        </a>
        <p class="text-gray-500 text-sm mt-2">{{ __('Confirmation du mot de passe') }}</p>
    </div>

    <p class="text-sm text-gray-600 mb-6 text-center">
        {{ __('Ceci est une zone sécurisée. Veuillez confirmer votre mot de passe avant de continuer.') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Mot de passe')" class="text-gray-700 font-medium" />
            <x-text-input id="password" class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl"
                type="password" name="password" required autocomplete="current-password"
                placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit"
            class="w-full py-3 bg-blue-800 hover:bg-blue-900 text-white font-bold rounded-xl
                   transition duration-200 shadow-lg shadow-blue-200 text-sm">
            {{ __('Confirmer') }}
        </button>
    </form>
</x-guest-layout>
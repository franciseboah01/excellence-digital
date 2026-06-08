<x-guest-layout>
    <div class="mb-6 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center font-black text-white text-lg"
                style="background:linear-gradient(135deg,#1e3a8a,#2563eb)">
                EDC
            </div>
            <span class="font-bold text-blue-900 text-lg">Excellence Digital Center</span>
        </a>
        <p class="text-gray-500 text-sm mt-2">{{ __('Réinitialiser le mot de passe') }}</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Adresse email')" class="text-gray-700 font-medium" />
            <x-text-input id="email" class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl"
                type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username"
                placeholder="votre@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Nouveau mot de passe')" class="text-gray-700 font-medium" />
            <x-text-input id="password" class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl"
                type="password" name="password" required autocomplete="new-password"
                placeholder="Minimum 8 caractères" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="text-gray-700 font-medium" />
            <x-text-input id="password_confirmation" class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl"
                type="password" name="password_confirmation" required autocomplete="new-password"
                placeholder="Répétez le mot de passe" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit"
            class="w-full py-3 bg-blue-800 hover:bg-blue-900 text-white font-bold rounded-xl
                   transition duration-200 shadow-lg shadow-blue-200 text-sm">
            {{ __('Réinitialiser le mot de passe') }}
        </button>
    </form>
</x-guest-layout>
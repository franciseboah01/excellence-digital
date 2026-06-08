<x-guest-layout>
    <div class="mb-6 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center font-black text-white text-lg"
                style="background:linear-gradient(135deg,#1e3a8a,#2563eb)">
                EDC
            </div>
            <span class="font-bold text-blue-900 text-lg">Excellence Digital Center</span>
        </a>
        <p class="text-gray-500 text-sm mt-2">Créez votre compte gratuitement</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Prénom --}}
        <div>
            <x-input-label for="prenom" :value="__('Prénom')" class="text-gray-700 font-medium" />
            <x-text-input id="prenom" class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl"
                type="text" name="prenom" :value="old('prenom')" required autofocus
                placeholder="Votre prénom" />
            <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
        </div>

        {{-- Nom --}}
        <div>
            <x-input-label for="nom" :value="__('Nom')" class="text-gray-700 font-medium" />
            <x-text-input id="nom" class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl"
                type="text" name="nom" :value="old('nom')" required
                placeholder="Votre nom" />
            <x-input-error :messages="$errors->get('nom')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Adresse email')" class="text-gray-700 font-medium" />
            <x-text-input id="email" class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl"
                type="email" name="email" :value="old('email')" required
                placeholder="votre@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Téléphone --}}
        <div>
            <x-input-label for="telephone" :value="__('Téléphone (WhatsApp)')" class="text-gray-700 font-medium" />
            <x-text-input id="telephone" class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl"
                type="text" name="telephone" :value="old('telephone')"
                placeholder="+225 07 00 00 00 00" />
            <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
        </div>

        {{-- Choix Formation --}}
        <div>
            <x-input-label for="formation_id" :value="__('Choisir une formation (optionnel)')" class="text-gray-700 font-medium" />
            <select name="formation_id" id="formation_id"
                class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl shadow-sm text-gray-700">
                <option value="">-- Aucune formation --</option>
                @foreach($formations as $formation)
                    <option value="{{ $formation->id }}" {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                        {{ $formation->titre }} ({{ $formation->niveau }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('formation_id')" class="mt-2" />
        </div>

        {{-- Mot de passe --}}
        <div>
            <x-input-label for="password" :value="__('Mot de passe')" class="text-gray-700 font-medium" />
            <x-text-input id="password" class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl"
                type="password" name="password" required autocomplete="new-password"
                placeholder="Minimum 8 caractères" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirmation --}}
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="text-gray-700 font-medium" />
            <x-text-input id="password_confirmation" class="block mt-1.5 w-full border-gray-300 focus:border-blue-600 focus:ring-blue-500 rounded-xl"
                type="password" name="password_confirmation" required autocomplete="new-password"
                placeholder="Répétez le mot de passe" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Bouton --}}
        <button type="submit"
            class="w-full py-3 bg-blue-800 hover:bg-blue-900 text-white font-bold rounded-xl
                   transition duration-200 shadow-lg shadow-blue-200 text-sm mt-2">
            {{ __("S'inscrire") }}
        </button>
    </form>

    {{-- Lien connexion --}}
    <p class="text-center text-sm text-gray-500 mt-6">
        {{ __('Déjà un compte ?') }}
        <a href="{{ route('login') }}" class="text-blue-700 font-semibold hover:underline">
            {{ __('Se connecter') }}
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
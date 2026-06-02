<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Prénom --}}
        <div>
            <x-input-label for="prenom" :value="__('Prénom')" />
            <x-text-input id="prenom" class="block mt-1 w-full" type="text"
                name="prenom" :value="old('prenom')" required autofocus />
            <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
        </div>

        {{-- Nom --}}
        <div class="mt-4">
            <x-input-label for="nom" :value="__('Nom')" />
            <x-text-input id="nom" class="block mt-1 w-full" type="text"
                name="nom" :value="old('nom')" required />
            <x-input-error :messages="$errors->get('nom')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email"
                name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Téléphone --}}
        <div class="mt-4">
            <x-input-label for="telephone" :value="__('Téléphone (WhatsApp)')" />
            <x-text-input id="telephone" class="block mt-1 w-full" type="text"
                name="telephone" :value="old('telephone')" />
            <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
        </div>

        {{-- Choix Formation --}}
        <div class="mt-4">
            <x-input-label for="formation_id" :value="__('Choisir une formation (optionnel)')" />
            <select name="formation_id" id="formation_id"
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">-- Aucune formation --</option>
                @foreach($formations as $formation)
                    <option value="{{ $formation->id }}"
                        {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                        {{ $formation->titre }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('formation_id')" class="mt-2" />
        </div>

        {{-- Mot de passe --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password"
                name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirmation --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation"
                :value="__('Confirmer le mot de passe')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900"
                href="{{ route('login') }}">
                {{ __('Déjà inscrit ?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __("S'inscrire") }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
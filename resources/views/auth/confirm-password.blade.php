<x-guest-layout>
    <div class="mb-6 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2.5">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center font-black text-white text-lg"
                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                EDC
            </div>
            <span class="font-extrabold text-lg" style="color: #F1F5F9;">Excellence Digital Center</span>
        </a>
        <p class="text-sm mt-2" style="color: #94A3B8;">Confirmation du mot de passe</p>
    </div>

    <p class="text-sm text-center mb-6" style="color: #94A3B8;">
        Ceci est une zone sécurisée. Veuillez confirmer votre mot de passe avant de continuer.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <label class="edc-label" for="password">Mot de passe</label>
            <input id="password" type="password" name="password"
                class="edc-input" placeholder="••••••••" required autocomplete="current-password">
            @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary w-full">
            Confirmer
        </button>
    </form>
</x-guest-layout>
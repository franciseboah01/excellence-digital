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
        <p class="text-sm mt-2" style="color: #94A3B8;">Réinitialiser le mot de passe</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div>
            <label class="edc-label" for="email">Adresse email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                class="edc-input" placeholder="votre@email.com" required autofocus autocomplete="username">
            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="edc-label" for="password">Nouveau mot de passe</label>
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
        <button type="submit" class="btn-primary w-full">Réinitialiser le mot de passe</button>
    </form>
</x-guest-layout>
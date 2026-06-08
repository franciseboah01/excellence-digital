<x-guest-layout>
    <div class="mb-6 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center font-black text-white text-lg"
                style="background:linear-gradient(135deg,#1e3a8a,#2563eb)">
                EDC
            </div>
            <span class="font-bold text-blue-900 text-lg">Excellence Digital Center</span>
        </a>
        <p class="text-gray-500 text-sm mt-2">{{ __('Vérification de l\'email') }}</p>
    </div>

    <div class="text-center">
        <p class="text-sm text-gray-600 mb-4">
            {{ __("Merci de vous être inscrit ! Avant de commencer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.") }}
        </p>
        <p class="text-sm text-gray-500 mb-6">
            {{ __("Si vous n'avez pas reçu l'email, nous vous en renverrons un.") }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-xl p-3 text-sm font-medium">
                ✅ {{ __('Un nouveau lien de vérification a été envoyé à votre adresse email.') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="mb-6">
            @csrf
            <button type="submit"
                class="w-full py-3 bg-blue-800 hover:bg-blue-900 text-white font-bold rounded-xl
                       transition duration-200 shadow-lg shadow-blue-200 text-sm">
                {{ __('Renvoyer l\'email de vérification') }}
            </button>
        </form>

        <div class="space-y-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm text-gray-500 hover:text-red-600 transition underline">
                    {{ __('Se déconnecter') }}
                </button>
            </form>

            <a href="{{ route('home') }}" class="block text-sm text-gray-500 hover:text-blue-700 transition">
                ← {{ __('Retour à l\'accueil') }}
            </a>
        </div>
    </div>
</x-guest-layout>
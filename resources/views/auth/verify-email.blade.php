<x-guest-layout>
    <div class="mb-6 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2.5">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center font-black text-white text-lg"
                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                EDC
            </div>
            <span class="font-extrabold text-lg" style="color: #F1F5F9;">Excellence Digital Center</span>
        </a>
        <p class="text-sm mt-2" style="color: #94A3B8;">Vérification de l'email</p>
    </div>

    <div class="text-center">
        <p class="text-sm mb-4" style="color: #94A3B8;">
            Merci de vous être inscrit ! Avant de commencer, veuillez vérifier votre adresse email
            en cliquant sur le lien que nous venons de vous envoyer.
        </p>
        <p class="text-sm mb-6" style="color: #64748B;">
            Si vous n'avez pas reçu l'email, nous vous en renverrons un.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success mb-6">
                <span>✅</span>
                <span>Un nouveau lien de vérification a été envoyé à votre adresse email.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="mb-6">
            @csrf
            <button type="submit" class="btn-primary w-full">
                Renvoyer l'email de vérification
            </button>
        </form>

        <div class="space-y-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm transition hover:underline"
                    style="color: #64748B;" onmouseover="this.style.color='#F87171'" onmouseout="this.style.color='#64748B'">
                    Se déconnecter
                </button>
            </form>

            <a href="{{ route('home') }}" class="block text-sm transition"
                style="color: #64748B;" onmouseover="this.style.color='#60A5FA'" onmouseout="this.style.color='#64748B'">
                ← Retour à l'accueil
            </a>
        </div>
    </div>
</x-guest-layout>
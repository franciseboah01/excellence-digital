{{-- resources/views/errors/500.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur serveur — {{ \App\Models\Configuration::get('site_nom', 'Excellence Digital Center') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex items-center justify-center font-sans" style="background-color: #0B0F1A;">

    <div class="rounded-2xl shadow-2xl p-10 max-w-md w-full text-center" style="background-color: #111827; border: 1px solid #2A3552;">

        <div class="text-8xl mb-6">🔥</div>

        <h1 class="text-6xl font-extrabold mb-2" style="color: #EF4444;">500</h1>
        <h2 class="text-xl font-bold mb-4" style="color: #F1F5F9;">Erreur interne du serveur</h2>

        <p class="mb-6 leading-relaxed" style="color: #94A3B8;">
            Une erreur inattendue s'est produite. Nos équipes ont été notifiées.
            Veuillez réessayer dans quelques instants.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url()->previous() }}"
                class="px-6 py-3 rounded-xl font-semibold transition" 
                style="background-color: #1A2235; color: #94A3B8; border: 1px solid #2A3552;">
                ← Réessayer
            </a>
            <a href="{{ route('home') }}"
                class="btn-primary">
                🏠 Accueil
            </a>
        </div>

        <p class="text-xs mt-6" style="color: #475569;">
            {{ \App\Models\Configuration::get('site_nom', 'Excellence Digital Center') }}
        </p>
    </div>

</body>
</html>
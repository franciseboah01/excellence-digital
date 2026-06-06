<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable — Excellence Digital Center</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans">
    <div class="bg-white rounded-2xl shadow-xl p-10 max-w-md w-full text-center">

        <div class="text-8xl mb-6">🔍</div>

        <h1 class="text-4xl font-extrabold text-blue-800 mb-2">404</h1>
        <h2 class="text-xl font-bold text-gray-800 mb-4">Page introuvable</h2>

        <p class="text-gray-500 mb-6 leading-relaxed">
            La page que vous cherchez n'existe pas ou a été déplacée.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url()->previous() }}"
                class="bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-300 transition">
                ← Retour
            </a>
            <a href="{{ route('home') }}"
                class="bg-blue-800 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-900 transition">
                🏠 Accueil
            </a>
        </div>

        <p class="text-xs text-gray-300 mt-6">
            Excellence Digital Center — Korhogo / Sirasso
        </p>
    </div>
</body>
</html>
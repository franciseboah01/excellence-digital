<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Espace Enseignant — EDC')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <span class="text-2xl font-bold text-blue-800">EDC</span>
                    <span class="hidden md:block text-xs text-gray-400">Espace Enseignant</span>
                </a>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('enseignant.dashboard') }}"
                        class="text-gray-600 hover:text-blue-700 font-medium text-sm transition">
                        🏠 Dashboard
                    </a>
                    <a href="{{ route('enseignant.ressources.index') }}"
                        class="text-gray-600 hover:text-blue-700 font-medium text-sm transition">
                        📚 Mes ressources
                    </a>
                    <a href="{{ route('enseignant.notifications.form') }}"
                        class="text-gray-600 hover:text-blue-700 font-medium text-sm transition">
                        🔔 Notifications
                    </a>
                </div>

                <div class="flex items-center space-x-4" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                        <div class="w-9 h-9 rounded-full bg-green-700 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                        </div>
                        <span class="hidden md:block text-sm font-medium text-gray-700">
                            {{ auth()->user()->prenom }}
                        </span>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-4 top-14 w-48 bg-white rounded-xl shadow-lg border py-2 z-50">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                🚪 Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl p-4 mb-6 font-medium">
            ✅ {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 rounded-xl p-4 mb-6 font-medium">
            ❌ {{ session('error') }}
        </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
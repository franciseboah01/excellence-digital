<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Excellence Digital Center - Services bureautiques, formations et solutions digitales à Korhogo">
    <title>@yield('title', 'Excellence Digital Center')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800 font-sans">

    {{-- NAVBAR --}}
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <span class="text-2xl font-bold text-blue-800">EDC</span>
                    <span class="hidden md:block text-sm text-gray-500 font-medium">Excellence Digital Center</span>
                </a>

                {{-- Menu --}}
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-700 font-medium transition">Accueil</a>
                    <a href="{{ route('services.index') }}" class="text-gray-700 hover:text-blue-700 font-medium transition">Services</a>
                    <a href="{{ route('formations.index') }}" class="text-gray-700 hover:text-blue-700 font-medium transition">Formations</a>
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-blue-700 font-medium transition">Contact</a>
                    <a href="{{ route('blog.index') }}" class="text-gray-700 hover:text-blue-700 font-medium transition">Blog</a>
                    <a href="{{ route('faq') }}" class="text-gray-700 hover:text-blue-700 font-medium transition">FAQ</a>
                </div>

                {{-- BARRE DE RECHERCHE --}}
                <div class="hidden md:flex items-center relative" x-data="{ resultats: [], query: '' }">
                    <form action="{{ route('recherche') }}" method="GET" class="flex">
                        <input type="text" name="q"
                            x-model="query"
                            @input.debounce.300ms="
                                if(query.length >= 2) {
                                    fetch('/recherche/autocomplete?q=' + encodeURIComponent(query))
                                        .then(r => r.json())
                                        .then(data => resultats = data);
                                } else { resultats = []; }
                            "
                            @click.away="resultats = []"
                            placeholder="🔍 Rechercher..."
                            class="border border-gray-300 rounded-l-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-48">
                        <button type="submit"
                            class="bg-blue-800 text-white px-4 py-2 rounded-r-lg hover:bg-blue-900 transition text-sm">
                            🔍
                        </button>
                    </form>

                    {{-- Suggestions autocomplete --}}
                    <div x-show="resultats.length > 0"
                        class="absolute top-full left-0 w-64 bg-white rounded-xl shadow-xl border border-gray-100 z-50 mt-1 overflow-hidden">
                        <template x-for="r in resultats" :key="r.url">
                            <a :href="r.url"
                                class="flex items-center space-x-2 px-4 py-3 hover:bg-blue-50 transition border-b border-gray-100 last:border-0">
                                <span x-text="r.type === 'service' ? '💼' : r.type === 'formation' ? '🎓' : '📰'"></span>
                                <div>
                                    <p class="text-sm font-medium text-gray-800" x-text="r.label"></p>
                                    <p class="text-xs text-gray-400 capitalize" x-text="r.type"></p>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>

                {{-- Auth Buttons --}}
                <div class="flex items-center space-x-3">
                    @auth
                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.dashboard') }}" class="bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
                                Mon espace
                            </a>
                        @elseif(auth()->user()->hasRole('enseignant'))
                            <a href="{{ route('enseignant.dashboard') }}" class="bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
                                Mon espace
                            </a>
                        @else
                            <a href="{{ route('client.dashboard') }}" class="bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
                                Mon espace
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-blue-800 font-medium hover:underline text-sm">Connexion</a>
                        <a href="{{ route('register') }}" class="bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
                            S'inscrire
                        </a>
                    @endauth
                </div>

                {{-- Menu Mobile --}}
                <button id="menuToggle" class="md:hidden text-gray-700 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            {{-- Menu Mobile Dropdown --}}
            <div id="mobileMenu" class="hidden md:hidden pb-4 space-y-2">
                <a href="{{ route('home') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 rounded">Accueil</a>
                <a href="{{ route('services.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 rounded">Services</a>
                <a href="{{ route('formations.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 rounded">Formations</a>
                <a href="{{ route('contact') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 rounded">Contact</a>
                @guest
                    <a href="{{ route('login') }}" class="block px-4 py-2 text-blue-800 font-medium">Connexion</a>
                    <a href="{{ route('register') }}" class="block px-4 py-2 bg-blue-800 text-white rounded text-center">S'inscrire</a>
                @endguest
            </div>
        </div>
    </nav>

    {{-- CONTENU --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-black text-white">
        <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-3">EDC</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Excellence Digital Center — votre partenaire en bureautique, digital et formation à Korhogo / Sirasso.
                </p>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-3">Liens rapides</h3>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition">Accueil</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-white transition">Services</a></li>
                    <li><a href="{{ route('formations.index') }}" class="hover:text-white transition">Formations</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white transition">Contact</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-3">Contact</h3>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li>📍 Korhogo / Sirasso</li>
                    <li>
                        📲
                        <a href="https://wa.me/22507000000" 
                            class="hover:text-white transition" target="_blank">
                            +225 07 00 00 00 
                        </a>
                    </li>
                    <li>✉️ contact@excellencedigital.ci</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 text-center py-4 text-gray-500 text-sm">
            © {{ date('Y') }} Excellence Digital Center — Former • Créer • Réussir 🚀
        </div>
    </footer>

    {{-- Script menu mobile --}}
    <script>
        document.getElementById('menuToggle').addEventListener('click', function () {
            document.getElementById('mobileMenu').classList.toggle('hidden');
        });
    </script>

    @stack('scripts')
</body>
</html>
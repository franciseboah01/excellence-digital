<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Excellence Digital Center — Services bureautiques, formations et solutions digitales à Korhogo">
    <meta name="theme-color" content="#1e3a8a">
    <title>@yield('title', 'Excellence Digital Center')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800 font-sans">

    {{-- NAVBAR MOBILE-FIRST --}}
    <nav class="bg-white shadow-md sticky top-0 z-50" x-data="{ menuOpen: false }">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-14">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-white text-sm"
                        style="background:linear-gradient(135deg,#1e3a8a,#2563eb)">
                        EDC
                    </div>
                    <span class="font-bold text-blue-900 text-sm hidden sm:block">
                        Excellence Digital Center
                    </span>
                    <span class="font-bold text-blue-900 text-sm sm:hidden">EDC</span>
                </a>

                {{-- Nav desktop --}}
                <div class="hidden md:flex items-center space-x-5">
                    <a href="{{ route('home') }}"
                        class="text-gray-700 hover:text-blue-700 font-medium transition text-sm
                        {{ request()->routeIs('home') ? 'text-blue-700' : '' }}">
                        Accueil
                    </a>
                    <a href="{{ route('services.index') }}"
                        class="text-gray-700 hover:text-blue-700 font-medium transition text-sm">
                        Services
                    </a>
                    <a href="{{ route('formations.index') }}"
                        class="text-gray-700 hover:text-blue-700 font-medium transition text-sm">
                        Formations
                    </a>
                    <a href="{{ route('blog.index') }}"
                        class="text-gray-700 hover:text-blue-700 font-medium transition text-sm">
                        Blog
                    </a>
                    <a href="{{ route('faq') }}"
                        class="text-gray-700 hover:text-blue-700 font-medium transition text-sm">
                        FAQ
                    </a>
                    <a href="{{ route('contact') }}"
                        class="text-gray-700 hover:text-blue-700 font-medium transition text-sm">
                        Contact
                    </a>
                </div>

                {{-- Boutons auth --}}
                <div class="hidden md:flex items-center space-x-2">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="bg-blue-800 text-white px-4 py-2 rounded-lg text-xs font-semibold
                                   hover:bg-blue-900 transition">
                            Mon espace →
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-blue-800 font-medium text-sm hover:underline">
                            Connexion
                        </a>
                        <a href="{{ route('register') }}"
                            class="bg-blue-800 text-white px-4 py-2 rounded-lg text-xs font-semibold
                                   hover:bg-blue-900 transition">
                            S'inscrire
                        </a>
                    @endauth
                </div>

                {{-- Burger mobile --}}
                <button @click="menuOpen = !menuOpen"
                    class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition"
                    aria-label="Menu">
                    <svg x-show="!menuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="menuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Menu mobile déroulant --}}
            <div x-show="menuOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-end="opacity-0 -translate-y-2"
                @click.away="menuOpen = false"
                class="md:hidden border-t border-gray-100 py-3 space-y-1">

                @foreach([
                    ['home', 'Accueil', '🏠'],
                    ['services.index', 'Services', '💼'],
                    ['formations.index', 'Formations', '🎓'],
                    ['blog.index', 'Blog', '📰'],
                    ['faq', 'FAQ', '❓'],
                    ['contact', 'Contact', '📩'],
                ] as $item)
                <a href="{{ route($item[0]) }}"
                    @click="menuOpen = false"
                    class="flex items-center space-x-3 px-4 py-3 rounded-xl
                           hover:bg-blue-50 transition text-gray-700 font-medium">
                    <span>{{ $item[2] }}</span>
                    <span>{{ $item[1] }}</span>
                </a>
                @endforeach

                <div class="border-t border-gray-100 pt-3 mt-2 px-4 space-y-2">
                    @auth
                    <a href="{{ route('dashboard') }}"
                        class="block w-full text-center bg-blue-800 text-white py-3
                               rounded-xl font-semibold hover:bg-blue-900 transition">
                        Mon espace →
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                        class="block w-full text-center border-2 border-blue-800 text-blue-800
                               py-3 rounded-xl font-semibold hover:bg-blue-50 transition">
                        Connexion
                    </a>
                    <a href="{{ route('register') }}"
                        class="block w-full text-center bg-blue-800 text-white py-3
                               rounded-xl font-semibold hover:bg-blue-900 transition">
                        S'inscrire gratuitement
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Contenu principal --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="text-white mt-0" style="background:#1e3a8a;">
        <div class="max-w-7xl mx-auto px-4 py-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">

                {{-- À propos --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center space-x-2 mb-3">
                        <div class="w-9 h-9 rounded-lg bg-white bg-opacity-20 flex items-center
                                    justify-center font-black text-sm">EDC</div>
                        <span class="font-bold">Excellence Digital Center</span>
                    </div>
                    <p class="text-blue-200 text-sm leading-relaxed">
                        Votre partenaire en bureautique, digital et formation à Korhogo / Sirasso.
                    </p>
                </div>

                {{-- Liens rapides --}}
                <div>
                    <h3 class="font-bold mb-3 text-sm uppercase tracking-wider">Navigation</h3>
                    <ul class="space-y-2 text-blue-200 text-sm">
                        @foreach([
                            ['home', 'Accueil'],
                            ['services.index', 'Services'],
                            ['formations.index', 'Formations'],
                            ['blog.index', 'Blog'],
                            ['faq', 'FAQ'],
                        ] as $lien)
                        <li>
                            <a href="{{ route($lien[0]) }}"
                                class="hover:text-white transition">{{ $lien[1] }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Services --}}
                <div>
                    <h3 class="font-bold mb-3 text-sm uppercase tracking-wider">Services</h3>
                    <ul class="space-y-2 text-blue-200 text-sm">
                        @foreach(['Création de CV', 'Mise en page Word', 'Logo & Design', 'Site Web', 'Formation Excel'] as $s)
                        <li>
                            <a href="{{ route('demande.form') }}"
                                class="hover:text-white transition">{{ $s }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h3 class="font-bold mb-3 text-sm uppercase tracking-wider">Contact</h3>
                    <ul class="space-y-3 text-blue-200 text-sm">
                        <li class="flex items-start space-x-2">
                            <span class="flex-shrink-0">📍</span>
                            <span>Korhogo / Sirasso, Côte d'Ivoire</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="flex-shrink-0">📲</span>
                            <a href="https://wa.me/2250748746140"
                                class="hover:text-white transition">+225 07 48 74 61 40</a>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="flex-shrink-0">✉️</span>
                            <span>contact@excellencedigital.ci</span>
                        </li>
                    </ul>
                    <a href="https://wa.me/2250748746140" target="_blank"
                        class="inline-flex items-center space-x-2 mt-4 bg-green-500
                               text-white px-4 py-2 rounded-xl text-sm font-semibold
                               hover:bg-green-600 transition">
                        <span>💬</span><span>WhatsApp</span>
                    </a>
                </div>
            </div>

            <div class="border-t border-blue-800 pt-6 flex flex-col sm:flex-row
                        justify-between items-center gap-3">
                <p class="text-blue-300 text-xs text-center sm:text-left">
                    © {{ date('Y') }} Excellence Digital Center — Tous droits réservés
                </p>
                <p class="text-blue-300 text-xs">Former • Créer • Réussir 🚀</p>
            </div>
        </div>

        {{-- Bouton WhatsApp flottant --}}
        <a href="https://wa.me/2250748746140" target="_blank"
            class="fixed bottom-5 right-5 z-50 w-14 h-14 bg-green-500 text-white
                   rounded-full shadow-lg flex items-center justify-center text-2xl
                   hover:bg-green-600 hover:scale-110 transition-all duration-200"
            title="Contacter sur WhatsApp">
            💬
        </a>
    </footer>

    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
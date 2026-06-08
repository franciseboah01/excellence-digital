<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Excellence Digital Center — Services bureautiques, formations et solutions digitales à Korhogo. Votre centre digital de référence.">
    <meta name="theme-color" content="#0B0F1A">
    <title>@yield('title', 'Excellence Digital Center')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased"
      style="background-color: #0B0F1A; color: #F1F5F9;">

    {{-- ========== NAVBAR ========== --}}
    <nav class="edc-navbar sticky top-0 z-50"
         x-data="{ menuOpen: false, searchOpen: false, resultats: [], query: '' }">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">

                {{-- LOGO --}}
                <a href="{{ route('home') }}" class="flex items-center space-x-2.5 flex-shrink-0">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center font-black text-white text-sm"
                         style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                        EDC
                    </div>
                    <span class="font-extrabold text-sm hidden sm:block"
                          style="color: #F1F5F9;">
                        Excellence Digital Center
                    </span>
                </a>

                {{-- LIENS DESKTOP --}}
                <div class="hidden lg:flex items-center space-x-1">
                    @foreach([
                        ['home', 'Accueil'],
                        ['services.index', 'Services'],
                        ['formations.index', 'Formations'],
                        ['blog.index', 'Blog'],
                        ['faq', 'FAQ'],
                        ['contact', 'Contact'],
                    ] as $item)
                    <a href="{{ route($item[0]) }}"
                       class="nav-link {{ request()->routeIs($item[0]) ? 'active' : '' }}">
                        {{ $item[1] }}
                    </a>
                    @endforeach
                </div>

                {{-- RECHERCHE + ACTIONS DESKTOP --}}
                <div class="hidden lg:flex items-center space-x-3 flex-shrink-0">

                    {{-- BARRE DE RECHERCHE DESKTOP --}}
                    <div class="relative">
                        <form action="{{ route('recherche') }}" method="GET" class="flex items-center">
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
                                placeholder="Rechercher..."
                                class="w-44 px-4 py-2 text-sm rounded-xl transition-all duration-200
                                       focus:outline-none focus:ring-2 focus:ring-blue-500"
                                style="background-color: #151B2B; border: 1px solid #2A3552; color: #F1F5F9;"
                                onfocus="this.style.borderColor='#3B82F6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.20)'"
                                onblur="this.style.borderColor='#2A3552'; this.style.boxShadow='none'">
                            <button type="submit"
                                class="ml-2 w-9 h-9 rounded-lg flex items-center justify-center transition
                                       hover:bg-blue-600/20"
                                style="color: #64748B;" title="Rechercher">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </form>
                        {{-- Suggestions autocomplete --}}
                        <div x-show="resultats.length > 0"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute top-full right-0 w-72 mt-2 rounded-2xl shadow-2xl overflow-hidden z-50"
                            style="background-color: #1A2235; border: 1px solid #2A3552;">
                            <template x-for="r in resultats" :key="r.url">
                                <a :href="r.url"
                                    class="flex items-center space-x-3 px-4 py-3.5 transition border-b last:border-0"
                                    style="border-color: #2A3552;"
                                    onmouseover="this.style.backgroundColor='rgba(59,130,246,0.08)'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                    <span class="text-lg" x-text="r.type === 'service' ? '💼' : r.type === 'formation' ? '🎓' : '📰'"></span>
                                    <div>
                                        <p class="text-sm font-semibold" style="color: #F1F5F9;" x-text="r.label"></p>
                                        <p class="text-xs capitalize" style="color: #64748B;" x-text="r.type"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>

                    {{-- BOUTONS AUTH --}}
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary btn-sm">
                            <span>Mon espace</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-sm font-semibold transition"
                           style="color: #94A3B8;"
                           onmouseover="this.style.color='#60A5FA'"
                           onmouseout="this.style.color='#94A3B8'">
                            Connexion
                        </a>
                        <a href="{{ route('register') }}" class="btn-primary btn-sm">
                            S'inscrire
                        </a>
                    @endauth
                </div>

                {{-- BURGER + RECHERCHE MOBILE --}}
                <div class="flex lg:hidden items-center space-x-2">
                    {{-- Bouton recherche mobile --}}
                    <button @click="searchOpen = !searchOpen; menuOpen = false"
                        class="p-2 rounded-lg transition"
                        style="color: #94A3B8;"
                        :style="searchOpen ? 'background-color: rgba(59,130,246,0.12); color: #60A5FA;' : ''">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    {{-- Burger --}}
                    <button @click="menuOpen = !menuOpen; searchOpen = false"
                        class="p-2 rounded-lg transition"
                        style="color: #94A3B8;"
                        :style="menuOpen ? 'background-color: rgba(59,130,246,0.12); color: #60A5FA;' : ''">
                        <svg x-show="!menuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="menuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- RECHERCHE MOBILE --}}
            <div x-show="searchOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-end="opacity-0 -translate-y-2"
                @click.away="searchOpen = false"
                class="lg:hidden pb-3">
                <form action="{{ route('recherche') }}" method="GET" class="flex items-center gap-2">
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
                        placeholder="🔍 Rechercher un service, une formation..."
                        class="flex-1 px-4 py-3 text-sm rounded-xl transition"
                        style="background-color: #151B2B; border: 1px solid #2A3552; color: #F1F5F9;"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.20)'"
                        onblur="this.style.borderColor='#2A3552'; this.style.boxShadow='none'">
                    <button type="submit"
                        class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                        style="background: linear-gradient(135deg, #3B82F6, #1D4ED8); color: #fff;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
                {{-- Suggestions mobile --}}
                <div x-show="resultats.length > 0"
                    class="mt-2 rounded-xl shadow-xl overflow-hidden z-50"
                    style="background-color: #1A2235; border: 1px solid #2A3552;">
                    <template x-for="r in resultats" :key="r.url">
                        <a :href="r.url"
                            class="flex items-center space-x-3 px-4 py-3 transition border-b last:border-0"
                            style="border-color: #2A3552;"
                            onmouseover="this.style.backgroundColor='rgba(59,130,246,0.08)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <span class="text-lg" x-text="r.type === 'service' ? '💼' : r.type === 'formation' ? '🎓' : '📰'"></span>
                            <div>
                                <p class="text-sm font-semibold" style="color: #F1F5F9;" x-text="r.label"></p>
                                <p class="text-xs capitalize" style="color: #64748B;" x-text="r.type"></p>
                            </div>
                        </a>
                    </template>
                </div>
            </div>

            {{-- MENU MOBILE --}}
            <div x-show="menuOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 @click.away="menuOpen = false"
                 class="lg:hidden pb-4 space-y-1"
                 style="border-top: 1px solid #2A3552;">

                @foreach([
                    ['home', 'Accueil'],
                    ['services.index', 'Services'],
                    ['formations.index', 'Formations'],
                    ['blog.index', 'Blog'],
                    ['faq', 'FAQ'],
                    ['contact', 'Contact'],
                ] as $item)
                <a href="{{ route($item[0]) }}"
                   @click="menuOpen = false"
                   class="block px-4 py-3 rounded-xl text-sm font-semibold transition
                          {{ request()->routeIs($item[0])
                              ? 'bg-blue-500/10 text-blue-400'
                              : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' }}">
                    {{ $item[1] }}
                </a>
                @endforeach

                <div class="pt-4 mt-2 px-4 space-y-3"
                     style="border-top: 1px solid #2A3552;">
                    @auth
                    <a href="{{ route('dashboard') }}"
                       class="block w-full text-center btn-primary py-3.5">
                        Mon espace →
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                       class="block w-full text-center py-3.5 rounded-xl font-bold text-sm transition"
                       style="border: 1.5px solid #3B82F6; color: #60A5FA;"
                       onmouseover="this.style.backgroundColor='rgba(59,130,246,0.08)'"
                       onmouseout="this.style.backgroundColor='transparent'">
                        Connexion
                    </a>
                    <a href="{{ route('register') }}"
                       class="block w-full text-center btn-primary py-3.5">
                        S'inscrire gratuitement
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- CONTENU --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="edc-footer">
        <div class="max-w-5xl mx-auto px-4 py-14">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center space-x-2 mb-5">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center font-black text-white text-sm"
                             style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                            EDC
                        </div>
                        <span class="font-extrabold text-sm" style="color: #F1F5F9;">
                            Excellence Digital Center
                        </span>
                    </div>
                    <p class="text-sm leading-relaxed" style="color: #64748B;">
                        Services bureautiques, digital et formation à Korhogo / Sirasso.
                        Votre centre de référence pour réussir dans l'univers digital.
                    </p>
                </div>

                <div>
                    <h3>Navigation</h3>
                    <ul class="space-y-3 text-sm">
                        @foreach([['home','Accueil'],['services.index','Services'],['formations.index','Formations'],['blog.index','Blog'],['faq','FAQ'],['contact','Contact']] as $l)
                        <li><a href="{{ route($l[0]) }}">{{ $l[1] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h3>Services</h3>
                    <ul class="space-y-3 text-sm">
                        @foreach(['Création de CV','Mise en page Word','Logo & Design','Site Web','Formation Excel'] as $s)
                        <li><a href="{{ route('demande.form') }}">{{ $s }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h3>Contact</h3>
                    <ul class="space-y-3 text-sm" style="color: #64748B;">
                        <li class="flex items-start space-x-2">
                            <span>📍</span>
                            <span>Korhogo / Sirasso,<br>Côte d'Ivoire</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <span>📲</span>
                            <a href="https://wa.me/2250748746140" class="hover:text-green-400 transition">
                                +225 07 48 74 61 40
                            </a>
                        </li>
                        <li class="flex items-center space-x-2">
                            <span>✉️</span>
                            <span>contact@excellencedigital.ci</span>
                        </li>
                    </ul>
                    <a href="https://wa.me/2250748746140" target="_blank"
                       class="inline-flex items-center space-x-2 mt-5 px-5 py-2.5 rounded-xl text-sm font-bold transition"
                       style="background: linear-gradient(135deg, #25D366, #128C7E); color: #ffffff;"
                       onmouseover="this.style.boxShadow='0 4px 16px rgba(37,211,102,0.30)'"
                       onmouseout="this.style.boxShadow='none'">
                        <span>💬</span>
                        <span>WhatsApp</span>
                    </a>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row justify-between items-center gap-3"
                 style="border-top: 1px solid #1A2235;">
                <p class="text-xs" style="color: #475569;">
                    © {{ date('Y') }} Excellence Digital Center — Tous droits réservés
                </p>
                <p class="text-xs font-medium" style="color: #3B82F6;">
                    Former • Créer • Réussir
                </p>
            </div>
        </div>
    </footer>

    {{-- WHATSAPP FLOTTANT --}}
    <a href="https://wa.me/2250748746140" target="_blank"
       class="whatsapp-float animate-bounce-slow"
       title="Nous contacter sur WhatsApp">
        💬
    </a>

    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
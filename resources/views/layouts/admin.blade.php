<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration — EDC')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-sans" x-data="{ sidebarOpen: false }">

    {{-- OVERLAY mobile --}}
    <div x-show="sidebarOpen"
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden">
    </div>

    {{-- SIDEBAR --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 w-64 bg-blue-900 text-white z-40
               transform transition-transform duration-300 ease-in-out
               lg:translate-x-0 flex flex-col shadow-2xl">

        {{-- Logo --}}
        <div class="px-5 py-4 border-b border-blue-800 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-extrabold tracking-wide">EDC Admin</h1>
                <p class="text-blue-300 text-xs mt-0.5">Excellence Digital Center</p>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-blue-300 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-0.5">

            @php
            $navItems = [
                ['route' => 'admin.dashboard', 'icon' => '🏠', 'label' => 'Dashboard', 'group' => null],
                ['route' => 'admin.users.index', 'icon' => '👥', 'label' => 'Clients', 'group' => 'Gestion'],
                ['route' => 'admin.enseignants.index', 'icon' => '👨‍🏫', 'label' => 'Enseignants', 'group' => null],
                ['route' => 'admin.services.index', 'icon' => '💼', 'label' => 'Services', 'group' => null],
                ['route' => 'admin.demandes.index', 'icon' => '📋', 'label' => 'Demandes', 'group' => null],
                ['route' => 'admin.formations.index', 'icon' => '🎓', 'label' => 'Formations', 'group' => null],
                ['route' => 'admin.paiements.index', 'icon' => '💰', 'label' => 'Paiements', 'group' => null],
                ['route' => 'admin.qcms.index', 'icon' => '📝', 'label' => 'QCMs', 'group' => null],
                ['route' => 'admin.certificats.index', 'icon' => '🏆', 'label' => 'Certificats', 'group' => null],
                ['route' => 'admin.notifications.form', 'icon' => '🔔', 'label' => 'Notifications', 'group' => 'Communication'],
                ['route' => 'admin.emails.form', 'icon' => '✉️', 'label' => 'Emails', 'group' => null],
                ['route' => 'messages.index', 'icon' => '💬', 'label' => 'Messages', 'group' => null],
                ['route' => 'admin.temoignages.index', 'icon' => '⭐', 'label' => 'Témoignages', 'group' => null],
                ['route' => 'admin.articles.index', 'icon' => '📰', 'label' => 'Blog', 'group' => 'Contenu'],
                ['route' => 'admin.faqs.index', 'icon' => '❓', 'label' => 'FAQ', 'group' => null],
                ['route' => 'admin.configurations.index', 'icon' => '⚙️', 'label' => 'Config', 'group' => null],
            ];
            $lastGroup = null;
            @endphp

            @foreach($navItems as $item)
                @if($item['group'] && $item['group'] !== $lastGroup)
                    @php $lastGroup = $item['group']; @endphp
                    <p class="text-blue-400 text-xs font-semibold uppercase tracking-wider
                               px-3 pt-4 pb-1">
                        {{ $item['group'] }}
                    </p>
                @endif
                <a href="{{ route($item['route']) }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm
                           font-medium transition
                           {{ request()->routeIs($item['route']) || request()->routeIs(str_replace('index','*',$item['route']))
                               ? 'bg-blue-700 text-white'
                               : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}">
                    <span class="text-base w-5 text-center">{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                    @if($item['route'] === 'admin.demandes.index')
                        @php $n = \App\Models\DemandeService::where('statut','en_attente')->count(); @endphp
                        @if($n > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 leading-none">
                            {{ $n }}
                        </span>
                        @endif
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Profil --}}
        <div class="px-4 py-3 border-t border-blue-800">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center
                            justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-white truncate">
                        {{ auth()->user()->nom_complet }}
                    </p>
                    <p class="text-xs text-blue-300">Administrateur</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Déconnexion"
                        class="text-blue-300 hover:text-red-400 transition">🚪</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- CONTENU PRINCIPAL --}}
    <div class="lg:ml-64 flex flex-col min-h-screen">

        {{-- TOPBAR --}}
        <header class="bg-white shadow-sm px-4 py-3 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center space-x-3">
                {{-- Burger --}}
                <button @click="sidebarOpen = true"
                    class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h2 class="text-sm font-bold text-gray-800 leading-tight">
                        @yield('page_title', 'Dashboard')
                    </h2>
                    <p class="text-xs text-gray-400 hidden sm:block">
                        @yield('page_subtitle', 'Vue d\'ensemble')
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs text-gray-400 hidden sm:block">
                    {{ now()->format('d/m/Y') }}
                </span>
                <a href="{{ route('home') }}" target="_blank"
                    class="text-xs bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg
                           hover:bg-blue-200 transition font-medium">
                    🌐 Site
                </a>
            </div>
        </header>

        {{-- FLASH MESSAGES --}}
        <div class="px-4 pt-4 space-y-2">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl p-3 text-sm font-medium flex items-center space-x-2">
                <span>✅</span><span>{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-800 rounded-xl p-3 text-sm font-medium flex items-center space-x-2">
                <span>❌</span><span>{{ session('error') }}</span>
            </div>
            @endif
        </div>

        {{-- CONTENU PAGE --}}
        <main class="flex-1 px-4 pb-8 overflow-x-hidden">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
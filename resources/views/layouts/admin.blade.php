<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Administration — EDC')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <aside class="w-64 bg-blue-900 text-white flex flex-col fixed h-full z-40 shadow-xl">

            {{-- Logo --}}
            <div class="px-6 py-5 border-b border-blue-800">
                <h1 class="text-xl font-extrabold tracking-wide">EDC Admin</h1>
                <p class="text-blue-300 text-xs mt-0.5">Excellence Digital Center</p>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">

                <p class="text-blue-400 text-xs font-semibold uppercase tracking-wider mb-2 px-2">
                    Principal
                </p>

                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <span>🏠</span><span>Dashboard</span>
                </a>

                <p class="text-blue-400 text-xs font-semibold uppercase tracking-wider mt-5 mb-2 px-2">
                    Gestion
                </p>

                <a href="{{ route('admin.enseignants.index') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('admin.enseignants.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <span>👨‍🏫</span><span>Enseignants</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('admin.users.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <span>👥</span><span>Utilisateurs</span>
                </a>

                <a href="{{ route('admin.services.index') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('admin.services.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <span>💼</span><span>Services</span>
                </a>

                <a href="{{ route('admin.demandes.index') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('admin.demandes.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <span>📋</span><span>Demandes</span>
                    @php $demandesEnAttente = \App\Models\DemandeService::where('statut','en_attente')->count(); @endphp
                    @if($demandesEnAttente > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">
                        {{ $demandesEnAttente }}
                    </span>
                    @endif
                </a>

                <a href="{{ route('admin.formations.index') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('admin.formations.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <span>🎓</span><span>Formations</span>
                </a>

                <p class="text-blue-400 text-xs font-semibold uppercase tracking-wider mt-5 mb-2 px-2">
                    Communication
                </p>

                <a href="{{ route('admin.notifications.form') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('admin.notifications.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <span>🔔</span><span>Notifications</span>
                </a>

                {{-- Message --}}
                <a href="{{ route('messages.index') }}"
                    class="relative flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('messages.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    <span>Messages</span>
                    @php
                        $msgCount = \App\Models\Message::where('destinataire_id', auth()->id())
                            ->where('lu', false)
                            ->count();
                    @endphp
                    @if($msgCount > 0)
                        <span class="msg-badge absolute top-2 right-2 bg-green-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                            {{ $msgCount > 9 ? '9+' : $msgCount }}
                        </span>
                    @endif
                </a>

                {{-- Mail --}}
                <a href="{{ route('admin.emails.form') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('admin.emails.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <span>✉️</span><span>Emails</span>
                </a>

                {{-- Temoignage --}}
                <a href="{{ route('admin.temoignages.index') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('admin.temoignages.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <span>⭐</span><span>Témoignages</span>
                    @if($stats['en_attente'] ?? 0 > 0)
                    <span class="ml-auto bg-yellow-500 text-white text-xs rounded-full px-2 py-0.5">
                        {{ $stats['en_attente'] }}
                    </span>
                    @endif
                </a>

                {{-- Après Emails, dans section Communication --}}
                <a href="{{ route('admin.articles.index') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('admin.articles.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <span>📰</span><span>Blog</span>
                </a>

                <a href="{{ route('admin.faqs.index') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('admin.faqs.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <span>❓</span><span>FAQ</span>
                </a>

                <a href="{{ route('admin.paiements.index') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('admin.paiements.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <span>💰</span><span>Paiements</span>
                </a>
                <a href="{{ route('admin.configurations.index') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                    {{ request()->routeIs('admin.configurations.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}
                    transition text-sm font-medium">
                    <span>⚙️</span><span>Configurations</span>
                </a>
            </nav>
        
            {{-- Profil Admin --}}
            <div class="px-4 py-4 border-t border-blue-800">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">
                            {{ auth()->user()->nom_complet }}
                        </p>
                        <p class="text-xs text-blue-300">Administrateur</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Déconnexion"
                            class="text-blue-300 hover:text-red-400 transition text-lg">
                            🚪
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- CONTENU PRINCIPAL --}}
        <div class="flex-1 ml-64 flex flex-col overflow-hidden">

            {{-- TOPBAR --}}
            <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">@yield('page_title', 'Dashboard')</h2>
                    <p class="text-xs text-gray-400">@yield('page_subtitle', 'Vue d\'ensemble')</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-400">
                        {{ now()->format('d/m/Y') }}
                    </span>
                    <a href="{{ route('home') }}" target="_blank"
                        class="text-xs bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-200 transition font-medium">
                        🌐 Voir le site
                    </a>
                </div>
            </header>

            {{-- MESSAGES FLASH --}}
            <div class="px-6 pt-4">
                @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl p-4 mb-4 font-medium">
                    ✅ {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-800 rounded-xl p-4 mb-4 font-medium">
                    ❌ {{ session('error') }}
                </div>
                @endif
            </div>

            {{-- PAGE CONTENT --}}
            <main class="flex-1 overflow-y-auto px-6 pb-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
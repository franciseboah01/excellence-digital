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
<body class="font-sans antialiased" style="background-color: var(--edc-bg-deep); color: var(--edc-text-primary);" x-data="{ sidebarOpen: false }">

    {{-- OVERLAY mobile --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
        class="fixed inset-0 z-30 lg:hidden"
        style="background-color: rgba(0,0,0,0.6);">
    </div>

    {{-- SIDEBAR --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 w-64 z-40 transform transition-transform duration-300 ease-in-out
               lg:translate-x-0 flex flex-col shadow-2xl"
        style="background-color: #0c1a3a;">

        {{-- Logo --}}
        <div class="px-5 py-4 flex items-center justify-between" style="border-bottom: 1px solid rgba(59,130,246,0.15);">
            <div>
                <h1 class="text-lg font-extrabold tracking-wide" style="color: #fff;">EDC Admin</h1>
                <p class="text-xs mt-0.5" style="color: #60A5FA;">Excellence Digital Center</p>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden hover:text-white" style="color: #60A5FA;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-0.5">

            @php
            $compteurs = [
                'admin.demandes.index'      => \App\Models\DemandeService::where('statut','en_attente')->count(),
                'admin.temoignages.index'   => \App\Models\Temoignage::where('statut_validation','en_attente')->count(),
                'admin.users.index'         => \App\Models\User::role('client')->where('statut','en_attente')->count(),
                'messages.index'            => \App\Models\Message::where('destinataire_id', auth()->id())->where('lu', false)->count(),
            ];

            $navItems = [
                ['route' => 'admin.dashboard', 'icon' => '🏠', 'label' => 'Dashboard', 'group' => null, 'compteur' => null],
                ['route' => 'admin.users.index', 'icon' => '👥', 'label' => 'Clients', 'group' => 'Gestion', 'compteur' => 'admin.users.index'],
                ['route' => 'admin.enseignants.index', 'icon' => '👨‍🏫', 'label' => 'Enseignants', 'group' => null, 'compteur' => null],
                ['route' => 'admin.categories.index', 'icon' => '📂', 'label' => 'Catégories', 'group' => null, 'compteur' => null],
                ['route' => 'admin.services.index', 'icon' => '💼', 'label' => 'Services', 'group' => null, 'compteur' => null],
                ['route' => 'admin.demandes.index', 'icon' => '📋', 'label' => 'Demandes', 'group' => null, 'compteur' => 'admin.demandes.index'],
                ['route' => 'admin.modules.index', 'icon' => '📚', 'label' => 'Modules de formations', 'group' => null, 'compteur' => null],
                ['route' => 'admin.formations.index', 'icon' => '🎓', 'label' => 'Formations', 'group' => null, 'compteur' => null],
                ['route' => 'admin.paiements.index', 'icon' => '💰', 'label' => 'Paiements', 'group' => null, 'compteur' => null],
                ['route' => 'admin.qcms.index', 'icon' => '📝', 'label' => 'QCMs', 'group' => null, 'compteur' => null],
                ['route' => 'admin.certificats.index', 'icon' => '🏆', 'label' => 'Certificats', 'group' => null, 'compteur' => null],
                ['route' => 'admin.notifications.form', 'icon' => '🔔', 'label' => 'Notifications', 'group' => 'Communication', 'compteur' => null],
                ['route' => 'admin.emails.form', 'icon' => '✉️', 'label' => 'Emails', 'group' => null, 'compteur' => null],
                ['route' => 'messages.index', 'icon' => '💬', 'label' => 'Messages', 'group' => null, 'compteur' => 'messages.index'],
                ['route' => 'admin.temoignages.index', 'icon' => '⭐', 'label' => 'Témoignages', 'group' => null, 'compteur' => 'admin.temoignages.index'],
                ['route' => 'admin.articles.index', 'icon' => '📰', 'label' => 'Blog', 'group' => 'Contenu', 'compteur' => null],
                ['route' => 'admin.faqs.index', 'icon' => '❓', 'label' => 'FAQ', 'group' => null, 'compteur' => null],
                ['route' => 'admin.configurations.index', 'icon' => '⚙️', 'label' => 'Config', 'group' => null, 'compteur' => null],
            ];
            $lastGroup = null;
            @endphp

            @foreach($navItems as $item)
                @if($item['group'] && $item['group'] !== $lastGroup)
                    @php $lastGroup = $item['group']; @endphp
                    <p class="text-xs font-semibold uppercase tracking-wider px-3 pt-4 pb-1" style="color: #3B82F6;">
                        {{ $item['group'] }}
                    </p>
                @endif
                <a href="{{ route($item['route']) }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                           {{ request()->routeIs($item['route']) || request()->routeIs(str_replace('index','*',$item['route']))
                               ? 'active'
                               : '' }}"
                    style="{{ request()->routeIs($item['route']) || request()->routeIs(str_replace('index','*',$item['route']))
                        ? 'background-color: rgba(59,130,246,0.20); color: #60A5FA;'
                        : 'color: #94A3B8;' }}"
                    onmouseover="if(!this.classList.contains('active')){this.style.backgroundColor='rgba(59,130,246,0.10)';this.style.color='#E2E8F0';}"
                    onmouseout="if(!this.classList.contains('active')){this.style.backgroundColor='transparent';this.style.color='#94A3B8';}">
                    <span class="text-base w-5 text-center">{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                    @if($item['compteur'] && ($compteurs[$item['compteur']] ?? 0) > 0)
                    <span class="ml-auto text-white text-xs rounded-full px-1.5 py-0.5 leading-none font-bold"
                        style="background-color: #EF4444;">
                        {{ $compteurs[$item['compteur']] > 99 ? '99+' : $compteurs[$item['compteur']] }}
                    </span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Profil --}}
        <div class="px-4 py-3" style="border-top: 1px solid rgba(59,130,246,0.15);">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                    style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold truncate" style="color: #F1F5F9;">
                        {{ auth()->user()->nom_complet }}
                    </p>
                    <p class="text-xs" style="color: #60A5FA;">Administrateur</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Déconnexion"
                        class="transition" style="color: #64748B;"
                        onmouseover="this.style.color='#EF4444'"
                        onmouseout="this.style.color='#64748B'">🚪</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- CONTENU PRINCIPAL --}}
    <div class="lg:ml-64 flex flex-col min-h-screen">

        {{-- TOPBAR --}}
        <header class="px-4 py-3 flex items-center justify-between sticky top-0 z-20"
            style="background-color: var(--edc-bg-card); border-bottom: 1px solid var(--edc-border);">
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = true"
                    class="lg:hidden p-2 rounded-lg transition"
                    style="color: var(--edc-text-secondary);"
                    onmouseover="this.style.backgroundColor='rgba(255,255,255,0.05)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h2 class="text-sm font-bold leading-tight" style="color: var(--edc-text-primary);">
                        @yield('page_title', 'Dashboard')
                    </h2>
                    <p class="text-xs hidden sm:block" style="color: var(--edc-text-muted);">
                        @yield('page_subtitle', 'Vue d\'ensemble')
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs hidden sm:block" style="color: var(--edc-text-muted);">
                    {{ now()->format('d/m/Y') }}
                </span>
                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer"
                    class="btn-tertiary btn-xs">
                    🌐 Voir le site
                </a>
            </div>
        </header>

        {{-- FLASH MESSAGES --}}
        <div class="px-4 pt-4 space-y-2">
            @if(session('success'))
            <div class="alert alert-success">
                <span>✅</span><span>{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-error">
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
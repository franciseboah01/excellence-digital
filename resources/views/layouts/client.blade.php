{{-- ═══════════════════════════════════════ --}}
{{-- CONFIGURATIONS GLOBALES --}}
{{-- ═══════════════════════════════════════ --}}
@php
    $siteNom  = \App\Models\Configuration::get('site_nom', 'Excellence Digital Center');
    $initiales = collect(explode(' ', $siteNom))
        ->map(fn($mot) => strtoupper(substr($mot, 0, 1)))
        ->take(3)
        ->implode('') ?: 'EDC';
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0B0F1A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title', 'Mon Espace — ' . $siteNom)</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%233B82F6'/><text x='50%' y='55%' dominant-baseline='middle' text-anchor='middle' font-family='Arial,sans-serif' font-size='40' font-weight='bold' fill='white'>{{ $initiales }}</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Empêcher le scroll du body quand un modal est ouvert */
        body.modal-open {
            overflow: hidden;
            position: fixed;
            width: 100%;
        }
        
        /* Amélioration tactile */
        @media (hover: none) and (pointer: coarse) {
            .hover\:bg-white\/5:active {
                background-color: rgba(255,255,255,0.08);
            }
        }
    </style>
</head>

<body class="font-sans antialiased" style="background-color: var(--edc-bg-deep); color: var(--edc-text-primary);">

    {{-- ========== NAVBAR CLIENT - OPTIMISÉE MOBILE ========== --}}
    <nav class="edc-navbar sticky top-0 z-50 safe-top" x-data="{ menuOpen: false }">
        <div class="max-w-7xl mx-auto px-3 sm:px-4">
            <div class="flex justify-between items-center h-14">
                {{-- Logo --}}
                <a href="{{ route('client.dashboard') }}" class="flex items-center space-x-2 sm:space-x-2.5 flex-shrink-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center font-black text-white text-xs sm:text-sm"
                        style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                        {{ $initiales }}
                    </div>
                    <span class="font-extrabold text-xs sm:text-sm truncate max-w-[120px] sm:max-w-none" 
                          style="color: var(--edc-text-primary);">
                        Mon Espace
                    </span>
                </a>

                {{-- Nav desktop (masqué sur mobile) --}}
                <div class="hidden lg:flex items-center space-x-1">
                    @foreach([
                        ['client.dashboard', '🏠', 'Dashboard'],
                        ['client.demandes', '📋', 'Demandes'],
                        ['client.formations', '🎓', 'Formations'],
                        ['client.qcms.index', '📝', 'QCMs'],
                        ['client.certificats.index', '🏆', 'Certificats'],
                        ['messages.index', '💬', 'Messages'],
                        ['client.paiements', '💰', 'Paiements'],
                    ] as $item)
                    <a href="{{ route($item[0]) }}"
                        class="nav-link {{ request()->routeIs($item[0]) ? 'active' : '' }} px-2 xl:px-3">
                        <span>{{ $item[1] }}</span>
                        <span class="ml-1.5 hidden xl:inline">{{ $item[2] }}</span>
                    </a>
                    @endforeach
                </div>

                {{-- Actions — visibles sur tous les écrans --}}
                <div class="flex items-center space-x-1 sm:space-x-2">
                    {{-- Bouton Voir le site (DESKTOP UNIQUEMENT) --}}
                    <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer"
                        class="btn-tertiary btn-xs !hidden lg:!inline-flex items-center"
                        style="display: none !important;">
                        🌐 <span class="hidden xl:inline ml-1">Voir le site</span>
                    </a>

                    {{-- Cloche notifications --}}
                    <div class="relative">
                        <button id="notifBtn" onclick="toggleNotifDropdown()"
                            class="relative p-2 rounded-lg transition active:scale-95 min-w-[40px] min-h-[40px] flex items-center justify-center"
                            style="touch-action: manipulation;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color: var(--edc-text-secondary);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @php $notifCount = auth()->user()->notifications()->where('lu', false)->count(); @endphp
                            @if($notifCount > 0)
                            <span class="notif-badge absolute -top-0.5 -right-0.5 text-white text-[10px] rounded-full min-w-[18px] h-[18px] flex items-center justify-center font-bold leading-none px-1"
                                style="background-color: var(--edc-danger);">
                                {{ $notifCount > 99 ? '99+' : $notifCount }}
                            </span>
                            @endif
                        </button>

                        <div id="notifDropdown"
                            class="hidden absolute right-0 mt-2 w-[calc(100vw-32px)] sm:w-80 rounded-xl shadow-2xl z-50 overflow-hidden"
                            style="background-color: var(--edc-bg-card); border: 1px solid var(--edc-border);">
                            <div class="flex justify-between items-center px-4 py-3" style="border-bottom: 1px solid var(--edc-border);">
                                <p class="font-semibold text-xs" style="color: var(--edc-text-primary);">🔔 Notifications</p>
                                <a href="{{ route('client.notifications') }}" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">Tout voir</a>
                            </div>
                            <div id="notifContainer" class="max-h-[50vh] sm:max-h-60 overflow-y-auto">
                                <p class="text-center text-xs py-4" style="color: var(--edc-text-muted);">Chargement...</p>
                            </div>
                        </div>
                    </div>

                    {{-- Messages badge --}}
                    <a href="{{ route('messages.index') }}" 
                       class="relative p-2 rounded-lg transition active:scale-95 min-w-[40px] min-h-[40px] flex items-center justify-center"
                       style="touch-action: manipulation;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="color: var(--edc-text-secondary);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        @php $msgCount = \App\Models\Message::where('destinataire_id', auth()->id())->where('lu', false)->count(); @endphp
                        @if($msgCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 text-white text-[10px] rounded-full min-w-[18px] h-[18px] flex items-center justify-center font-bold px-1"
                            style="background-color: var(--edc-success);">{{ $msgCount }}</span>
                        @endif
                    </a>

                    {{-- Avatar dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-1.5 p-1 rounded-lg transition active:scale-95 min-w-[40px] min-h-[40px]"
                            style="touch-action: manipulation;">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-white text-xs font-bold overflow-hidden flex-shrink-0"
                                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                                @endif
                            </div>
                            <span class="text-xs font-medium hidden sm:inline max-w-[80px] truncate" style="color: var(--edc-text-secondary);">
                                {{ auth()->user()->prenom }}
                            </span>
                        </button>

                        {{-- Dropdown du profil --}}
                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 rounded-xl shadow-2xl py-1 z-50"
                            style="background-color: var(--edc-bg-card); border: 1px solid var(--edc-border);">
                            <a href="{{ route('client.profil') }}"
                                class="flex items-center space-x-3 px-4 py-3 text-sm transition hover:bg-white/5"
                                style="color: var(--edc-text-secondary);">
                                <span>👤</span><span>Mon profil</span>
                            </a>
                            <a href="{{ route('client.notifications') }}"
                                class="flex items-center space-x-3 px-4 py-3 text-sm transition hover:bg-white/5"
                                style="color: var(--edc-text-secondary);">
                                <span>🔔</span><span>Notifications</span>
                            </a>
                            <hr style="border-color: var(--edc-border);">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center space-x-3 px-4 py-3 text-sm transition hover:bg-red-500/10"
                                    style="color: #EF4444;">
                                    <span>🚪</span><span>Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Burger mobile --}}
                    <button @click="menuOpen = !menuOpen"
                        class="lg:hidden p-2 rounded-lg transition active:scale-95 min-w-[40px] min-h-[40px] flex items-center justify-center"
                        style="color: var(--edc-text-secondary); touch-action: manipulation;"
                        aria-label="Menu">
                        <!-- Icône Burger -->
                        <svg x-show="!menuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <!-- Icône X -->
                        <svg x-show="menuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Menu mobile - OPTIMISÉ --}}
            <div x-show="menuOpen" @click.away="menuOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="lg:hidden pb-3 space-y-1"
                style="border-top: 1px solid var(--edc-border);">
                
                {{-- Navigation principale --}}
                @foreach([
                    ['client.dashboard', '🏠', 'Tableau de bord'],
                    ['client.demandes', '📋', 'Mes demandes'],
                    ['client.formations', '🎓', 'Mes formations'],
                    ['client.formations.disponibles', '🔍', 'Catalogue formations'],
                    ['client.qcms.index', '📝', 'QCMs'],
                    ['client.certificats.index', '🏆', 'Certificats'],
                    ['messages.index', '💬', 'Messagerie'],
                    ['client.temoignages.index', '⭐', 'Mes avis'],
                    ['client.paiements', '💰', 'Paiements'],
                    ['client.profil', '👤', 'Mon profil'],
                ] as $item)
                <a href="{{ route($item[0]) }}"
                    @click="menuOpen = false"
                    class="flex items-center space-x-3 px-4 py-3.5 text-sm font-semibold rounded-xl transition active:scale-[0.98]"
                    style="{{ request()->routeIs($item[0])
                        ? 'background-color: rgba(59,130,246,0.15); color: #60A5FA;'
                        : 'color: #E2E8F0;' }}"
                    onmouseover="if(window.innerWidth>1024){this.style.backgroundColor='rgba(59,130,246,0.08)'}"
                    onmouseout="if(window.innerWidth>1024){this.style.backgroundColor='transparent'}">
                    <span class="text-lg flex-shrink-0">{{ $item[1] }}</span>
                    <span>{{ $item[2] }}</span>
                </a>
                @endforeach
                
                {{-- Séparateur et bouton Voir le site (mobile) --}}
                <div class="px-4 pt-3 mt-2" style="border-top: 1px solid var(--edc-border);">
                    <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer"
                        @click="menuOpen = false"
                        class="flex items-center justify-center space-x-2 w-full py-3.5 rounded-xl text-sm font-bold transition active:scale-[0.98] min-h-[48px]"
                        style="background-color: rgba(59,130,246,0.12); color: #60A5FA; border: 1px solid rgba(59,130,246,0.30);">
                        <span>🌐</span>
                        <span>Voir le site public</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- CONTENU PRINCIPAL --}}
    <main class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">
        @if(session('success'))
        <div class="alert alert-success mb-4 animate-fade-in-up"><span>✅</span><span>{{ session('success') }}</span></div>
        @endif
        @if(session('error'))
        <div class="alert alert-error mb-4 animate-fade-in-up"><span>❌</span><span>{{ session('error') }}</span></div>
        @endif
        @yield('content')
    </main>

    {{-- Visionneuse PDF - Optimisée mobile --}}
    <div id="pdfModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-0 sm:p-2"
        style="background-color: rgba(0,0,0,0.9);">
        <div class="rounded-none sm:rounded-2xl w-full h-full sm:w-[95%] sm:max-w-4xl sm:h-[90%] flex flex-col shadow-2xl"
            style="background-color: var(--edc-bg-card); border: 1px solid var(--edc-border);">
            <div class="flex justify-between items-center p-3 sm:p-4" style="border-bottom: 1px solid var(--edc-border);">
                <h3 class="font-bold text-sm sm:text-base" style="color: var(--edc-text-primary);">📄 Document</h3>
                <button onclick="fermerPdf()"
                    class="text-xl font-bold w-10 h-10 flex items-center justify-center rounded-lg transition active:scale-90 min-w-[44px] min-h-[44px]"
                    style="color: var(--edc-text-muted); touch-action: manipulation;"
                    aria-label="Fermer">
                    ✕
                </button>
            </div>
            <iframe id="pdfViewer" src="" class="flex-1 rounded-b-none sm:rounded-b-2xl" frameborder="0"></iframe>
        </div>
    </div>

    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
    // === GESTION DES NOTIFICATIONS ===
    function mettreAJourCloche() {
        fetch('{{ route("notifications.non-lues") }}', { 
            headers: { 'X-Requested-With': 'XMLHttpRequest' } 
        })
        .then(r => r.json())
        .then(data => {
            document.querySelectorAll('.notif-badge').forEach(b => {
                if (data.count > 0) { 
                    b.textContent = data.count > 99 ? '99+' : data.count; 
                    b.style.display = 'flex';
                } else { 
                    b.style.display = 'none';
                }
            });
        })
        .catch(() => {});
    }
    
    mettreAJourCloche(); 
    setInterval(mettreAJourCloche, 30000);

    function toggleNotifDropdown() {
        const d = document.getElementById('notifDropdown');
        if (d.classList.contains('hidden')) { 
            chargerNotifs(); 
            d.classList.remove('hidden'); 
        } else { 
            d.classList.add('hidden'); 
        }
    }
    
    function chargerNotifs() {
        const c = document.getElementById('notifContainer');
        c.innerHTML = '<div class="text-center py-4"><div class="inline-block w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div></div>';
        
        fetch('{{ route("notifications.dernieres") }}', { 
            headers: { 'X-Requested-With': 'XMLHttpRequest' } 
        })
        .then(r => r.json())
        .then(notifs => {
            if (!notifs.length) { 
                c.innerHTML = '<div class="text-center py-6"><span class="text-2xl">🔔</span><p class="text-xs mt-2" style="color: var(--edc-text-muted);">Aucune notification</p></div>'; 
                return; 
            }
            
            const icons = { info:'📢', success:'✅', warning:'⚠️', error:'❌' };
            c.innerHTML = notifs.map(n => `
                <div class="flex items-start space-x-3 p-3 transition hover:bg-white/5 active:bg-white/5" style="border-bottom: 1px solid rgba(42,53,82,0.5);">
                    <span class="text-base flex-shrink-0 mt-0.5">${icons[n.type]||'📢'}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold truncate" style="color: #F1F5F9;">${n.titre}</p>
                        <p class="text-xs mt-0.5 line-clamp-2" style="color: #64748B;">${n.message}</p>
                        <p class="text-[10px] mt-1" style="color: #4B5563;">${n.created_at || 'À l\'instant'}</p>
                    </div>
                </div>
            `).join('');
            
            // Marquer comme lues
            fetch('{{ route("notifications.marquer-lu") }}', {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 
                    'Content-Type': 'application/json' 
                }
            }).then(() => mettreAJourCloche());
        })
        .catch(() => { 
            c.innerHTML = '<p class="text-center text-xs py-4" style="color: #EF4444;">Erreur de chargement</p>'; 
        });
    }
    
    // Fermer le dropdown au clic extérieur
    document.addEventListener('click', function(e) {
        const d = document.getElementById('notifDropdown');
        const b = document.getElementById('notifBtn');
        if (d && b && !b.contains(e.target) && !d.contains(e.target)) {
            d.classList.add('hidden');
        }
    });
    
    // === GESTION PDF ===
    function ouvrirPdf(url) {
        document.getElementById('pdfViewer').src = url;
        document.getElementById('pdfModal').classList.remove('hidden');
        document.body.classList.add('modal-open');
    }
    
    function fermerPdf() {
        document.getElementById('pdfViewer').src = '';
        document.getElementById('pdfModal').classList.add('hidden');
        document.body.classList.remove('modal-open');
    }
    
    document.addEventListener('keydown', e => { 
        if(e.key === 'Escape') fermerPdf(); 
    });
    
    // Swipe down pour fermer le PDF sur mobile
    let touchStartY = 0;
    document.getElementById('pdfModal')?.addEventListener('touchstart', (e) => {
        touchStartY = e.touches[0].clientY;
    });
    
    document.getElementById('pdfModal')?.addEventListener('touchmove', (e) => {
        const touchY = e.touches[0].clientY;
        if (touchY - touchStartY > 100) {
            fermerPdf();
        }
    });
    </script>
</body>
</html>
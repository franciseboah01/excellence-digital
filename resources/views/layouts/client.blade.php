<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mon Espace — EDC')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

    {{-- NAVBAR CLIENT --}}
    <nav class="bg-white shadow-sm sticky top-0 z-50" x-data="{ menuOpen: false }">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-14">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-md flex items-center justify-center
                                text-white font-black text-xs"
                        style="background:linear-gradient(135deg,#1e3a8a,#2563eb)">
                        EDC
                    </div>
                    <span class="font-bold text-blue-900 text-sm hidden sm:block">
                        Excellence Digital
                    </span>
                </a>

                {{-- Nav desktop --}}
                <div class="hidden md:flex items-center space-x-1">
                    @foreach([
                        ['client.dashboard', '🏠', 'Dashboard'],
                        ['client.demandes', '📋', 'Demandes'],
                        ['client.formations', '🎓', 'Formations'],
                        ['client.qcms.index', '📝', 'QCMs'],
                        ['messages.index', '💬', 'Messages'],
                    ] as $item)
                    <a href="{{ route($item[0]) }}"
                        class="flex items-center space-x-1 px-3 py-2 rounded-lg text-xs font-medium transition
                        {{ request()->routeIs($item[0]) ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:bg-gray-100' }}">
                        <span>{{ $item[1] }}</span>
                        <span class="hidden lg:block">{{ $item[2] }}</span>
                    </a>
                    @endforeach
                </div>

                {{-- Droite --}}
                <div class="flex items-center space-x-2">

                    {{-- Cloche notifications --}}
                    <div class="relative">
                        <button id="notifBtn" onclick="toggleNotifDropdown()"
                            class="relative p-2 rounded-lg hover:bg-gray-100 transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @php $notifCount = auth()->user()->notifications()->where('lu', false)->count(); @endphp
                            @if($notifCount > 0)
                            <span class="notif-badge absolute top-1 right-1 bg-red-500 text-white
                                         text-xs rounded-full w-4 h-4 flex items-center justify-center
                                         font-bold leading-none">
                                {{ $notifCount > 9 ? '9+' : $notifCount }}
                            </span>
                            @endif
                        </button>

                        {{-- Dropdown notifs --}}
                        <div id="notifDropdown"
                            class="hidden absolute right-0 mt-1 w-72 bg-white rounded-xl
                                   shadow-xl border border-gray-100 z-50">
                            <div class="flex justify-between items-center px-4 py-2
                                        border-b border-gray-100">
                                <p class="font-semibold text-gray-800 text-xs">🔔 Notifications</p>
                                <a href="{{ route('client.notifications') }}"
                                    class="text-xs text-blue-600 hover:underline">
                                    Tout voir
                                </a>
                            </div>
                            <div id="notifContainer" class="max-h-60 overflow-y-auto">
                                <p class="text-center text-gray-400 text-xs py-3">
                                    Chargement...
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Messages badge --}}
                    <a href="{{ route('messages.index') }}" class="relative p-2 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        @php $msgCount = \App\Models\Message::where('destinataire_id', auth()->id())->where('lu', false)->count(); @endphp
                        @if($msgCount > 0)
                        <span class="absolute top-1 right-1 bg-green-500 text-white text-xs
                                     rounded-full w-4 h-4 flex items-center justify-center font-bold">
                            {{ $msgCount }}
                        </span>
                        @endif
                    </a>

                    {{-- Avatar dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-1.5 p-1 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-7 h-7 rounded-full bg-blue-800 flex items-center
                                        justify-center text-white text-xs font-bold overflow-hidden">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                                @endif
                            </div>
                            <span class="text-xs font-medium text-gray-700 hidden sm:block max-w-16 truncate">
                                {{ auth()->user()->prenom }}
                            </span>
                            <svg class="w-3 h-3 text-gray-400 hidden sm:block" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-lg
                                   border border-gray-100 py-1 z-50">
                            <a href="{{ route('client.profil') }}"
                                class="flex items-center space-x-2 px-4 py-2.5 text-xs
                                       text-gray-700 hover:bg-blue-50 transition">
                                <span>👤</span><span>Mon profil</span>
                            </a>
                            <a href="{{ route('client.notifications') }}"
                                class="flex items-center space-x-2 px-4 py-2.5 text-xs
                                       text-gray-700 hover:bg-blue-50 transition">
                                <span>🔔</span><span>Notifications</span>
                            </a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center space-x-2 px-4 py-2.5
                                           text-xs text-red-600 hover:bg-red-50 transition">
                                    <span>🚪</span><span>Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Burger mobile --}}
                    <button @click="menuOpen = !menuOpen"
                        class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Menu mobile --}}
            <div x-show="menuOpen" @click.away="menuOpen = false"
                class="md:hidden border-t border-gray-100 py-2">
                @foreach([
                    ['client.dashboard', '🏠', 'Tableau de bord'],
                    ['client.demandes', '📋', 'Mes demandes'],
                    ['client.formations', '🎓', 'Mes formations'],
                    ['client.qcms.index', '📝', 'QCMs & Certificats'],
                    ['messages.index', '💬', 'Messagerie'],
                    ['client.temoignages.index', '⭐', 'Mes avis'],
                    ['client.profil', '👤', 'Mon profil'],
                ] as $item)
                <a href="{{ route($item[0]) }}"
                    @click="menuOpen = false"
                    class="flex items-center space-x-3 px-4 py-3 text-sm
                           font-medium text-gray-700 hover:bg-blue-50 transition
                           {{ request()->routeIs($item[0]) ? 'text-blue-700 bg-blue-50' : '' }}">
                    <span>{{ $item[1] }}</span>
                    <span>{{ $item[2] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </nav>

    {{-- CONTENU --}}
    <main class="max-w-7xl mx-auto px-4 py-6">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl p-3 mb-4 text-sm font-medium flex items-center space-x-2">
            <span>✅</span><span>{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 rounded-xl p-3 mb-4 text-sm font-medium flex items-center space-x-2">
            <span>❌</span><span>{{ session('error') }}</span>
        </div>
        @endif

        @yield('content')
    </main>

    {{-- Visionneuse PDF --}}
    <div id="pdfModal"
        class="hidden fixed inset-0 bg-black bg-opacity-80 z-50 flex items-center justify-center p-2">
        <div class="bg-white rounded-2xl w-full max-w-4xl h-5/6 flex flex-col shadow-2xl">
            <div class="flex justify-between items-center p-3 border-b">
                <h3 class="font-bold text-blue-900 text-sm">📄 Document</h3>
                <button onclick="fermerPdf()"
                    class="text-gray-400 hover:text-red-500 transition text-xl font-bold w-8 h-8
                           flex items-center justify-center rounded-lg hover:bg-red-50">
                    ✕
                </button>
            </div>
            <iframe id="pdfViewer" src="" class="flex-1 rounded-b-2xl" frameborder="0"></iframe>
        </div>
    </div>

    @stack('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
    // Notifications polling
    function mettreAJourCloche() {
        fetch('{{ route("notifications.non-lues") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            document.querySelectorAll('.notif-badge').forEach(b => {
                if (data.count > 0) {
                    b.textContent = data.count > 9 ? '9+' : data.count;
                    b.classList.remove('hidden');
                } else {
                    b.classList.add('hidden');
                }
            });
        }).catch(() => {});
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
        c.innerHTML = '<p class="text-center text-gray-400 text-xs py-3">Chargement...</p>';
        fetch('{{ route("notifications.dernieres") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(notifs => {
            if (!notifs.length) {
                c.innerHTML = '<p class="text-center text-gray-400 text-xs py-4">Aucune notification</p>';
                return;
            }
            const icons = { info:'📢', success:'✅', warning:'⚠️', error:'❌' };
            c.innerHTML = notifs.map(n => `
                <div class="flex items-start space-x-2 p-3 border-b border-gray-50 hover:bg-gray-50 last:border-0">
                    <span class="text-sm flex-shrink-0">${icons[n.type]||'📢'}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate">${n.titre}</p>
                        <p class="text-xs text-gray-400 mt-0.5 truncate">${n.message}</p>
                    </div>
                </div>
            `).join('');
            fetch('{{ route("notifications.marquer-lu") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Content-Type': 'application/json'
                }
            }).then(() => mettreAJourCloche());
        }).catch(() => {
            c.innerHTML = '<p class="text-center text-red-400 text-xs py-3">Erreur</p>';
        });
    }

    document.addEventListener('click', function(e) {
        const d = document.getElementById('notifDropdown');
        const b = document.getElementById('notifBtn');
        if (d && b && !b.contains(e.target) && !d.contains(e.target)) {
            d.classList.add('hidden');
        }
    });

    function ouvrirPdf(url) {
        document.getElementById('pdfViewer').src = url;
        document.getElementById('pdfModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function fermerPdf() {
        document.getElementById('pdfViewer').src = '';
        document.getElementById('pdfModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    function ouvrirFichierSecurise(ressourceId, type) {
        fetch(`/ressources/${ressourceId}/url-signee`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { alert('❌ ' + data.error); return; }
            type === 'pdf' ? ouvrirPdf(data.url) : window.location.href = data.url;
        })
        .catch(() => alert('❌ Erreur lors de la génération du lien.'));
    }
    document.addEventListener('keydown', e => { if(e.key==='Escape') fermerPdf(); });
    </script>
</body>
</html>
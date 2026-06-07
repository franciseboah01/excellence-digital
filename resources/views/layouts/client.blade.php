<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- CSRF TOKEN --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mon Espace — EDC')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{-- MODAL VISIONNEUSE PDF --}}
<div id="pdfModal" class="hidden fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl h-5/6 flex flex-col">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="font-bold text-blue-900">📄 Visionneuse de document</h3>
            <button onclick="fermerPdf()"
                class="text-gray-500 hover:text-red-600 transition text-xl font-bold">✕</button>
        </div>
        <iframe id="pdfViewer" src="" class="flex-1 rounded-b-2xl" frameborder="0"></iframe>
    </div>
</div>

<script>
    function ouvrirFichierSecurise(ressourceId, type) {
        fetch(`/ressources/${ressourceId}/url-signee`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert('❌ ' + data.error);
                return;
            }
            if (type === 'pdf') {
                // Ouvrir PDF dans la visionneuse
                ouvrirPdf(data.url);
            } else {
                // Télécharger directement
                window.location.href = data.url;
            }
        })
        .catch(() => alert('❌ Erreur lors de la génération du lien.'));
    }
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
    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') fermerPdf();
    });

    {{-- POLLING NOTIFICATIONS TEMPS RÉEL --}}

    // Mise à jour badge cloche toutes les 30 secondes
    function mettreAJourCloche() {
        fetch('{{ route("notifications.non-lues") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            const badges = document.querySelectorAll('.notif-badge');
            badges.forEach(badge => {
                if (data.count > 0) {
                    badge.textContent = data.count > 9 ? '9+' : data.count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        })
        .catch(() => {});
    }

    // Lancer immédiatement puis toutes les 30s
    mettreAJourCloche();
    setInterval(mettreAJourCloche, 30000);

    // Dropdown notifications rapides
    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notifDropdown');
        if (dropdown.classList.contains('hidden')) {
            chargerDernieresNotifs();
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }
    }

    function chargerDernieresNotifs() {
        const container = document.getElementById('notifContainer');
        container.innerHTML = '<p class="text-center text-gray-400 text-xs py-3">Chargement...</p>';

        fetch('{{ route("notifications.dernieres") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(notifs => {
            if (notifs.length === 0) {
                container.innerHTML = '<p class="text-center text-gray-400 text-xs py-4">✅ Aucune nouvelle notification</p>';
                return;
            }

            const icones = { info: '📢', success: '✅', warning: '⚠️', error: '❌' };
            container.innerHTML = notifs.map(n => `
                <div class="flex items-start space-x-2 p-3 border-b border-gray-100 last:border-0 hover:bg-gray-50">
                    <span class="text-base">${icones[n.type] || '📢'}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate">${n.titre}</p>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">${n.message}</p>
                    </div>
                </div>
            `).join('');

            // Marquer comme lues
            fetch('{{ route("notifications.marquer-lu") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            }).then(() => mettreAJourCloche());
        })
        .catch(() => {
            container.innerHTML = '<p class="text-center text-gray-400 text-xs py-3">Erreur de chargement</p>';
        });
    }

    // Fermer dropdown si clic en dehors
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notifDropdown');
        const btn = document.getElementById('notifBtn');
        if (dropdown && btn && !btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

</script>

<body class="bg-gray-100 font-sans">

    {{-- NAVBAR CLIENT --}}
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <span class="text-2xl font-bold text-blue-800">EDC</span>
                    <span class="hidden md:block text-sm text-gray-400">Excellence Digital Center</span>
                </a>

                {{-- Nav Links --}}
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('client.dashboard') }}"
                        class="text-gray-600 hover:text-blue-700 font-medium transition text-sm">
                        🏠 Tableau de bord
                    </a>
                    <a href="{{ route('client.demandes') }}"
                        class="text-gray-600 hover:text-blue-700 font-medium transition text-sm">
                        📋 Mes demandes
                    </a>
                    <a href="{{ route('client.formations') }}"
                        class="text-gray-600 hover:text-blue-700 font-medium transition text-sm">
                        🎓 Mes formations
                    </a>
                    <a href="{{ route('client.notifications') }}"
                        class="text-gray-600 hover:text-blue-700 font-medium transition text-sm">
                        🔔 Notifications
                    </a>
                </div>

                {{-- Droite : Notif + Avatar --}}
                <div class="flex items-center space-x-4">

                    {{-- Cloche notifications avec dropdown --}}
                    <div class="relative">
                        <button id="notifBtn" onclick="toggleNotifDropdown()"
                            class="relative focus:outline-none">
                            <svg class="w-6 h-6 text-gray-600 hover:text-blue-700 transition"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            {{-- Badge --}}
                            @php $notifCount = auth()->user()->notifications()->where('lu', false)->count(); @endphp
                            <span class="notif-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold
                                {{ $notifCount == 0 ? 'hidden' : '' }}">
                                {{ $notifCount > 9 ? '9+' : $notifCount }}
                            </span>
                        </button>

                        {{-- Dropdown rapide --}}
                        <div id="notifDropdown"
                            class="hidden absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl border border-gray-100 z-50">
                            <div class="flex justify-between items-center px-4 py-3 border-b border-gray-100">
                                <p class="font-semibold text-gray-800 text-sm">🔔 Notifications</p>
                                <a href="{{ route('client.notifications') }}"
                                    class="text-xs text-blue-600 hover:underline">Tout voir</a>
                            </div>
                            <div id="notifContainer" class="max-h-64 overflow-y-auto">
                                <p class="text-center text-gray-400 text-xs py-4">Cliquez pour charger</p>
                            </div>
                        </div>
                    </div>

                    {{-- Badge Messages --}}
                    <a href="{{ route('messages.index') }}" class="relative">
                        <svg class="w-6 h-6 text-gray-600 hover:text-blue-700 transition"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        @php $msgCount = \App\Models\Message::where('destinataire_id', auth()->id())->where('lu', false)->count(); @endphp
                        @if($msgCount > 0)
                        <span class="msg-badge absolute -top-1 -right-1 bg-green-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                            {{ $msgCount > 9 ? '9+' : $msgCount }}
                        </span>
                        @endif
                    </a>

                    {{-- Avatar + Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-2 focus:outline-none">
                            <div class="w-9 h-9 rounded-full bg-blue-800 flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                            </div>
                            <span class="hidden md:block text-sm font-medium text-gray-700">
                                {{ auth()->user()->prenom }}
                            </span>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                            <a href="{{ route('client.profil') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
                                👤 Mon profil
                            </a>
                            <hr class="my-1">
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
        </div>
    </nav>

    {{-- CONTENU --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Message flash --}}
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

    {{-- Alpine.js pour les dropdowns --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- CSRF TOKEN --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Espace Enseignant — EDC')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans">

{{-- SCRIPT NOTIFICATIONS --}}
<script>
    function mettreAJourCloche() {
        fetch('{{ route("notifications.non-lues") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            document.querySelectorAll('.notif-badge').forEach(badge => {
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

    mettreAJourCloche();
    setInterval(mettreAJourCloche, 30000);

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

            if (!notifs.length) {
                container.innerHTML = '<p class="text-center text-gray-400 text-xs py-4">Aucune notification</p>';
                return;
            }

            const icones = { info: '📢', success: '✅', warning: '⚠️', error: '❌' };

            container.innerHTML = notifs.map(n => `
                <div class="flex items-start space-x-2 p-3 border-b last:border-0 hover:bg-gray-50">
                    <span>${icones[n.type] || '📢'}</span>
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-800">${n.titre}</p>
                        <p class="text-xs text-gray-500">${n.message}</p>
                    </div>
                </div>
            `).join('');

            fetch('{{ route("notifications.marquer-lu") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            }).then(() => mettreAJourCloche());
        });
    }

    document.addEventListener('click', function (e) {
        const dropdown = document.getElementById('notifDropdown');
        const btn = document.getElementById('notifBtn');

        if (dropdown && btn && !btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>

{{-- NAVBAR --}}
<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <span class="text-2xl font-bold text-blue-800">EDC</span>
                <span class="hidden md:block text-xs text-gray-400">Espace Enseignant</span>
            </a>

            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('enseignant.dashboard') }}" class="text-gray-600 hover:text-blue-700 text-sm">
                    🏠 Dashboard
                </a>
                <a href="{{ route('enseignant.ressources.index') }}" class="text-gray-600 hover:text-blue-700 text-sm">
                    📚 Ressources
                </a>
                <a href="{{ route('enseignant.notifications.form') }}" class="text-gray-600 hover:text-blue-700 text-sm">
                    🔔 Notifications
                </a>
            </div>

            <div class="flex items-center space-x-4" x-data="{ open: false }">

                {{-- 🔔 CLOCHES NOTIFICATIONS --}}
                <div class="relative">
                    <button id="notifBtn" onclick="toggleNotifDropdown()"
                        class="relative focus:outline-none">

                        <svg class="w-6 h-6 text-gray-600 hover:text-blue-700 transition"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>

                        @php
                            $notifCount = auth()->user()->notifications()->where('lu', false)->count();
                        @endphp

                        <span class="notif-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center {{ $notifCount == 0 ? 'hidden' : '' }}">
                            {{ $notifCount > 9 ? '9+' : $notifCount }}
                        </span>
                    </button>

                    <div id="notifDropdown"
                        class="hidden absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl border z-50">

                        <div class="px-4 py-3 border-b">
                            <p class="font-semibold text-sm">🔔 Notifications</p>
                        </div>

                        <div id="notifContainer" class="max-h-64 overflow-y-auto">
                            <p class="text-center text-gray-400 text-xs py-4">Cliquez pour charger</p>
                        </div>
                    </div>
                </div>

                {{-- AVATAR --}}
                <div class="relative">
                    <button @click="open = !open" class="flex items-center space-x-2">
                        <div class="w-9 h-9 rounded-full bg-green-700 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                        </div>
                        <span class="hidden md:block text-sm">
                            {{ auth()->user()->prenom }}
                        </span>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border py-2 z-50">

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

{{-- MAIN --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 rounded-xl p-4 mb-6">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 rounded-xl p-4 mb-6">
            ❌ {{ session('error') }}
        </div>
    @endif

    @yield('content')

</main>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@stack('scripts')

</body>
</html>
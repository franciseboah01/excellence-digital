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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Enseignant — ' . $siteNom)</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%233B82F6'/><text x='50%' y='55%' dominant-baseline='middle' text-anchor='middle' font-family='Arial,sans-serif' font-size='40' font-weight='bold' fill='white'>{{ $initiales }}</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased" style="background-color: var(--edc-bg-deep); color: var(--edc-text-primary);">

{{-- ========== NAVBAR ========== --}}
<nav class="edc-navbar sticky top-0 z-50" x-data="{ menuOpen: false }">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-14">

            {{-- Logo --}}
            <a href="{{ route('enseignant.dashboard') }}" class="flex items-center space-x-2.5 flex-shrink-0">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-white text-sm"
                     style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                    {{ $initiales }}
                </div>
                <span class="font-extrabold text-sm hidden sm:block" style="color: var(--edc-text-primary);">
                    Espace Enseignant
                </span>
            </a>

            {{-- Nav desktop --}}
            <div class="hidden md:flex items-center space-x-1">
                @foreach([
                    ['enseignant.dashboard', '🏠', 'Dashboard'],
                    ['enseignant.ressources.index', '📚', 'Ressources'],
                    ['enseignant.notifications.form', '🔔', 'Notifications'],
                    ['enseignant.qcms.index', '📝', 'QCMs'],
                ] as $item)
                <a href="{{ route($item[0]) }}"
                    class="nav-link {{ request()->routeIs($item[0]) ? 'active' : '' }}">
                    <span>{{ $item[1] }}</span>
                    <span class="ml-1.5 hidden lg:inline">{{ $item[2] }}</span>
                </a>
                @endforeach
            </div>

            {{-- Actions desktop --}}
            <div class="flex items-center space-x-2">

                {{-- Bouton Voir le site --}}
                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer"
                    class="btn-tertiary btn-xs hidden lg:inline-flex">
                    🌐 Voir le site
                </a>

                {{-- Cloche notifications --}}
                <div class="relative">
                    <button id="notifBtn" onclick="toggleNotifDropdown()"
                        class="relative p-2 rounded-lg transition hover:bg-white/5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="color: var(--edc-text-secondary);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @php $notifCount = auth()->user()->notifications()->where('lu', false)->count(); @endphp
                        @if($notifCount > 0)
                        <span class="notif-badge absolute top-1 right-1 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold leading-none"
                            style="background-color: var(--edc-danger);">
                            {{ $notifCount > 9 ? '9+' : $notifCount }}
                        </span>
                        @endif
                    </button>

                    <div id="notifDropdown"
                        class="hidden absolute right-0 mt-1 w-72 rounded-xl shadow-2xl z-50 overflow-hidden"
                        style="background-color: var(--edc-bg-card); border: 1px solid var(--edc-border);">
                        <div class="flex justify-between items-center px-4 py-2.5" style="border-bottom: 1px solid var(--edc-border);">
                            <p class="font-semibold text-xs" style="color: var(--edc-text-primary);">🔔 Notifications</p>
                        </div>
                        <div id="notifContainer" class="max-h-60 overflow-y-auto">
                            <p class="text-center text-xs py-3" style="color: var(--edc-text-muted);">Chargement...</p>
                        </div>
                    </div>
                </div>

                {{-- Messages badge --}}
                <a href="{{ route('messages.index') }}" class="relative p-2 rounded-lg transition hover:bg-white/5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="color: var(--edc-text-secondary);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    @php $msgCount = \App\Models\Message::where('destinataire_id', auth()->id())->where('lu', false)->count(); @endphp
                    @if($msgCount > 0)
                    <span class="absolute top-1 right-1 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold"
                        style="background-color: var(--edc-success);">{{ $msgCount }}</span>
                    @endif
                </a>

                {{-- Avatar dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center space-x-1.5 p-1 rounded-lg transition hover:bg-white/5">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm overflow-hidden"
                            style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                            @endif
                        </div>
                        <span class="text-xs font-medium hidden sm:inline max-w-16 truncate" style="color: var(--edc-text-secondary);">
                            {{ auth()->user()->prenom }}
                        </span>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-1 w-44 rounded-xl shadow-2xl py-1 z-50"
                        style="background-color: var(--edc-bg-card); border: 1px solid var(--edc-border);">
                        <a href="{{ route('enseignant.profil') }}"
                            class="flex items-center space-x-2 px-4 py-2.5 text-xs transition"
                            style="color: var(--edc-text-secondary);"
                            onmouseover="this.style.backgroundColor='rgba(59,130,246,0.08)'; this.style.color='#60A5FA';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#94A3B8';">
                            <span>👤</span><span>Mon profil</span>
                        </a>
                        <hr style="border-color: var(--edc-border);">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center space-x-2 px-4 py-2.5 text-xs transition"
                                style="color: #EF4444;"
                                onmouseover="this.style.backgroundColor='rgba(239,68,68,0.08)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <span>🚪</span><span>Déconnexion</span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Burger mobile --}}
                <button @click="menuOpen = !menuOpen"
                    class="md:hidden p-2 rounded-lg transition"
                    style="color: var(--edc-text-secondary);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Menu mobile --}}
        <div x-show="menuOpen" @click.away="menuOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden py-3 space-y-1" style="border-top: 1px solid var(--edc-border);">
            @foreach([
                ['enseignant.dashboard', '🏠', 'Tableau de bord'],
                ['enseignant.ressources.index', '📚', 'Ressources'],
                ['enseignant.notifications.form', '🔔', 'Notifications'],
                ['enseignant.qcms.index', '📝', 'QCMs'],
            ] as $item)
            <a href="{{ route($item[0]) }}"
                @click="menuOpen = false"
                class="flex items-center space-x-3 px-4 py-3 text-sm font-semibold rounded-xl transition"
                style="{{ request()->routeIs($item[0])
                    ? 'background-color: rgba(59,130,246,0.15); color: #60A5FA;'
                    : 'color: #E2E8F0;' }}">
                <span>{{ $item[1] }}</span>
                <span>{{ $item[2] }}</span>
            </a>
            @endforeach
            <div class="px-4 pt-3 mt-2" style="border-top: 1px solid var(--edc-border);">
                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer"
                    class="block w-full text-center py-3 rounded-xl text-sm font-bold transition"
                    style="background-color: rgba(59,130,246,0.12); color: #60A5FA; border: 1px solid rgba(59,130,246,0.30);">
                    🌐 Voir le site
                </a>
            </div>
        </div>
    </div>
</nav>

{{-- MAIN --}}
<main class="max-w-7xl mx-auto px-4 py-6">
    @if(session('success'))
    <div class="alert alert-success mb-4"><span>✅</span><span>{{ session('success') }}</span></div>
    @endif
    @if(session('error'))
    <div class="alert alert-error mb-4"><span>❌</span><span>{{ session('error') }}</span></div>
    @endif
    @yield('content')
</main>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

{{-- SCRIPT NOTIFICATIONS --}}
<script>
function mettreAJourCloche() {
    fetch('{{ route("notifications.non-lues") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json()).then(data => {
        document.querySelectorAll('.notif-badge').forEach(b => {
            if (data.count > 0) { b.textContent = data.count > 9 ? '9+' : data.count; b.classList.remove('hidden'); }
            else { b.classList.add('hidden'); }
        });
    }).catch(() => {});
}
mettreAJourCloche(); setInterval(mettreAJourCloche, 30000);

function toggleNotifDropdown() {
    const d = document.getElementById('notifDropdown');
    if (d.classList.contains('hidden')) { chargerNotifs(); d.classList.remove('hidden'); }
    else { d.classList.add('hidden'); }
}
function chargerNotifs() {
    const c = document.getElementById('notifContainer');
    c.innerHTML = '<p class="text-center text-xs py-3" style="color: var(--edc-text-muted);">Chargement...</p>';
    fetch('{{ route("notifications.dernieres") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json()).then(notifs => {
        if (!notifs.length) { c.innerHTML = '<p class="text-center text-xs py-4" style="color: var(--edc-text-muted);">Aucune notification</p>'; return; }
        const icons = { info:'📢', success:'✅', warning:'⚠️', error:'❌' };
        c.innerHTML = notifs.map(n => `
            <div class="flex items-start space-x-2 p-3" style="border-bottom: 1px solid rgba(42,53,82,0.5);">
                <span class="text-sm flex-shrink-0">${icons[n.type]||'📢'}</span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold truncate" style="color: #F1F5F9;">${n.titre}</p>
                    <p class="text-xs mt-0.5 truncate" style="color: #64748B;">${n.message}</p>
                </div>
            </div>`).join('');
        fetch('{{ route("notifications.marquer-lu") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Content-Type': 'application/json' }
        }).then(() => mettreAJourCloche());
    }).catch(() => { c.innerHTML = '<p class="text-center text-xs py-3" style="color: #EF4444;">Erreur</p>'; });
}
document.addEventListener('click', function(e) {
    const d = document.getElementById('notifDropdown'), b = document.getElementById('notifBtn');
    if (d && b && !b.contains(e.target) && !d.contains(e.target)) d.classList.add('hidden');
});
</script>

@stack('scripts')
</body>
</html>
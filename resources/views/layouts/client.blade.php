<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

                    {{-- Cloche notifications --}}
                    <a href="{{ route('client.notifications') }}" class="relative">
                        <svg class="w-6 h-6 text-gray-600 hover:text-blue-700 transition" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @php
                            $notifCount = auth()->user()->notifications()->where('lu', false)->count();
                        @endphp
                        @if($notifCount > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                            {{ $notifCount > 9 ? '9+' : $notifCount }}
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
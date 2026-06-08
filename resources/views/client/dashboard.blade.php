@extends('layouts.client')
@section('title', 'Mon Espace')

@section('content')

{{-- HEADER --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-blue-900">
            Bonjour {{ auth()->user()->prenom }} 👋
        </h1>
        <p class="text-gray-500 text-xs sm:text-sm mt-0.5">
            Bienvenue dans votre espace personnel
        </p>
    </div>
    <a href="{{ route('demande.form') }}"
        class="inline-flex items-center justify-center space-x-2 bg-blue-800
               text-white font-semibold px-5 py-2.5 rounded-xl hover:bg-blue-900
               transition text-sm btn-touch">
        <span>➕</span><span>Nouvelle demande</span>
    </a>
</div>

{{-- STATS --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    @foreach([
        [$stats['demandes'],       '📋 Demandes',     'blue'],
        [$stats['demandes_cours'], '🔄 En cours',     'green'],
        [$stats['formations'],     '🎓 Formations',   'purple'],
        [$stats['notifications'],  '🔔 Non lues',     'red'],
    ] as $stat)
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-{{ $stat[2] }}-500">
        <p class="text-2xl font-extrabold text-{{ $stat[2] }}-700">{{ $stat[0] }}</p>
        <p class="text-gray-500 text-xs mt-1">{{ $stat[1] }}</p>
    </div>
    @endforeach
</div>

{{-- CONTENU --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- PRINCIPALE --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Demandes --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold text-blue-900">📋 Dernières demandes</h2>
                <a href="{{ route('client.demandes') }}"
                    class="text-xs text-blue-600 hover:underline">Voir tout</a>
            </div>
            @forelse($dernieres_demandes as $d)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center space-x-3">
                    <span class="text-xl">{{ $d->service->icone ?? '💼' }}</span>
                    <div>
                        <p class="font-medium text-gray-800 text-sm">
                            {{ Str::limit($d->service->titre, 28) }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $d->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                @include('client.partials.statut-badge', ['statut' => $d->statut])
            </div>
            @empty
            <div class="text-center py-6 text-gray-400">
                <p class="text-3xl mb-2">📋</p>
                <p class="text-sm">Aucune demande.</p>
            </div>
            @endforelse
            <div class="mt-4">
                <a href="{{ route('demande.form') }}"
                    class="block text-center bg-blue-800 text-white py-2.5 rounded-xl
                           text-sm font-semibold hover:bg-blue-900 transition">
                    + Nouvelle demande
                </a>
            </div>
        </div>

        {{-- Formations --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold text-blue-900">🎓 Mes Formations</h2>
                <a href="{{ route('client.formations') }}"
                    class="text-xs text-blue-600 hover:underline">Voir tout</a>
            </div>
            @forelse($mes_formations as $i)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div>
                    <p class="font-medium text-gray-800 text-sm">
                        {{ $i->formation->titre }}
                    </p>
                    <span class="inline-block bg-blue-100 text-blue-700 text-xs
                                 px-2 py-0.5 rounded-full mt-0.5">
                        {{ ucfirst($i->formation->niveau) }}
                    </span>
                </div>
                <a href="{{ route('client.ressources', $i->formation) }}"
                    class="bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-lg
                           font-semibold hover:bg-green-200 transition flex-shrink-0 ml-2 btn-touch">
                    Accéder →
                </a>
            </div>
            @empty
            <div class="text-center py-6 text-gray-400">
                <p class="text-3xl mb-2">🎓</p>
                <p class="text-sm">Aucune formation.</p>
                <a href="{{ route('formations.index') }}"
                    class="inline-block mt-2 bg-blue-800 text-white px-4 py-2
                           rounded-lg text-xs hover:bg-blue-900">
                    Voir les formations
                </a>
            </div>
            @endforelse
        </div>

        {{-- Notifications --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold text-blue-900">🔔 Notifications récentes</h2>
                <a href="{{ route('client.notifications') }}"
                    class="text-xs text-blue-600 hover:underline">Tout voir</a>
            </div>
            @forelse($notifications->take(4) as $n)
            <div class="flex items-start space-x-2 py-2.5 border-b border-gray-100 last:border-0
                        {{ !$n->lu ? 'bg-blue-50 rounded-lg px-2' : '' }}">
                <span class="text-base mt-0.5">
                    @if($n->type=='success')✅@elseif($n->type=='warning')⚠️@elseif($n->type=='error')❌@else📢@endif
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-800 truncate">{{ $n->titre }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $n->message }}</p>
                    <p class="text-xs text-gray-300 mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                @if(!$n->lu)
                <div class="w-1.5 h-1.5 bg-blue-500 rounded-full flex-shrink-0 mt-1.5"></div>
                @endif
            </div>
            @empty
            <p class="text-gray-400 text-xs text-center py-4">Aucune notification.</p>
            @endforelse
        </div>
    </div>

    {{-- COLONNE DROITE --}}
    <div class="space-y-4">

        {{-- Profil --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100 text-center">
            <div class="w-14 h-14 rounded-full bg-blue-800 flex items-center
                        justify-center text-white text-xl font-bold mx-auto mb-3 overflow-hidden">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/'.auth()->user()->avatar) }}"
                        class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                @endif
            </div>
            <p class="font-bold text-blue-900 text-sm">{{ auth()->user()->nom_complet }}</p>
            <p class="text-xs text-gray-400 mt-0.5 truncate px-2">{{ auth()->user()->email }}</p>
            <span class="inline-block bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full mt-2">
                👤 Client
            </span>
            <a href="{{ route('client.profil') }}"
                class="block mt-3 border border-gray-300 text-gray-700 py-2 rounded-xl
                       text-xs font-semibold hover:bg-gray-50 transition">
                ✏️ Modifier le profil
            </a>
        </div>

        {{-- Actions rapides --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
            <h3 class="font-bold text-blue-900 mb-3 text-sm">⚡ Actions rapides</h3>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-1">
                @foreach([
                    ['demande.form',              '💼', 'Service',      'bg-blue-50  text-blue-800'],
                    ['client.formations',         '🎓', 'Formations',   'bg-green-50 text-green-800'],
                    ['client.qcms.index',         '📝', 'QCMs',         'bg-purple-50 text-purple-800'],
                    ['messages.index',            '💬', 'Messages',     'bg-yellow-50 text-yellow-800'],
                    ['client.temoignages.index',  '⭐', 'Avis',         'bg-red-50 text-red-700'],
                ] as $a)
                <a href="{{ route($a[0]) }}"
                    class="flex items-center space-x-2 p-3 rounded-xl hover:opacity-80
                           transition text-xs font-semibold btn-touch {{ $a[3] }}">
                    <span>{{ $a[1] }}</span><span>{{ $a[2] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
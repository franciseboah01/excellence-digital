@extends('layouts.admin')
@section('title', 'Services')
@section('page_title', 'Gestion des Services')
@section('page_subtitle', 'Créez et gérez les services proposés')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-6">
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-blue-600">
        <p class="text-2xl font-bold text-blue-700">{{ $stats['total'] }}</p>
        <p class="text-gray-500 text-xs mt-1">Total</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-green-500">
        <p class="text-2xl font-bold text-green-600">{{ $stats['actifs'] }}</p>
        <p class="text-gray-500 text-xs mt-1">Actifs</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-purple-500">
        <p class="text-2xl font-bold text-purple-600">{{ $stats['bureautique'] }}</p>
        <p class="text-gray-500 text-xs mt-1">💼 Bureautique</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-yellow-500">
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['design'] }}</p>
        <p class="text-gray-500 text-xs mt-1">🌐 Design</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-red-500">
        <p class="text-2xl font-bold text-red-600">{{ $stats['web_mobile'] }}</p>
        <p class="text-gray-500 text-xs mt-1">💻 Web & Mobile</p>
    </div>
</div>

{{-- BOUTON AJOUTER --}}
<div class="flex justify-end mt-5">
    <a href="{{ route('admin.services.create') }}"
        class="bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
        ➕ Nouveau service
    </a>
</div>

{{-- GRILLE SERVICES --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-4">
    @forelse($services as $service)
    <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden
        {{ !$service->actif ? 'opacity-60' : '' }}">
        <div class="p-5">
            <div class="flex items-start justify-between mb-3">
                <span class="text-3xl">{{ $service->icone }}</span>
                <div class="flex items-center space-x-2">
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                        {{ $service->actif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $service->actif ? '✅ Actif' : '⏸️ Inactif' }}
                    </span>
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                        {{ $service->demandes_count }} demande(s)
                    </span>
                </div>
            </div>

            <h3 class="font-bold text-gray-800 mb-1">{{ $service->titre }}</h3>

            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                @if($service->categorie == 'bureautique') 💼 Bureautique
                @elseif($service->categorie == 'design') 🌐 Design
                @else 💻 Web & Mobile
                @endif
            </span>

            <p class="text-gray-500 text-sm mt-2 leading-relaxed">
                {{ Str::limit($service->description, 80) }}
            </p>

            @if($service->prix)
            <p class="text-blue-700 font-semibold mt-2 text-sm">
                {{ number_format($service->prix, 0, ',', ' ') }} FCFA
            </p>
            @endif
        </div>

        <div class="border-t border-gray-100 px-5 py-3 bg-gray-50 flex justify-between items-center">
            <div class="flex space-x-3">
                <a href="{{ route('admin.services.edit', $service) }}"
                    class="text-xs text-blue-600 hover:underline font-medium">
                    ✏️ Modifier
                </a>
                <form method="POST" action="{{ route('admin.services.toggle', $service) }}">
                    @csrf
                    <button type="submit" class="text-xs font-medium
                        {{ $service->actif ? 'text-yellow-600 hover:underline' : 'text-green-600 hover:underline' }}">
                        {{ $service->actif ? '⏸️ Désactiver' : '▶️ Activer' }}
                    </button>
                </form>
            </div>
            <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
                onsubmit="return confirm('Supprimer ce service ?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-red-600 hover:underline font-medium">
                    🗑️ Supprimer
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-xl shadow text-center py-16 text-gray-400">
        <p class="text-5xl mb-4">💼</p>
        <p class="font-medium">Aucun service créé.</p>
        <a href="{{ route('admin.services.create') }}"
            class="inline-block mt-4 bg-blue-800 text-white px-6 py-2 rounded-lg text-sm">
            Créer le premier service
        </a>
    </div>
    @endforelse
</div>

<div class="mt-6">{{ $services->links() }}</div>
@endsection
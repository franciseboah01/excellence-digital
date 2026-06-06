@extends('layouts.admin')
@section('title', 'Témoignages')
@section('page_title', 'Modération des Témoignages')
@section('page_subtitle', 'Validez ou refusez les avis des clients')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-gray-400">
        <p class="text-2xl font-bold text-gray-700">{{ $stats['total'] }}</p>
        <p class="text-gray-500 text-xs mt-1">⭐ Total</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-yellow-500">
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['en_attente'] }}</p>
        <p class="text-gray-500 text-xs mt-1">⏳ En attente</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-green-500">
        <p class="text-2xl font-bold text-green-600">{{ $stats['valides'] }}</p>
        <p class="text-gray-500 text-xs mt-1">✅ Publiés</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-red-500">
        <p class="text-2xl font-bold text-red-600">{{ $stats['refuses'] }}</p>
        <p class="text-gray-500 text-xs mt-1">❌ Refusés</p>
    </div>
</div>

{{-- LISTE --}}
<div class="bg-white rounded-xl shadow mt-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-blue-900">
            ⭐ {{ $temoignages->total() }} témoignage(s)
        </h3>
    </div>

    @forelse($temoignages as $temoignage)
    <div class="p-6 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
        <div class="flex items-start justify-between">
            <div class="flex items-start space-x-4 flex-1">
                {{-- Avatar --}}
                <div class="w-10 h-10 rounded-full bg-blue-800 flex items-center justify-center text-white font-bold flex-shrink-0">
                    {{ strtoupper(substr($temoignage->user->prenom, 0, 1)) }}
                </div>

                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-1">
                        <p class="font-semibold text-gray-800">
                            {{ $temoignage->user->prenom }} {{ $temoignage->user->nom }}
                        </p>
                        <div class="text-yellow-400 text-sm">
                            {!! $temoignage->etoiles_html !!}
                        </div>
                        <span class="text-xs text-gray-300">
                            {{ $temoignage->created_at->diffForHumans() }}
                        </span>
                    </div>

                    @if($temoignage->formation)
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                        🎓 {{ $temoignage->formation->titre }}
                    </span>
                    @elseif($temoignage->service)
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                        💼 {{ $temoignage->service->titre }}
                    </span>
                    @endif

                    <p class="text-gray-600 text-sm mt-2 leading-relaxed">
                        "{{ $temoignage->contenu }}"
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col items-end space-y-2 ml-4 flex-shrink-0">
                @php
                    $badge = match($temoignage->statut_validation) {
                        'valide'     => 'bg-green-100 text-green-700',
                        'refuse'     => 'bg-red-100 text-red-700',
                        default      => 'bg-yellow-100 text-yellow-700',
                    };
                    $label = match($temoignage->statut_validation) {
                        'valide'     => '✅ Publié',
                        'refuse'     => '❌ Refusé',
                        default      => '⏳ En attente',
                    };
                @endphp
                <span class="text-xs px-2 py-1 rounded-full font-medium {{ $badge }}">
                    {{ $label }}
                </span>

                <div class="flex space-x-2">
                    @if($temoignage->statut_validation !== 'valide')
                    <form method="POST"
                        action="{{ route('admin.temoignages.valider', $temoignage) }}">
                        @csrf
                        <button type="submit"
                            class="text-xs bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700 transition">
                            ✅ Publier
                        </button>
                    </form>
                    @endif

                    @if($temoignage->statut_validation !== 'refuse')
                    <form method="POST"
                        action="{{ route('admin.temoignages.refuser', $temoignage) }}">
                        @csrf
                        <button type="submit"
                            class="text-xs bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 transition">
                            ❌ Refuser
                        </button>
                    </form>
                    @endif

                    <form method="POST"
                        action="{{ route('admin.temoignages.destroy', $temoignage) }}"
                        onsubmit="return confirm('Supprimer définitivement ?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="text-xs text-red-500 hover:underline font-medium">
                            🗑️
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-16 text-gray-400">
        <p class="text-5xl mb-4">⭐</p>
        <p>Aucun témoignage pour le moment.</p>
    </div>
    @endforelse

    <div class="px-6 py-4 border-t border-gray-100">
        {{ $temoignages->links() }}
    </div>
</div>
@endsection
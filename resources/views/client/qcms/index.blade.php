@extends('layouts.client')
@section('title', 'QCMs & Certificats')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-blue-900">🎓 QCMs & Certificats</h1>
    <p class="text-gray-500 mt-1 text-sm">
        Testez vos connaissances et obtenez vos certificats
    </p>
</div>

{{-- CERTIFICATS OBTENUS --}}
@if($certificats->count())
<div class="bg-gradient-to-r from-yellow-500 to-yellow-400 rounded-2xl p-6 mb-6">
    <h2 class="text-lg font-bold text-white mb-4">🏆 Mes Certificats obtenus</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($certificats as $certificat)
        <div class="bg-white rounded-xl p-4 flex items-center justify-between">
            <div>
                <p class="font-bold text-gray-800">{{ $certificat->formation->titre }}</p>
                <p class="text-xs text-gray-500 mt-0.5">
                    N° {{ $certificat->numero_certificat }} •
                    {{ $certificat->delivre_le->format('d/m/Y') }} •
                    Note : {{ $certificat->note_obtenue }}/20
                </p>
            </div>
            <a href="{{ route('certificats.telecharger', $certificat) }}"
                class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-yellow-600 transition flex-shrink-0 ml-3">
                📄 PDF
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- QCMS DISPONIBLES --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    @forelse($qcms as $qcm)
    <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
        <div class="p-5">
            <div class="flex justify-between items-start mb-3">
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium">
                    🎓 {{ $qcm->formation->titre }}
                </span>
                @if($qcm->deja_reussi)
                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold">
                    🏆 Réussi !
                </span>
                @elseif($qcm->tentatives_faites > 0)
                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">
                    🔄 {{ $qcm->tentatives_faites }}/{{ $qcm->tentatives_max }} tentatives
                </span>
                @endif
            </div>

            <h3 class="font-bold text-gray-800 mb-1">{{ $qcm->titre }}</h3>
            @if($qcm->niveau)
            <p class="text-xs text-gray-400">📂 {{ $qcm->niveau->nom }}</p>
            @endif

            <div class="grid grid-cols-3 gap-2 mt-3 text-center">
                <div class="bg-blue-50 rounded-lg p-2">
                    <p class="text-xs text-gray-400">Questions</p>
                    <p class="font-bold text-blue-800">{{ $qcm->questions_count }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-2">
                    <p class="text-xs text-gray-400">Note min.</p>
                    <p class="font-bold text-green-700">{{ $qcm->note_minimale }}/20</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-2">
                    <p class="text-xs text-gray-400">Durée/Q</p>
                    <p class="font-bold text-yellow-700">{{ $qcm->duree_par_question }}s</p>
                </div>
            </div>

            @if($qcm->meilleure_note)
            <div class="mt-3">
                <div class="flex justify-between text-xs text-gray-400 mb-1">
                    <span>Meilleure note</span>
                    <span>{{ $qcm->meilleure_note }}/20</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $qcm->deja_reussi ? 'bg-green-500' : 'bg-blue-500' }}"
                        style="width: {{ ($qcm->meilleure_note / 20) * 100 }}%">
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="border-t border-gray-100 px-5 py-3 bg-gray-50">
            @if($qcm->deja_reussi)
            <p class="text-center text-green-600 text-sm font-semibold">
                🎓 Certificat obtenu !
            </p>
            @elseif($qcm->peut_repasser)
            <a href="{{ route('client.qcms.demarrer', $qcm) }}"
                class="block w-full text-center bg-blue-800 text-white py-2 rounded-lg text-sm font-semibold hover:bg-blue-900 transition">
                {{ $qcm->tentatives_faites > 0 ? '🔄 Repasser le QCM' : '▶️ Commencer le QCM' }}
            </a>
            @else
            <p class="text-center text-red-500 text-sm">
                ❌ Tentatives épuisées ({{ $qcm->tentatives_max }}/{{ $qcm->tentatives_max }})
            </p>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-2 bg-white rounded-xl shadow text-center py-12 text-gray-400">
        <p class="text-5xl mb-4">📝</p>
        <p>Aucun QCM disponible pour vos formations.</p>
        <p class="text-sm mt-1">Les enseignants ajouteront des QCMs prochainement.</p>
    </div>
    @endforelse
</div>
@endsection
@extends('layouts.enseignant')
@section('title', 'Mes QCMs')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-blue-900">📝 Mes QCMs</h1>
    <a href="{{ route('enseignant.qcms.create') }}"
        class="bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
        ➕ Créer un QCM
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($qcms as $qcm)
    <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
        <div class="p-5">
            <div class="flex justify-between items-start mb-3">
                <span class="text-xs px-2 py-1 rounded-full font-medium
                    {{ $qcm->actif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $qcm->actif ? '✅ Actif' : '⏸️ Inactif' }}
                </span>
                <span class="text-xs text-gray-400">
                    {{ $qcm->questions_count }}/10 questions
                </span>
            </div>

            <h3 class="font-bold text-gray-800 mb-1">{{ $qcm->titre }}</h3>
            <p class="text-xs text-blue-600">🎓 {{ $qcm->formation->titre }}</p>
            @if($qcm->niveau)
            <p class="text-xs text-gray-400 mt-0.5">📂 {{ $qcm->niveau->nom }}</p>
            @endif

            <div class="flex items-center space-x-4 mt-3 text-xs text-gray-500">
                <span>⏱ {{ $qcm->duree_par_question }}s/question</span>
                <span>🎯 {{ $qcm->note_minimale }}/20 requis</span>
                <span>🔄 {{ $qcm->tentatives_max }} essais</span>
            </div>
        </div>

        <div class="border-t border-gray-100 px-5 py-3 bg-gray-50 flex justify-between items-center">
            <div class="flex space-x-3">
                <a href="{{ route('enseignant.qcms.questions', $qcm) }}"
                    class="text-xs text-blue-600 hover:underline font-medium">
                    ✏️ Questions
                </a>
                <a href="{{ route('enseignant.qcms.resultats', $qcm) }}"
                    class="text-xs text-purple-600 hover:underline font-medium">
                    📊 Résultats
                </a>
            </div>
            <div class="flex space-x-2">
                <form method="POST" action="{{ route('enseignant.qcms.toggle', $qcm) }}">
                    @csrf
                    <button type="submit" class="text-xs font-medium
                        {{ $qcm->actif ? 'text-yellow-600' : 'text-green-600' }} hover:underline">
                        {{ $qcm->actif ? '⏸️' : '▶️' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('enseignant.qcms.destroy', $qcm) }}"
                    onsubmit="return confirm('Supprimer ce QCM ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-600 hover:underline">🗑️</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-xl shadow text-center py-16 text-gray-400">
        <p class="text-5xl mb-4">📝</p>
        <p>Aucun QCM créé.</p>
        <a href="{{ route('enseignant.qcms.create') }}"
            class="inline-block mt-4 bg-blue-800 text-white px-5 py-2 rounded-lg text-sm">
            Créer mon premier QCM
        </a>
    </div>
    @endforelse
</div>
@endsection
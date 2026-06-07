@extends('layouts.admin')
@section('title', 'QCMs')
@section('page_title', 'Gestion des QCMs')
@section('page_subtitle', 'Supervision des QCMs et certificats')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-6">
    <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-blue-600">
        <p class="text-2xl font-bold text-blue-700">{{ $stats['total'] }}</p>
        <p class="text-gray-500 text-xs mt-1">📝 QCMs total</p>
    </div>
    <div class="bg-green-50 rounded-xl shadow p-4 text-center border-l-4 border-green-500">
        <p class="text-2xl font-bold text-green-600">{{ $stats['actifs'] }}</p>
        <p class="text-gray-500 text-xs mt-1">✅ Actifs</p>
    </div>
    <div class="bg-blue-50 rounded-xl shadow p-4 text-center border-l-4 border-blue-400">
        <p class="text-2xl font-bold text-blue-600">{{ $stats['sessions'] }}</p>
        <p class="text-gray-500 text-xs mt-1">🎯 Sessions</p>
    </div>
    <div class="bg-yellow-50 rounded-xl shadow p-4 text-center border-l-4 border-yellow-500">
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['certificats'] }}</p>
        <p class="text-gray-500 text-xs mt-1">🏆 Certificats</p>
    </div>
    <div class="bg-purple-50 rounded-xl shadow p-4 text-center border-l-4 border-purple-500">
        <p class="text-2xl font-bold text-purple-600">{{ $stats['taux_reussite'] }}%</p>
        <p class="text-gray-500 text-xs mt-1">📈 Taux réussite</p>
    </div>
</div>

<div class="flex justify-end mt-5">
    <a href="{{ route('admin.certificats.index') }}"
        class="bg-yellow-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-yellow-600 transition mr-3">
        🏆 Voir les certificats
    </a>
</div>

<div class="bg-white rounded-xl shadow mt-4 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">QCM</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Formation</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Enseignant</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Questions</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sessions</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($qcms as $qcm)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-4">
                    <p class="font-semibold text-gray-800">{{ $qcm->titre }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        🎯 {{ $qcm->note_minimale }}/20 requis •
                        ⏱ {{ $qcm->duree_par_question }}s/Q
                    </p>
                </td>
                <td class="px-5 py-4 text-sm text-gray-600">
                    {{ Str::limit($qcm->formation->titre, 25) }}
                    @if($qcm->niveau)
                    <p class="text-xs text-gray-400">{{ $qcm->niveau->nom }}</p>
                    @endif
                </td>
                <td class="px-5 py-4 text-sm text-gray-600">
                    {{ $qcm->createur->prenom }} {{ $qcm->createur->nom }}
                </td>
                <td class="px-5 py-4 text-center">
                    <span class="text-sm font-bold
                        {{ $qcm->questions_count >= 10 ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $qcm->questions_count }}/10
                    </span>
                </td>
                <td class="px-5 py-4 text-center">
                    <span class="text-sm font-bold text-blue-700">
                        {{ $qcm->sessions_count }}
                    </span>
                </td>
                <td class="px-5 py-4">
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                        {{ $qcm->actif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $qcm->actif ? '✅ Actif' : '⏸️ Inactif' }}
                    </span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.qcms.show', $qcm) }}"
                            class="text-xs text-blue-600 hover:underline">👁️ Voir</a>
                        <form method="POST" action="{{ route('admin.qcms.toggle', $qcm) }}">
                            @csrf
                            <button class="text-xs {{ $qcm->actif ? 'text-yellow-600' : 'text-green-600' }} hover:underline">
                                {{ $qcm->actif ? '⏸️' : '▶️' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.qcms.destroy', $qcm) }}"
                            onsubmit="return confirm('Supprimer ce QCM ?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-600 hover:underline">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                    <p class="text-4xl mb-3">📝</p>
                    <p>Aucun QCM créé par les enseignants.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100">{{ $qcms->links() }}</div>
</div>
@endsection
@extends('layouts.admin')
@section('title', 'Certificats')
@section('page_title', 'Certificats délivrés')

@section('content')
<div class="grid grid-cols-3 gap-4 mt-6">
    <div class="bg-yellow-50 rounded-xl shadow p-4 text-center border-l-4 border-yellow-500">
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['total'] }}</p>
        <p class="text-gray-500 text-xs mt-1">🏆 Total certificats</p>
    </div>
    <div class="bg-green-50 rounded-xl shadow p-4 text-center border-l-4 border-green-500">
        <p class="text-2xl font-bold text-green-600">{{ $stats['ce_mois'] }}</p>
        <p class="text-gray-500 text-xs mt-1">📅 Ce mois-ci</p>
    </div>
    <div class="bg-blue-50 rounded-xl shadow p-4 text-center border-l-4 border-blue-500">
        <p class="text-2xl font-bold text-blue-600">{{ $stats['moyenne'] }}/20</p>
        <p class="text-gray-500 text-xs mt-1">📊 Note moyenne</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow mt-5 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">N° Certificat</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Apprenant</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Formation</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Note</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($certificats as $cert)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-mono text-xs text-blue-800 font-bold">
                    {{ $cert->numero_certificat }}
                </td>
                <td class="px-5 py-3 font-medium text-gray-800">
                    {{ $cert->user->prenom }} {{ $cert->user->nom }}
                </td>
                <td class="px-5 py-3 text-gray-600 text-xs">
                    {{ $cert->formation->titre }}
                </td>
                <td class="px-5 py-3">
                    <span class="font-bold text-green-600">{{ $cert->note_obtenue }}/20</span>
                </td>
                <td class="px-5 py-3 text-gray-400 text-xs">
                    {{ $cert->delivre_le->format('d/m/Y') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                    <p class="text-4xl mb-3">🏆</p>
                    <p>Aucun certificat délivré.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100">{{ $certificats->links() }}</div>
</div>
@endsection
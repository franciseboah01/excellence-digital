@extends('layouts.admin')
@section('title', 'FAQ')
@section('page_title', 'Gestion FAQ')
@section('page_subtitle', 'Questions fréquentes publiques')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

    {{-- FORMULAIRE AJOUT --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold text-blue-900 mb-5">➕ Ajouter une question</h2>
        <form method="POST" action="{{ route('admin.faqs.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Question *</label>
                <textarea name="question" rows="2" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="La question..."></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Réponse *</label>
                <textarea name="reponse" rows="4" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="La réponse détaillée..."></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Catégorie *</label>
                <input type="text" name="categorie" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ex : général, formations, paiements...">
            </div>
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Ordre</label>
                <input type="number" name="ordre" value="0" min="0"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition">
                ➕ Ajouter
            </button>
        </form>
    </div>

    {{-- LISTE FAQ --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-blue-900">
                    ❓ {{ $faqs->total() }} question(s)
                </h2>
            </div>
            @forelse($faqs as $faq)
            <div class="p-5 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition"
                x-data="{ editOpen: false }">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 text-sm">{{ $faq->question }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            📂 {{ $faq->categorie }} • Ordre : {{ $faq->ordre }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            {{ Str::limit($faq->reponse, 80) }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2 ml-4 flex-shrink-0">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $faq->actif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $faq->actif ? '✅' : '⏸️' }}
                        </span>
                        <button @click="editOpen = !editOpen"
                            class="text-xs text-blue-600 hover:underline">✏️</button>
                        <form method="POST" action="{{ route('admin.faqs.toggle', $faq) }}">
                            @csrf
                            <button type="submit" class="text-xs text-yellow-600 hover:underline">
                                {{ $faq->actif ? '⏸️' : '▶️' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}"
                            onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:underline">🗑️</button>
                        </form>
                    </div>
                </div>

                {{-- Édition inline --}}
                <div x-show="editOpen" x-cloak class="mt-4 bg-gray-50 rounded-xl p-4">
                    <form method="POST" action="{{ route('admin.faqs.update', $faq) }}">
                        @csrf @method('PUT')
                        <textarea name="question" rows="2" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $faq->question }}</textarea>
                        <textarea name="reponse" rows="3" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $faq->reponse }}</textarea>
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <input type="text" name="categorie" value="{{ $faq->categorie }}" required
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="number" name="ordre" value="{{ $faq->ordre }}" min="0"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit"
                            class="bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
                            💾 Enregistrer
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">
                <p class="text-4xl mb-3">❓</p>
                <p>Aucune question ajoutée.</p>
            </div>
            @endforelse
            <div class="px-6 py-4 border-t border-gray-100">{{ $faqs->links() }}</div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.admin')
@section('title', 'FAQ')
@section('page_title', '❓ Gestion FAQ')
@section('page_subtitle', 'Questions fréquentes publiques')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

    {{-- FORMULAIRE AJOUT --}}
    <div class="edc-card p-6">
        <h2 class="text-lg font-bold mb-5" style="color: var(--edc-text-primary);">➕ Ajouter une question</h2>
        <form method="POST" action="{{ route('admin.faqs.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="edc-label">Question *</label>
                <textarea name="question" rows="2" required class="edc-input" placeholder="La question..."></textarea>
            </div>
            <div>
                <label class="edc-label">Réponse *</label>
                <textarea name="reponse" rows="4" required class="edc-input" placeholder="La réponse détaillée..."></textarea>
            </div>
            <div>
                <label class="edc-label">Catégorie *</label>
                <input type="text" name="categorie" required class="edc-input" placeholder="Ex : général, formations, paiements...">
            </div>
            <div>
                <label class="edc-label">Ordre</label>
                <input type="number" name="ordre" value="0" min="0" class="edc-input">
            </div>
            <button type="submit" class="btn-primary w-full">➕ Ajouter</button>
        </form>
    </div>

    {{-- LISTE FAQ --}}
    <div class="lg:col-span-2">
        <div class="edc-card overflow-hidden">
            <div class="px-6 py-4" style="border-bottom: 1px solid var(--edc-border);">
                <h2 class="text-lg font-bold" style="color: var(--edc-text-primary);">❓ {{ $faqs->total() }} question(s)</h2>
            </div>
            @forelse($faqs as $faq)
            <div class="p-5 transition" style="border-bottom: 1px solid var(--edc-border);"
                onmouseover="this.style.backgroundColor='rgba(255,255,255,0.02)'"
                onmouseout="this.style.backgroundColor='transparent'"
                x-data="{ editOpen: false }">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="font-semibold text-sm" style="color: var(--edc-text-primary);">{{ $faq->question }}</p>
                        <p class="text-xs mt-1" style="color: var(--edc-text-muted);">📂 {{ $faq->categorie }} • Ordre : {{ $faq->ordre }}</p>
                        <p class="text-xs mt-1 leading-relaxed" style="color: var(--edc-text-secondary);">{{ Str::limit($faq->reponse, 80) }}</p>
                    </div>
                    <div class="flex items-center space-x-2 ml-4 flex-shrink-0">
                        <span class="badge text-xs" style="{{ $faq->actif
                            ? 'background-color: rgba(16,185,129,0.12); color: #34D399;'
                            : 'background-color: rgba(148,163,184,0.10); color: #94A3B8;' }}">
                            {{ $faq->actif ? '✅' : '⏸️' }}
                        </span>
                        <button @click="editOpen = !editOpen" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">✏️</button>
                        <form method="POST" action="{{ route('admin.faqs.toggle', $faq) }}">
                            @csrf
                            <button type="submit" class="text-xs font-medium hover:underline"
                                style="color: {{ $faq->actif ? 'var(--edc-accent-gold)' : 'var(--edc-secondary)' }};">
                                {{ $faq->actif ? '⏸️' : '▶️' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}"
                            onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium hover:underline" style="color: var(--edc-danger);">🗑️</button>
                        </form>
                    </div>
                </div>

                {{-- Édition inline --}}
                <div x-show="editOpen" x-cloak class="mt-4 rounded-xl p-4" style="background-color: var(--edc-bg-base);">
                    <form method="POST" action="{{ route('admin.faqs.update', $faq) }}" class="space-y-3">
                        @csrf @method('PUT')
                        <textarea name="question" rows="2" required class="edc-input">{{ $faq->question }}</textarea>
                        <textarea name="reponse" rows="3" required class="edc-input">{{ $faq->reponse }}</textarea>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="categorie" value="{{ $faq->categorie }}" required class="edc-input">
                            <input type="number" name="ordre" value="{{ $faq->ordre }}" min="0" class="edc-input">
                        </div>
                        <button type="submit" class="btn-primary btn-sm">💾 Enregistrer</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-12" style="color: var(--edc-text-muted);">
                <p class="text-4xl mb-3">❓</p>
                <p>Aucune question ajoutée.</p>
            </div>
            @endforelse
            <div class="px-6 py-4" style="border-top: 1px solid var(--edc-border);">{{ $faqs->links() }}</div>
        </div>
    </div>
</div>
@endsection
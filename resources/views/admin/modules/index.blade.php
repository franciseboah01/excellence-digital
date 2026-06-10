@extends('layouts.admin')
@section('title', 'Modules')
@section('page_title', '📚 Gestion des Modules')
@section('page_subtitle', 'Organisez vos formations par modules')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

    {{-- FORMULAIRE AJOUT --}}
    <div class="edc-card p-6">
        <h2 class="text-lg font-bold mb-5" style="color: var(--edc-text-primary);">➕ Ajouter un module</h2>
        <form method="POST" action="{{ route('admin.modules.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="edc-label">Nom *</label>
                <input type="text" name="nom" required class="edc-input" placeholder="Ex : Débutant">
            </div>
            <div>
                <label class="edc-label">Icône (emoji)</label>
                <input type="text" name="icone" value="📚" class="edc-input w-24 text-2xl text-center" maxlength="5">
            </div>
            <button type="submit" class="btn-primary w-full">➕ Ajouter</button>
        </form>
    </div>

    {{-- LISTE --}}
    <div class="lg:col-span-2 edc-card overflow-hidden">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--edc-border);">
            <h2 class="text-lg font-bold" style="color: var(--edc-text-primary);">📚 {{ $modules->total() }} module(s)</h2>
        </div>
        @forelse($modules as $module)
        <div x-data="{ editOpen: false }" class="transition" style="border-bottom: 1px solid var(--edc-border);"
            onmouseover="this.style.backgroundColor='rgba(255,255,255,0.02)'"
            onmouseout="this.style.backgroundColor='transparent'">

            {{-- Affichage normal --}}
            <div class="p-5 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">{{ $module->icone }}</span>
                    <div>
                        <p class="font-semibold text-sm" style="color: var(--edc-text-primary);">{{ $module->nom }}</p>
                        <p class="text-xs" style="color: var(--edc-text-muted);">{{ $module->formations_count }} formation(s) liée(s)</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="editOpen = !editOpen" class="text-xs font-medium hover:underline" style="color: var(--edc-primary-light);">✏️</button>
                    <form method="POST" action="{{ route('admin.modules.destroy', $module) }}"
                        onsubmit="return confirm('Supprimer ce module ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-medium hover:underline" style="color: var(--edc-danger);">🗑️</button>
                    </form>
                </div>
            </div>

            {{-- Édition inline --}}
            <div x-show="editOpen" x-cloak class="px-5 pb-5">
                <form method="POST" action="{{ route('admin.modules.update', $module) }}" class="space-y-3">
                    @csrf @method('PUT')
                    <div>
                        <label class="edc-label">Nom</label>
                        <input type="text" name="nom" value="{{ $module->nom }}" required class="edc-input">
                    </div>
                    <div>
                        <label class="edc-label">Icône (emoji)</label>
                        <input type="text" name="icone" value="{{ $module->icone }}" class="edc-input w-24 text-2xl text-center">
                    </div>
                    <button type="submit" class="btn-primary btn-sm">💾 Enregistrer</button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-12" style="color: var(--edc-text-muted);">
            <p class="text-4xl mb-3">📚</p>
            <p>Aucun module créé.</p>
        </div>
        @endforelse
        <div class="px-6 py-4" style="border-top: 1px solid var(--edc-border);">{{ $modules->links() }}</div>
    </div>
</div>
@endsection
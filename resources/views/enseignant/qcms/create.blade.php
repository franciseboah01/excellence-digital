@extends('layouts.enseignant')
@section('title', 'Créer un QCM')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('enseignant.qcms.index') }}"
        class="text-blue-600 hover:underline text-sm">← Retour</a>
    <h1 class="text-2xl font-bold text-blue-900 mt-2 mb-6">➕ Créer un QCM</h1>

    <div class="bg-white rounded-xl shadow p-8">
        <form method="POST" action="{{ route('enseignant.qcms.store') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Formation *</label>
                <select name="formation_id" id="formation_id" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Choisir --</option>
                    @foreach($formations as $formation)
                    <option value="{{ $formation->id }}">{{ $formation->titre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Niveau (optionnel)</label>
                <select name="niveau_id" id="niveau_id"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Général --</option>
                </select>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Titre du QCM *</label>
                <input type="text" name="titre" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ex : QCM Excel — Niveau Débutant">
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Description du QCM..."></textarea>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Durée/question (sec) *
                    </label>
                    <input type="number" name="duree_par_question" value="120"
                        min="30" max="600" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-400 mt-1">30s à 10min</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Note minimale /20 *
                    </label>
                    <input type="number" name="note_minimale" value="14"
                        min="1" max="20" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-400 mt-1">Pour obtenir le cert.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Tentatives max *
                    </label>
                    <input type="number" name="tentatives_max" value="3"
                        min="1" max="5" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition">
                ➕ Créer et ajouter les questions →
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('formation_id').addEventListener('change', function() {
        const formationId = this.value;
        const niveauSelect = document.getElementById('niveau_id');
        niveauSelect.innerHTML = '<option value="">-- Général --</option>';
        if (!formationId) return;
        fetch(`/enseignant/formations/${formationId}/niveaux`)
            .then(r => r.json())
            .then(niveaux => niveaux.forEach(n => {
                const opt = document.createElement('option');
                opt.value = n.id;
                opt.textContent = `${n.nom}`;
                niveauSelect.appendChild(opt);
            }));
    });
</script>
@endpush
@endsection
@extends('layouts.client')
@section('title', 'Mes Avis')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-blue-900">⭐ Mes Avis & Évaluations</h1>
    <p class="text-gray-500 mt-1 text-sm">Partagez votre expérience avec EDC</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- FORMULAIRE --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold text-blue-900 mb-5">✍️ Laisser un avis</h2>

        <form method="POST" action="{{ route('client.temoignages.store') }}">
            @csrf

            {{-- Sujet --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Avis sur une formation
                </label>
                <select name="formation_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Aucune formation --</option>
                    @foreach($formations as $formation)
                    <option value="{{ $formation->id }}">{{ $formation->titre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Ou sur un service
                </label>
                <select name="service_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Aucun service --</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}">{{ $service->titre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Note étoiles --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Note *</label>
                <div class="flex space-x-2" id="starRating">
                    @for($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer">
                        <input type="radio" name="note" value="{{ $i }}"
                            class="hidden" {{ $i == 5 ? 'checked' : '' }}>
                        <span class="text-3xl star-btn transition"
                            data-value="{{ $i }}">⭐</span>
                    </label>
                    @endfor
                </div>
                <p class="text-xs text-gray-400 mt-1" id="noteLabel">Excellent (5/5)</p>
            </div>

            {{-- Contenu --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Votre avis *
                </label>
                <textarea name="contenu" rows="4" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Partagez votre expérience avec EDC..."></textarea>
                @error('contenu')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition">
                ⭐ Soumettre mon avis
            </button>

            <p class="text-xs text-gray-400 text-center mt-2">
                Votre avis sera publié après modération par l'administrateur.
            </p>
        </form>
    </div>

    {{-- MES AVIS --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold text-blue-900 mb-5">📋 Mes avis soumis</h2>

        @forelse($temoignages as $temoignage)
        <div class="border border-gray-200 rounded-xl p-4 mb-3">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <div class="text-yellow-400 text-lg">
                        {!! $temoignage->etoiles_html !!}
                    </div>
                    @if($temoignage->formation)
                    <p class="text-xs text-blue-600 font-medium mt-1">
                        🎓 {{ $temoignage->formation->titre }}
                    </p>
                    @elseif($temoignage->service)
                    <p class="text-xs text-green-600 font-medium mt-1">
                        💼 {{ $temoignage->service->titre }}
                    </p>
                    @endif
                </div>
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
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">
                "{{ $temoignage->contenu }}"
            </p>
            <div class="flex justify-between items-center mt-3">
                <p class="text-xs text-gray-300">
                    {{ $temoignage->created_at->format('d/m/Y') }}
                </p>
                @if($temoignage->statut_validation === 'en_attente')
                <form method="POST"
                    action="{{ route('client.temoignages.destroy', $temoignage) }}"
                    onsubmit="return confirm('Supprimer cet avis ?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="text-xs text-red-500 hover:underline">
                        🗑️ Supprimer
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400">
            <p class="text-4xl mb-3">⭐</p>
            <p class="text-sm">Vous n'avez pas encore soumis d'avis.</p>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    // Système d'étoiles interactif
    const labels = ['', 'Mauvais (1/5)', 'Passable (2/5)', 'Bien (3/5)', 'Très bien (4/5)', 'Excellent (5/5)'];
    const stars = document.querySelectorAll('.star-btn');

    stars.forEach((star, idx) => {
        star.addEventListener('click', function() {
            const val = parseInt(this.dataset.value);
            document.querySelectorAll('input[name="note"]')[val - 1].checked = true;
            document.getElementById('noteLabel').textContent = labels[val];
            stars.forEach((s, i) => {
                s.style.opacity = i < val ? '1' : '0.3';
            });
        });
    });
</script>
@endpush
@endsection
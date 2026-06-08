@extends('layouts.client')
@section('title', 'Mes Avis')

@section('content')
<div class="mb-6">
    <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">⭐ Mes Avis & Évaluations</h1>
    <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">Partagez votre expérience avec EDC</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- FORMULAIRE --}}
    <div class="edc-card p-6">
        <h2 class="text-lg font-bold mb-5" style="color: var(--edc-text-primary);">✍️ Laisser un avis</h2>

        <form method="POST" action="{{ route('client.temoignages.store') }}">
            @csrf

            {{-- Sujet --}}
            <div class="mb-4">
                <label class="edc-label">Avis sur une formation</label>
                <select name="formation_id" class="edc-select">
                    <option value="">-- Aucune formation --</option>
                    @foreach($formations as $formation)
                    <option value="{{ $formation->id }}">{{ $formation->titre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="edc-label">Ou sur un service</label>
                <select name="service_id" class="edc-select">
                    <option value="">-- Aucun service --</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}">{{ $service->titre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Note étoiles --}}
            <div class="mb-4">
                <label class="edc-label">Note *</label>
                <div class="flex space-x-2" id="starRating">
                    @for($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer">
                        <input type="radio" name="note" value="{{ $i }}"
                            class="hidden" {{ $i == 5 ? 'checked' : '' }}>
                        <span class="text-3xl star-btn transition"
                            data-value="{{ $i }}"
                            style="color: var(--edc-accent-gold);">★</span>
                    </label>
                    @endfor
                </div>
                <p class="text-xs mt-1" style="color: var(--edc-text-muted);" id="noteLabel">Excellent (5/5)</p>
            </div>

            {{-- Contenu --}}
            <div class="mb-5">
                <label class="edc-label">Votre avis *</label>
                <textarea name="contenu" rows="4" required class="edc-input"
                    placeholder="Partagez votre expérience avec EDC..."></textarea>
                @error('contenu')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-primary w-full">
                ⭐ Soumettre mon avis
            </button>

            <p class="text-xs text-center mt-2" style="color: var(--edc-text-muted);">
                Votre avis sera publié après modération par l'administrateur.
            </p>
        </form>
    </div>

    {{-- MES AVIS --}}
    <div class="edc-card p-6">
        <h2 class="text-lg font-bold mb-5" style="color: var(--edc-text-primary);">📋 Mes avis soumis</h2>

        @forelse($temoignages as $temoignage)
        <div class="rounded-xl p-4 mb-3" style="border: 1px solid var(--edc-border);">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <div class="text-lg" style="color: var(--edc-accent-gold);">
                        {!! $temoignage->etoiles_html !!}
                    </div>
                    @if($temoignage->formation)
                    <p class="text-xs font-medium mt-1" style="color: var(--edc-primary-light);">
                        🎓 {{ $temoignage->formation->titre }}
                    </p>
                    @elseif($temoignage->service)
                    <p class="text-xs font-medium mt-1" style="color: var(--edc-secondary);">
                        💼 {{ $temoignage->service->titre }}
                    </p>
                    @endif
                </div>
                @php
                    $badgeStyle = match($temoignage->statut_validation) {
                        'valide'     => 'background-color: rgba(16,185,129,0.12); color: #34D399;',
                        'refuse'     => 'background-color: rgba(239,68,68,0.12); color: #F87171;',
                        default      => 'background-color: rgba(245,158,11,0.12); color: #FBBF24;',
                    };
                    $label = match($temoignage->statut_validation) {
                        'valide'     => '✅ Publié',
                        'refuse'     => '❌ Refusé',
                        default      => '⏳ En attente',
                    };
                @endphp
                <span class="badge text-xs" style="{{ $badgeStyle }}">
                    {{ $label }}
                </span>
            </div>
            <p class="text-sm leading-relaxed" style="color: var(--edc-text-secondary);">
                "{{ $temoignage->contenu }}"
            </p>
            <div class="flex justify-between items-center mt-3">
                <p class="text-xs" style="color: var(--edc-text-muted);">
                    {{ $temoignage->created_at->format('d/m/Y') }}
                </p>
                @if($temoignage->statut_validation === 'en_attente')
                <form method="POST"
                    action="{{ route('client.temoignages.destroy', $temoignage) }}"
                    onsubmit="return confirm('Supprimer cet avis ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs hover:underline" style="color: var(--edc-danger);">
                        🗑️ Supprimer
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-8" style="color: var(--edc-text-muted);">
            <p class="text-4xl mb-3">⭐</p>
            <p class="text-sm">Vous n'avez pas encore soumis d'avis.</p>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    const labels = ['', 'Mauvais (1/5)', 'Passable (2/5)', 'Bien (3/5)', 'Très bien (4/5)', 'Excellent (5/5)'];
    const stars = document.querySelectorAll('.star-btn');
    stars.forEach((star) => {
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
@extends('layouts.client')
@section('title', 'Mes Avis')

@section('content')

{{-- HEADER - Optimisé mobile --}}
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">
                ⭐ Mes Avis & Évaluations
            </h1>
            <p class="text-xs sm:text-sm mt-0.5" style="color: var(--edc-text-secondary);">
                Partagez votre expérience avec EDC
            </p>
        </div>
        
        {{-- Stats --}}
        @if($temoignages->count() > 0)
        <div class="flex items-center gap-2">
            <span class="text-xs px-3 py-1.5 rounded-full flex items-center gap-1.5"
                  style="background-color: rgba(245,158,11,0.1); color: var(--edc-accent-gold);">
                <span>⭐</span>
                <span>{{ $temoignages->count() }} avis</span>
            </span>
        </div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">

    {{-- FORMULAIRE D'AVIS --}}
    <div class="edc-card p-5 sm:p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm"
                 style="background-color: rgba(245,158,11,0.12);">
                ✍️
            </div>
            <h2 class="text-base sm:text-lg font-bold" style="color: var(--edc-text-primary);">
                Laisser un avis
            </h2>
        </div>

        <form method="POST" action="{{ route('client.temoignages.store') }}" id="avisForm">
            @csrf

            {{-- Formation --}}
            <div class="mb-4">
                <label class="edc-label flex items-center gap-1.5">
                    <span>🎓</span>
                    <span>Avis sur une formation</span>
                </label>
                <select name="formation_id" class="edc-select" onchange="toggleServiceSelect(this)">
                    <option value="">-- Aucune formation --</option>
                    @foreach($formations as $formation)
                    <option value="{{ $formation->id }}" {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                        {{ $formation->titre }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Service --}}
            <div class="mb-4">
                <label class="edc-label flex items-center gap-1.5">
                    <span>💼</span>
                    <span>Ou sur un service</span>
                </label>
                <select name="service_id" id="serviceSelect" class="edc-select" onchange="toggleFormationSelect(this)">
                    <option value="">-- Aucun service --</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                        {{ $service->titre }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Note étoiles - Version améliorée --}}
            <div class="mb-4">
                <label class="edc-label flex items-center gap-1.5">
                    <span>⭐</span>
                    <span>Note <span style="color: var(--edc-danger);">*</span></span>
                </label>
                
                <div class="flex items-center gap-1 sm:gap-2" id="starRating">
                    @for($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer group" for="star{{ $i }}">
                        <input type="radio" 
                               name="note" 
                               value="{{ $i }}" 
                               id="star{{ $i }}"
                               class="hidden peer" 
                               {{ $i == 5 ? 'checked' : '' }}>
                        <svg class="w-8 h-8 sm:w-10 sm:h-10 transition-all duration-200 hover:scale-110 active:scale-95 star-icon"
                             data-value="{{ $i }}"
                             fill="currentColor" 
                             viewBox="0 0 24 24"
                             style="color: var(--edc-accent-gold);">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </label>
                    @endfor
                </div>
                
                {{-- Label de la note --}}
                <div class="mt-2 flex items-center gap-2">
                    <span id="noteEmoji" class="text-xl">🌟</span>
                    <span id="noteLabel" class="text-sm font-semibold" style="color: var(--edc-text-secondary);">
                        Excellent (5/5)
                    </span>
                </div>
                
                @error('note')
                <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                    <span>⚠️</span><span>{{ $message }}</span>
                </p>
                @enderror
            </div>

            {{-- Contenu --}}
            <div class="mb-5">
                <label class="edc-label flex items-center gap-1.5">
                    <span>💬</span>
                    <span>Votre avis <span style="color: var(--edc-danger);">*</span></span>
                </label>
                <textarea name="contenu" 
                          rows="4" 
                          required 
                          class="edc-input resize-none @error('contenu') border-red-500 @enderror"
                          placeholder="Partagez votre expérience avec EDC..."
                          maxlength="500"
                          oninput="updateCharCount()">{{ old('contenu') }}</textarea>
                <div class="flex justify-between items-center mt-1.5">
                    @error('contenu')
                    <p class="text-red-400 text-xs flex items-center gap-1">
                        <span>⚠️</span><span>{{ $message }}</span>
                    </p>
                    @else
                    <span></span>
                    @enderror
                    <span id="charCount" class="text-[10px]" style="color: var(--edc-text-muted);">
                        0/500
                    </span>
                </div>
            </div>

            {{-- Bouton --}}
            <button type="submit" 
                    class="btn-primary w-full btn-touch text-sm font-bold group relative overflow-hidden">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <span class="transition-transform duration-300 group-hover:scale-125">⭐</span>
                    <span>Soumettre mon avis</span>
                </span>
            </button>

            <p class="text-xs text-center mt-3 flex items-center justify-center gap-1.5" 
               style="color: var(--edc-text-muted);">
                <span>🛡️</span>
                <span>Votre avis sera publié après modération.</span>
            </p>
        </form>
    </div>

    {{-- MES AVIS --}}
    <div class="edc-card p-5 sm:p-6">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm"
                     style="background-color: rgba(59,130,246,0.12);">
                    📋
                </div>
                <h2 class="text-base sm:text-lg font-bold" style="color: var(--edc-text-primary);">
                    Mes avis soumis
                </h2>
            </div>
            
            @if($temoignages->count() > 0)
            <span class="text-xs" style="color: var(--edc-text-muted);">
                {{ $temoignages->count() }}
            </span>
            @endif
        </div>

        @forelse($temoignages as $temoignage)
        <div class="rounded-xl p-4 mb-3 transition-all duration-200 hover:bg-white/[0.02]"
             style="border: 1px solid var(--edc-border);">
            
            {{-- En-tête : étoiles + statut --}}
            <div class="flex justify-between items-start mb-2 gap-2">
                <div>
                    {{-- Étoiles --}}
                    <div class="flex gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" 
                             fill="currentColor" 
                             viewBox="0 0 24 24"
                             style="color: {{ $i <= $temoignage->note ? 'var(--edc-accent-gold)' : 'var(--edc-bg-elevated)' }};">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        @endfor
                    </div>
                    
                    {{-- Formation ou Service --}}
                    @if($temoignage->formation)
                    <p class="text-xs font-medium mt-1.5 flex items-center gap-1"
                       style="color: var(--edc-primary-light);">
                        <span>🎓</span>
                        <span class="truncate max-w-[200px]">{{ $temoignage->formation->titre }}</span>
                    </p>
                    @elseif($temoignage->service)
                    <p class="text-xs font-medium mt-1.5 flex items-center gap-1"
                       style="color: var(--edc-secondary);">
                        <span>💼</span>
                        <span class="truncate max-w-[200px]">{{ $temoignage->service->titre }}</span>
                    </p>
                    @else
                    <p class="text-xs font-medium mt-1.5" style="color: var(--edc-text-muted);">
                        📝 Avis général
                    </p>
                    @endif
                </div>
                
                {{-- Badge statut --}}
                @php
                    $badgeStyle = match($temoignage->statut_validation) {
                        'valide'     => 'background-color: rgba(16,185,129,0.12); color: #34D399;',
                        'refuse'     => 'background-color: rgba(239,68,68,0.12); color: #F87171;',
                        default      => 'background-color: rgba(245,158,11,0.12); color: #FBBF24;',
                    };
                    $badgeIcon = match($temoignage->statut_validation) {
                        'valide'     => '✅',
                        'refuse'     => '❌',
                        default      => '⏳',
                    };
                    $badgeLabel = match($temoignage->statut_validation) {
                        'valide'     => 'Publié',
                        'refuse'     => 'Refusé',
                        default      => 'En attente',
                    };
                @endphp
                <span class="badge text-[10px] sm:text-xs flex-shrink-0 flex items-center gap-1"
                      style="{{ $badgeStyle }}">
                    <span>{{ $badgeIcon }}</span>
                    <span>{{ $badgeLabel }}</span>
                </span>
            </div>
            
            {{-- Contenu de l'avis --}}
            <p class="text-sm leading-relaxed italic" style="color: var(--edc-text-secondary);">
                "{{ $temoignage->contenu }}"
            </p>
            
            {{-- Pied : date + action --}}
            <div class="flex justify-between items-center mt-3 pt-2" 
                 style="border-top: 1px solid var(--edc-border);">
                <p class="text-xs flex items-center gap-1" style="color: var(--edc-text-muted);">
                    <span>📅</span>
                    <span>{{ $temoignage->created_at->format('d/m/Y à H:i') }}</span>
                </p>
                
                @if($temoignage->statut_validation === 'en_attente')
                <form method="POST"
                    action="{{ route('client.temoignages.destroy', $temoignage) }}"
                    onsubmit="return confirm('🗑️ Êtes-vous sûr de vouloir supprimer cet avis ?\n\nCette action est irréversible.')">
                    @csrf @method('DELETE')
                    <button type="submit" 
                            class="text-xs font-medium hover:underline transition-colors flex items-center gap-1 btn-touch px-2 py-1 rounded-lg"
                            style="color: var(--edc-danger);"
                            onmouseover="this.style.backgroundColor='rgba(239,68,68,0.08)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                        <span>🗑️</span>
                        <span>Supprimer</span>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-12 sm:py-16" style="color: var(--edc-text-muted);">
            <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-3xl sm:text-4xl"
                 style="background-color: var(--edc-bg-base);">
                ⭐
            </div>
            <p class="font-semibold text-sm sm:text-base mb-2" style="color: var(--edc-text-secondary);">
                Aucun avis pour le moment
            </p>
            <p class="text-xs sm:text-sm">
                Partagez votre première expérience avec EDC !
            </p>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Gestion des étoiles interactives
    const stars = document.querySelectorAll('.star-icon');
    const noteLabel = document.getElementById('noteLabel');
    const noteEmoji = document.getElementById('noteEmoji');
    
    const labels = {
        1: { text: 'Mauvais (1/5)', emoji: '😞' },
        2: { text: 'Passable (2/5)', emoji: '😐' },
        3: { text: 'Bien (3/5)', emoji: '🙂' },
        4: { text: 'Très bien (4/5)', emoji: '😊' },
        5: { text: 'Excellent (5/5)', emoji: '🌟' }
    };
    
    // Initialiser l'affichage
    const checkedStar = document.querySelector('input[name="note"]:checked');
    if (checkedStar) {
        updateStars(parseInt(checkedStar.value));
    }
    
    stars.forEach((star) => {
        star.addEventListener('click', function() {
            const val = parseInt(this.dataset.value);
            updateStars(val);
        });
        
        // Effet hover
        star.addEventListener('mouseenter', function() {
            const val = parseInt(this.dataset.value);
            previewStars(val);
        });
        
        star.addEventListener('mouseleave', function() {
            const checkedStar = document.querySelector('input[name="note"]:checked');
            if (checkedStar) {
                updateStars(parseInt(checkedStar.value));
            } else {
                resetStars();
            }
        });
    });
    
    function updateStars(val) {
        stars.forEach((star, i) => {
            star.style.opacity = i < val ? '1' : '0.25';
            star.style.transform = i < val ? 'scale(1)' : 'scale(0.9)';
        });
        noteLabel.textContent = labels[val].text;
        noteEmoji.textContent = labels[val].emoji;
    }
    
    function previewStars(val) {
        stars.forEach((star, i) => {
            star.style.opacity = i < val ? '1' : '0.25';
            star.style.transform = i < val ? 'scale(1.1)' : 'scale(0.9)';
        });
    }
    
    function resetStars() {
        stars.forEach((star) => {
            star.style.opacity = '1';
            star.style.transform = 'scale(1)';
        });
    }
    
    // Gestion des selects (formation/service) - un seul à la fois
    function toggleServiceSelect(formationSelect) {
        const serviceSelect = document.getElementById('serviceSelect');
        if (formationSelect.value) {
            serviceSelect.value = '';
        }
    }
    
    function toggleFormationSelect(serviceSelect) {
        const formationSelects = document.querySelectorAll('select[name="formation_id"]');
        if (serviceSelect.value) {
            formationSelects.forEach(s => s.value = '');
        }
    }
    
    // Compteur de caractères
    function updateCharCount() {
        const textarea = document.querySelector('textarea[name="contenu"]');
        const charCount = document.getElementById('charCount');
        const current = textarea.value.length;
        charCount.textContent = current + '/500';
        
        if (current > 450) {
            charCount.style.color = '#F59E0B';
        } else if (current >= 500) {
            charCount.style.color = '#EF4444';
        } else {
            charCount.style.color = '#64748B';
        }
    }
    
    // Initialiser le compteur
    document.addEventListener('DOMContentLoaded', function() {
        updateCharCount();
    });
</script>
@endpush
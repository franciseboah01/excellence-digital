@extends('layouts.client')
@section('title', 'Mes Certificats')

@section('content')
<div class="max-w-3xl mx-auto">
    
    {{-- HEADER - Optimisé mobile --}}
    <div class="mb-6 sm:mb-8 text-center">
        <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-3xl sm:text-4xl"
             style="background: linear-gradient(135deg, rgba(251,191,36,0.15), rgba(245,158,11,0.15)); 
                    border: 2px solid rgba(251,191,36,0.3);">
            🏆
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">
            Mes Certificats
        </h1>
        <p class="text-xs sm:text-sm mt-1" style="color: var(--edc-text-secondary);">
            Tous vos certificats et duplicatas obtenus
        </p>
        
        {{-- Stats --}}
        @if($certificats->count() > 0)
        <div class="flex items-center justify-center gap-3 mt-4">
            <span class="text-xs px-3 py-1.5 rounded-full flex items-center gap-1.5"
                  style="background-color: rgba(251,191,36,0.1); color: var(--edc-accent-gold);">
                <span>🏆</span>
                <span>{{ $certificats->count() }} certificat(s)</span>
            </span>
            
            @php
                $originaux = $certificats->where('est_duplicata', false)->count();
                $duplicatas = $certificats->where('est_duplicata', true)->count();
            @endphp
            
            @if($originaux > 0)
            <span class="text-xs px-3 py-1.5 rounded-full flex items-center gap-1.5"
                  style="background-color: rgba(59,130,246,0.1); color: #60A5FA;">
                <span>📜</span>
                <span>{{ $originaux }} original{{ $originaux > 1 ? 'ux' : '' }}</span>
            </span>
            @endif
            
            @if($duplicatas > 0)
            <span class="text-xs px-3 py-1.5 rounded-full flex items-center gap-1.5"
                  style="background-color: rgba(245,158,11,0.1); color: #FBBF24;">
                <span>🔄</span>
                <span>{{ $duplicatas }} duplicata</span>
            </span>
            @endif
        </div>
        @endif
    </div>

    {{-- LISTE DES CERTIFICATS --}}
    @if($certificats->count())
    <div class="space-y-3 sm:space-y-4">
        @foreach($certificats as $certificat)
        <div class="edc-card group transition-all duration-300 overflow-hidden"
             style="border-left: 4px solid {{ $certificat->est_duplicata ? 'var(--edc-accent-gold)' : 'var(--edc-primary)' }};">
            
            <div class="p-4 sm:p-5">
                {{-- En-tête : Titre + Badge --}}
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg">{{ $certificat->est_duplicata ? '🔄' : '📜' }}</span>
                            <h3 class="font-bold text-sm sm:text-base line-clamp-2" style="color: var(--edc-text-primary);">
                                {{ $certificat->formation->titre }}
                            </h3>
                        </div>
                        
                        {{-- Numéro de certificat --}}
                        <p class="font-mono text-[10px] sm:text-xs mt-1 flex items-center gap-2"
                           style="color: var(--edc-text-muted);">
                            <span>🔢 N° {{ $certificat->numero_certificat }}</span>
                            <span class="w-1 h-1 rounded-full" style="background-color: var(--edc-border);"></span>
                            <span>{{ $certificat->delivre_le ? $certificat->delivre_le->format('d/m/Y') : 'En attente' }}</span>
                        </p>
                    </div>
                    
                    {{-- Badge type --}}
                    <span class="badge text-[10px] sm:text-xs flex-shrink-0 flex items-center gap-1"
                          style="{{ $certificat->est_duplicata 
                              ? 'background-color: rgba(245,158,11,0.15); color: #FBBF24;' 
                              : 'background-color: rgba(59,130,246,0.15); color: #60A5FA;' }}">
                        <span>{{ $certificat->est_duplicata ? '🔄' : '📜' }}</span>
                        <span>{{ $certificat->est_duplicata ? 'Duplicata' : 'Original' }}</span>
                    </span>
                </div>
                
                {{-- Infos --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-3 mb-4">
                    {{-- Note --}}
                    <div class="rounded-xl p-2.5 sm:p-3 text-center"
                         style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
                        <p class="text-[10px] font-medium" style="color: var(--edc-text-muted);">📊 Note</p>
                        <p class="text-lg sm:text-xl font-extrabold" 
                           style="color: {{ $certificat->note_obtenue >= 14 ? '#34D399' : ($certificat->note_obtenue >= 10 ? '#FBBF24' : '#F87171') }};">
                            {{ $certificat->note_obtenue }}/20
                        </p>
                    </div>
                    
                    {{-- Mention --}}
                    <div class="rounded-xl p-2.5 sm:p-3 text-center"
                         style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
                        <p class="text-[10px] font-medium" style="color: var(--edc-text-muted);">🎖️ Mention</p>
                        <p class="text-xs sm:text-sm font-bold truncate" style="color: var(--edc-text-primary);">
                            @php
                                $note = $certificat->note_obtenue;
                                $mention = $note >= 18 ? '🏅 Excellence' : 
                                          ($note >= 16 ? '🌟 Très Bien' : 
                                          ($note >= 14 ? '👍 Bien' : 
                                          ($note >= 12 ? '✅ Assez Bien' : 
                                          ($note >= 10 ? '✔️ Passable' : '❌ Insuffisant'))));
                            @endphp
                            {{ $mention }}
                        </p>
                    </div>
                    
                    {{-- Statut téléchargement --}}
                    <div class="rounded-xl p-2.5 sm:p-3 text-center hidden sm:block"
                         style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
                        <p class="text-[10px] font-medium" style="color: var(--edc-text-muted);">💾 Statut</p>
                        <p class="text-xs sm:text-sm font-bold" 
                           style="color: {{ $certificat->telecharge ? '#34D399' : '#F59E0B' }};">
                            {{ $certificat->telecharge ? '✅ Téléchargé' : '⏳ Non téléchargé' }}
                        </p>
                    </div>
                </div>
                
                {{-- Actions --}}
                <div class="flex flex-wrap items-center gap-2 pt-3" 
                     style="border-top: 1px solid var(--edc-border);">
                    
                    {{-- Télécharger PDF / JPG --}}
                    @if($certificat->est_telechargeable)
                        <a href="{{ route('client.certificats.telecharger', ['certificat' => $certificat, 'format' => 'pdf']) }}"
                           class="btn-touch inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all duration-300 hover:scale-105 active:scale-95"
                           style="background: linear-gradient(135deg, #FBBF24, #F59E0B); color: #1a1a1a;">
                            <span>📄</span>
                            <span>PDF</span>
                        </a>
                        <a href="{{ route('client.certificats.telecharger', ['certificat' => $certificat, 'format' => 'jpg']) }}"
                           class="btn-touch inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all duration-300 hover:scale-105 active:scale-95"
                           style="background: linear-gradient(135deg, #6B7280, #4B5563); color: white;">
                            <span>🖼️</span>
                            <span>JPG</span>
                        </a>
                    @endif

                    {{-- Demande Duplicata --}}
                    @if(!$certificat->est_duplicata && $certificat->telecharge)
                        @php
                            $demandeActive = $certificat->demandesDuplicata->first();
                        @endphp

                        @if(!$certificat->demande_existante && !$certificat->duplicata_existant)
                            <form method="POST" action="{{ route('client.certificats.demande-duplicata', $certificat) }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="btn-touch inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all duration-300 hover:scale-105 active:scale-95"
                                    style="color: var(--edc-accent-gold); background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3);"
                                    onclick="return confirm('⚠️ Confirmer la demande de duplicata ?\n\nUn paiement de {{ number_format($certificat->prix_duplicata, 0, ',', ' ') }} FCFA sera requis.\n\nContinuer ?');">
                                    <span>🔄</span>
                                    <span>Duplicata ({{ number_format($certificat->prix_duplicata, 0, ',', ' ') }} FCFA)</span>
                                </button>
                            </form>
                        @elseif($demandeActive && $demandeActive->statut === 'en_attente')
                            <a href="{{ route('client.paiement.form', ['type' => 'duplicata', 'id' => $certificat->id]) }}"
                               class="btn-touch inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-300 hover:scale-105 active:scale-95 animate-pulse-soft"
                               style="background: #F59E0B; color: #1a1a1a;">
                                <span>💳</span>
                                <span>Finaliser le paiement</span>
                            </a>
                        @elseif($demandeActive && $demandeActive->statut === 'paye')
                            <span class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl text-xs font-bold"
                                  style="background: rgba(59,130,246,0.15); color: #60A5FA; border: 1px solid rgba(59,130,246,0.3);">
                                <span>⏳</span>
                                <span>En attente de validation</span>
                            </span>
                        @elseif($certificat->duplicata_existant)
                            <span class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl text-xs font-bold"
                                  style="background: rgba(16,185,129,0.15); color: #34D399; border: 1px solid rgba(16,185,129,0.3);">
                                <span>✅</span>
                                <span>Duplicata disponible</span>
                            </span>
                        @endif
                    @endif

                    {{-- Duplicata déjà téléchargé --}}
                    @if($certificat->est_duplicata && $certificat->telecharge)
                        <span class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium"
                              style="color: var(--edc-text-muted);">
                            <span>📄</span>
                            <span>Déjà téléchargé</span>
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    {{-- Message d'aide --}}
    <div class="mt-6 text-center">
        <p class="text-xs" style="color: var(--edc-text-muted);">
            💡 Besoin d'un duplicata ? Cliquez sur "Duplicata" pour un certificat déjà téléchargé.
        </p>
    </div>
    
    @else
    {{-- État vide --}}
    <div class="edc-card text-center py-16 sm:py-20 px-4" style="color: var(--edc-text-muted);">
        <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-3xl sm:text-4xl"
             style="background-color: var(--edc-bg-base);">
            📜
        </div>
        <p class="font-semibold text-sm sm:text-base mb-2" style="color: var(--edc-text-secondary);">
            Aucun certificat pour le moment
        </p>
        <p class="text-xs sm:text-sm mb-6">
            Réussissez les QCMs de vos formations pour obtenir vos certificats !
        </p>
        <a href="{{ route('client.qcms.index') }}" 
           class="btn-primary btn-touch inline-flex items-center gap-2">
            <span>📝</span>
            <span>Voir les QCMs disponibles</span>
        </a>
    </div>
    @endif
</div>
@endsection
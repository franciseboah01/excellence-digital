@php
    $config = match($ressource->type) {
        'pdf'      => ['rgba(239,68,68,0.08)', 'rgba(239,68,68,0.25)',  '📄', '#F87171', 'PDF'],
        'ebook'    => ['rgba(168,85,247,0.08)', 'rgba(168,85,247,0.25)', '📖', '#C084FC', 'E-Book'],
        'lien'     => ['rgba(16,185,129,0.08)', 'rgba(16,185,129,0.25)', '🔗', '#34D399', 'Lien'],
        'video'    => ['rgba(245,158,11,0.08)', 'rgba(245,158,11,0.25)', '🎬', '#FBBF24', 'Vidéo'],
        'document' => ['rgba(59,130,246,0.08)', 'rgba(59,130,246,0.25)', '📝', '#60A5FA', 'Document'],
        default    => ['rgba(148,163,184,0.08)', 'rgba(148,163,184,0.25)', '📎', '#94A3B8', 'Fichier'],
    };

    $infos = $ressource->fichier_path
        ? \App\Services\FichierService::infos($ressource->fichier_path)
        : [];
@endphp

<div class="ressource-card group rounded-xl p-3 sm:p-4 transition-all duration-300 hover:scale-[1.02] active:scale-[0.98] relative overflow-hidden"
    style="background-color: {{ $config[0] }}; border: 1px solid {{ $config[1] }}; touch-action: manipulation;">
    
    {{-- Fond décoratif --}}
    <div class="absolute top-0 right-0 w-20 h-20 opacity-5 transition-opacity duration-300 group-hover:opacity-10"
         style="background: radial-gradient(circle, {{ $config[3] }}, transparent);">
    </div>
    
    <div class="relative z-10">
        {{-- En-tête : Type + Taille --}}
        <div class="flex items-center justify-between mb-2">
            <span class="badge text-[10px] font-medium flex items-center gap-1"
                  style="background-color: rgba(255,255,255,0.05); color: {{ $config[3] }};">
                <span>{{ $config[2] }}</span>
                <span>{{ $config[4] }}</span>
            </span>
            
            @if(!empty($infos))
            <span class="text-[10px] font-medium flex items-center gap-1"
                  style="color: var(--edc-text-muted);">
                <span>📦</span>
                <span>{{ $infos['taille_mb'] }} MB</span>
            </span>
            @endif
        </div>
        
        {{-- Icône principale --}}
        <div class="flex items-start gap-2.5 sm:gap-3">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center text-xl sm:text-2xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110"
                 style="background-color: rgba(255,255,255,0.03);">
                {{ $config[2] }}
            </div>
            
            <div class="flex-1 min-w-0">
                {{-- Titre --}}
                <p class="font-semibold text-sm line-clamp-2" style="color: var(--edc-text-primary);">
                    {{ $ressource->titre }}
                </p>
                
                {{-- Description --}}
                @if($ressource->description)
                <p class="text-xs mt-1 line-clamp-2" style="color: var(--edc-text-secondary);">
                    {{ Str::limit($ressource->description, 60) }}
                </p>
                @endif
                
                {{-- Extension du fichier --}}
                @if(!empty($infos))
                <p class="text-[10px] mt-1.5 font-mono uppercase tracking-wider" 
                   style="color: var(--edc-text-muted);">
                    .{{ $infos['extension'] }}
                </p>
                @endif
            </div>
        </div>
        
        {{-- Bouton d'action --}}
        <div class="mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.05);">
            @if(in_array($ressource->type, ['lien', 'video']))
                <a href="{{ $ressource->lien_url }}" 
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn-touch inline-flex items-center justify-center gap-2 w-full rounded-xl py-2.5 text-xs font-semibold transition-all duration-300 hover:brightness-110"
                   style="background-color: {{ $config[3] }}15; color: {{ $config[3] }}; border: 1px solid {{ $config[1] }};">
                    <span>{{ $config[2] }}</span>
                    <span>Ouvrir le lien</span>
                    <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-0.5" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            @elseif($ressource->fichier_path)
                <button onclick="ouvrirFichierSecurise({{ $ressource->id }}, '{{ $ressource->type }}')"
                        class="btn-touch w-full rounded-xl py-2.5 text-xs font-semibold transition-all duration-300 hover:brightness-110 flex items-center justify-center gap-2"
                        style="background-color: {{ $config[3] }}15; color: {{ $config[3] }}; border: 1px solid {{ $config[1] }};">
                        @if($ressource->type === 'pdf')
                            <span>📖</span>
                            <span>Lire le PDF</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-0.5" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        @else
                            <span>📥</span>
                            <span>Télécharger</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-y-0.5" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        @endif
                </button>
            @else
                <div class="text-center py-2">
                    <p class="text-[10px]" style="color: var(--edc-text-muted);">
                        Fichier non disponible
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
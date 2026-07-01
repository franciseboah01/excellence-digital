@extends('layouts.client')
@section('title', 'Mes Certificats')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8 text-center">
        <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">🏆 Mes Certificats</h1>
        <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">
            Tous vos certificats obtenus
        </p>
    </div>

    @if($certificats->count())
    <div class="space-y-3">
        @foreach($certificats as $certificat)
        <div class="edc-card p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="min-w-0">
                <p class="font-bold truncate" style="color: var(--edc-text-primary);">{{ $certificat->formation->titre }}</p>
                <p class="text-xs mt-0.5" style="color: var(--edc-text-muted);">
                    N° {{ $certificat->numero_certificat }}
                    @if($certificat->est_duplicata)
                        <span class="badge badge-warning text-[10px] ml-1" style="background: #F59E0B; color: #1a1a1a; padding: 1px 8px; border-radius: 9999px; font-weight: 600;">Duplicata</span>
                    @else
                        <span class="badge badge-primary text-[10px] ml-1" style="background: #3B82F6; color: white; padding: 1px 8px; border-radius: 9999px; font-weight: 600;">Original</span>
                    @endif
                    •
                    {{ $certificat->delivre_le ? $certificat->delivre_le->format('d/m/Y') : 'En attente' }} •
                    Note : {{ $certificat->note_obtenue }}/20
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- ===== TÉLÉCHARGER (PDF / JPG) ===== --}}
                @if($certificat->est_telechargeable)
                    <a href="{{ route('client.certificats.telecharger', ['certificat' => $certificat, 'format' => 'pdf']) }}"
                        class="btn-xs rounded-lg text-xs font-bold transition px-3 py-1.5"
                        style="background: linear-gradient(135deg, #FBBF24, #F59E0B); color: #1a1a1a;">
                        📄 PDF
                    </a>
                    <a href="{{ route('client.certificats.telecharger', ['certificat' => $certificat, 'format' => 'jpg']) }}"
                        class="btn-xs rounded-lg text-xs font-bold transition px-3 py-1.5"
                        style="background: linear-gradient(135deg, #6B7280, #4B5563); color: white;">
                        🖼️ JPG
                    </a>
                @endif

                {{-- ===== DEMANDE DUPLICATA (Original déjà téléchargé) ===== --}}
                @if(!$certificat->est_duplicata && $certificat->telecharge)
                    @php
                        // On récupère la demande active (si elle existe) pour distinguer
                        // "en attente de paiement" de "payé, en attente de validation admin".
                        $demandeActive = $certificat->demandesDuplicata->first();
                    @endphp

                    @if(!$certificat->demande_existante && !$certificat->duplicata_existant)
                        <form method="POST" action="{{ route('client.certificats.demande-duplicata', $certificat) }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="text-xs font-bold px-3 py-1.5 rounded-lg transition"
                                style="color: var(--edc-accent-gold); background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3);"
                                onclick="return confirm('⚠️ Confirmer la demande de duplicata ? Un paiement de {{ number_format($certificat->prix_duplicata, 0, ',', ' ') }} FCFA sera requis.');">
                                🔄 Duplicata ({{ number_format($certificat->prix_duplicata, 0, ',', ' ') }} FCFA)
                            </button>
                        </form>
                    @elseif($demandeActive && $demandeActive->statut === 'en_attente')
                        {{-- Demande créée mais paiement pas encore effectué : proposer de reprendre le paiement.
                             ✅ L'ID du certificat est passé directement dans l'URL (plus de session),
                             donc ce lien fonctionne même après une longue absence ou une session perdue. --}}
                        <a href="{{ route('client.paiement.form', ['type' => 'duplicata', 'id' => $certificat->id]) }}"
                           class="badge badge-warning text-xs" style="background: #F59E0B; color: #1a1a1a; padding: 2px 12px; border-radius: 9999px; font-weight: 600; text-decoration: none;">
                            💳 Finaliser le paiement
                        </a>
                    @elseif($demandeActive && $demandeActive->statut === 'paye')
                        <span class="badge badge-info text-xs" style="background: #3B82F6; color: white; padding: 2px 12px; border-radius: 9999px; font-weight: 600;">⏳ Payé, en attente de validation</span>
                    @elseif($certificat->duplicata_existant)
                        <span class="badge badge-success text-xs" style="background: #10B981; color: white; padding: 2px 12px; border-radius: 9999px; font-weight: 600;">✅ Duplicata disponible</span>
                    @endif
                @endif

                {{-- ===== DUPLICATA DÉJÀ TÉLÉCHARGÉ ===== --}}
                @if($certificat->est_duplicata && $certificat->telecharge)
                    <span class="text-xs" style="color: var(--edc-text-muted);">📄 Téléchargé</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="edc-card text-center py-12" style="color: var(--edc-text-muted);">
        <p class="text-5xl mb-4">📜</p>
        <p>Vous n'avez pas encore de certificats.</p>
        <p class="text-sm mt-1">Réussissez des QCMs pour obtenir vos certificats !</p>
        <a href="{{ route('client.qcms.index') }}" class="btn-primary btn-sm mt-4 inline-block">Voir les QCMs disponibles</a>
    </div>
    @endif
</div>
@endsection
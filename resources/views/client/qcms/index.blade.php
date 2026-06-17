@extends('layouts.client')
@section('title', 'QCMs & Certificats')

@php
    use App\Models\DemandeDuplicata;
    use App\Models\Certificat;
    use App\Models\Configuration;
@endphp

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8 text-center">
        <h1 class="text-xl sm:text-2xl font-extrabold" style="color: var(--edc-text-primary);">🎓 QCMs & Certificats</h1>
        <p class="text-sm mt-1" style="color: var(--edc-text-secondary);">
            Testez vos connaissances et obtenez vos certificats
        </p>
    </div>

    {{-- CERTIFICATS OBTENUS --}}
    @if($certificats->count())
    <div class="edc-card p-6 mb-6">
        <h2 class="text-lg font-bold mb-4" style="color: var(--edc-text-primary);">🏆 Mes Certificats obtenus</h2>
        <div class="space-y-3">
            @foreach($certificats as $certificat)
            <div class="rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                style="background-color: var(--edc-bg-base); border: 1px solid var(--edc-border);">
                <div class="min-w-0">
                    <p class="font-bold truncate" style="color: var(--edc-text-primary);">{{ $certificat->formation->titre }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--edc-text-muted);">
                        N° {{ $certificat->numero_certificat }}
                        @if(str_ends_with($certificat->numero_certificat, '-DUP'))
                            <span class="badge badge-warning text-[10px] ml-1" style="background: #F59E0B; color: #1a1a1a; padding: 1px 8px; border-radius: 9999px; font-weight: 600;">Duplicata</span>
                        @else
                            <span class="badge badge-primary text-[10px] ml-1" style="background: #3B82F6; color: white; padding: 1px 8px; border-radius: 9999px; font-weight: 600;">Original</span>
                        @endif
                        •
                        {{ $certificat->delivre_le->format('d/m/Y') }} •
                        Note : {{ $certificat->note_obtenue }}/20
                    </p>
                </div>

                {{-- STATUT TÉLÉCHARGEMENT --}}
                <div class="flex flex-col items-end gap-2">
                    @if(!$certificat->telecharge)
                        <span class="badge badge-success text-xs" style="background: #10B981; color: white; padding: 2px 12px; border-radius: 9999px; font-weight: 600;">✅ Téléchargeable</span>
                    @else
                        <span class="badge badge-danger text-xs" style="background: #EF4444; color: white; padding: 2px 12px; border-radius: 9999px; font-weight: 600;">❌ Déjà téléchargé</span>
                    @endif
                </div>

                {{-- ACTIONS --}}
                <div class="flex flex-wrap items-center gap-2 mt-2 sm:mt-0">
                    @if(!$certificat->telecharge)
                        {{-- Téléchargement PDF --}}
                        <a href="{{ route('client.certificats.telecharger', ['certificat' => $certificat, 'format' => 'pdf']) }}"
                            class="btn-xs rounded-lg text-xs font-bold transition px-3 py-1.5"
                            style="background: linear-gradient(135deg, #FBBF24, #F59E0B); color: #1a1a1a;"
                            onmouseover="this.style.filter='brightness(1.1)'"
                            onmouseout="this.style.filter='brightness(1)'">
                            📄 PDF
                        </a>
                        {{-- Téléchargement JPG --}}
                        <a href="{{ route('client.certificats.telecharger', ['certificat' => $certificat, 'format' => 'jpg']) }}"
                            class="btn-xs rounded-lg text-xs font-bold transition px-3 py-1.5"
                            style="background: linear-gradient(135deg, #6B7280, #4B5563); color: white;"
                            onmouseover="this.style.filter='brightness(1.1)'"
                            onmouseout="this.style.filter='brightness(1)'">
                            🖼️ JPG
                        </a>

                    @elseif(!str_ends_with($certificat->numero_certificat, '-DUP'))
                        {{-- Original déjà téléchargé : Demander duplicata --}}
                        @php
                            $demandeExistante = DemandeDuplicata::where('certificat_id', $certificat->id)
                                ->whereIn('statut', ['en_attente', 'valide'])
                                ->exists();
                            $duplicataExistant = Certificat::where('parent_id', $certificat->id)
                                ->where('telecharge', false)
                                ->exists();
                            $prixDuplicata = Configuration::get('duplicata_prix', 1000);
                        @endphp

                        @if(!$demandeExistante && !$duplicataExistant)
                            <form method="POST" action="{{ route('client.duplicata.demander', $certificat) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                    class="text-xs font-bold hover:underline px-3 py-1.5 rounded-lg transition"
                                    style="color: var(--edc-accent-gold); background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3);"
                                    onmouseover="this.style.background='rgba(245,158,11,0.2)'"
                                    onmouseout="this.style.background='rgba(245,158,11,0.1)'">
                                    🔄 Duplicata ({{ number_format($prixDuplicata, 0, ',', ' ') }} FCFA)
                                </button>
                            </form>
                        @elseif($demandeExistante)
                            <span class="badge badge-info text-xs" style="background: #3B82F6; color: white; padding: 2px 12px; border-radius: 9999px; font-weight: 600;">⏳ Demande en cours</span>
                        @elseif($duplicataExistant)
                            <span class="badge badge-success text-xs" style="background: #10B981; color: white; padding: 2px 12px; border-radius: 9999px; font-weight: 600;">✅ Duplicata disponible</span>
                        @endif

                    @else
                        {{-- Duplicata déjà téléchargé --}}
                        <span class="text-xs" style="color: var(--edc-text-muted);">Téléchargé</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- QCMS DISPONIBLES --}}
    <div class="space-y-5">
        @forelse($qcms as $qcm)
        <div class="edc-card overflow-hidden">
            <div class="p-5">
                <div class="flex justify-between items-start mb-3">
                    <span class="badge badge-blue">
                        🎓 {{ $qcm->formation->titre }}
                    </span>
                    @if($qcm->deja_reussi)
                    <span class="badge badge-green font-bold">
                        🏆 Réussi !
                    </span>
                    @elseif($qcm->tentatives_faites > 0)
                    <span class="badge badge-gold">
                        🔄 {{ $qcm->tentatives_faites }}/{{ $qcm->tentatives_max }} tentatives
                    </span>
                    @endif
                </div>

                <h3 class="font-bold mb-1" style="color: var(--edc-text-primary);">{{ $qcm->titre }}</h3>
                @if($qcm->niveau)
                <p class="text-xs" style="color: var(--edc-text-muted);">📂 {{ $qcm->niveau->nom }}</p>
                @endif

                <div class="grid grid-cols-3 gap-2 mt-3 text-center">
                    <div class="rounded-lg p-2" style="background-color: rgba(59,130,246,0.06);">
                        <p class="text-xs" style="color: var(--edc-text-muted);">Questions</p>
                        <p class="font-bold" style="color: var(--edc-primary-light);">{{ $qcm->questions_count }}</p>
                    </div>
                    <div class="rounded-lg p-2" style="background-color: rgba(16,185,129,0.06);">
                        <p class="text-xs" style="color: var(--edc-text-muted);">Note min.</p>
                        <p class="font-bold" style="color: var(--edc-secondary);">{{ $qcm->note_minimale }}/20</p>
                    </div>
                    <div class="rounded-lg p-2" style="background-color: rgba(245,158,11,0.06);">
                        <p class="text-xs" style="color: var(--edc-text-muted);">Durée/Q</p>
                        <p class="font-bold" style="color: var(--edc-accent-gold);">{{ $qcm->duree_par_question }}s</p>
                    </div>
                </div>

                @if($qcm->meilleure_note)
                <div class="mt-3">
                    <div class="flex justify-between text-xs mb-1" style="color: var(--edc-text-muted);">
                        <span>Meilleure note</span>
                        <span>{{ $qcm->meilleure_note }}/20</span>
                    </div>
                    <div class="w-full rounded-full h-2" style="background-color: var(--edc-bg-elevated);">
                        <div class="h-2 rounded-full transition-all"
                            style="width: {{ ($qcm->meilleure_note / 20) * 100 }}%;
                            {{ $qcm->deja_reussi ? 'background: linear-gradient(135deg, #10B981, #059669);' : 'background: linear-gradient(135deg, #3B82F6, #1D4ED8);' }}">
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="px-5 py-3" style="border-top: 1px solid var(--edc-border); background-color: var(--edc-bg-base);">
                @if($qcm->deja_reussi)
                <p class="text-center text-sm font-semibold" style="color: var(--edc-secondary);">
                    🎓 Certificat obtenu !
                </p>
                @elseif($qcm->peut_repasser)
                <a href="{{ route('client.qcms.demarrer', $qcm) }}"
                    class="btn-primary btn-sm w-full text-center">
                    {{ $qcm->tentatives_faites > 0 ? '🔄 Repasser le QCM' : '▶️ Commencer le QCM' }}
                </a>
                @else
                <p class="text-center text-sm" style="color: var(--edc-danger);">
                    ❌ Tentatives épuisées ({{ $qcm->tentatives_max }}/{{ $qcm->tentatives_max }})
                </p>
                @endif
            </div>
        </div>
        @empty
        <div class="edc-card text-center py-12" style="color: var(--edc-text-muted);">
            <p class="text-5xl mb-4">📝</p>
            <p>Aucun QCM disponible pour vos formations.</p>
            <p class="text-sm mt-1">Les enseignants ajouteront des QCMs prochainement.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
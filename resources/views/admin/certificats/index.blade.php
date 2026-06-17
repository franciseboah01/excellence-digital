@extends('layouts.admin')

@section('title', 'Certificats')
@section('page_title', '🏆 Certificats délivrés')

@php
    use App\Models\DemandeDuplicata;
    use App\Models\Certificat;
@endphp

@section('content')

<div class="grid grid-cols-3 gap-4 mt-6">
    @foreach([
        ['total', '🏆 Total certificats', 'var(--edc-accent-gold)'],
        ['ce_mois', '📅 Ce mois-ci', 'var(--edc-secondary)'],
        ['moyenne', '📊 Note moyenne', 'var(--edc-primary)'],
        ['duplicatas', '📄 Duplicatas', 'var(--edc-accent-gold)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">
            {{ $stat[0] === 'moyenne' ? ($stats[$stat[0]] ?? '0') . '/20' : ($stats[$stat[0]] ?? 0) }}
        </p>
        <p class="stat-label">
            {{ $stat[1] }}
        </p>
    </div>
    @endforeach
</div>

<div class="edc-card mt-5 overflow-hidden">
    <div class="table-responsive">
        <table class="admin-table w-full">
            <thead>
                <tr>
                    <th>N° Certificat</th>
                    <th>Type</th>
                    <th>Apprenant</th>
                    <th>Formation</th>
                    <th>Note</th>
                    <th>Téléchargé</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($certificats as $cert)
                <tr>
                    <td class="font-mono text-xs font-bold" style="color: var(--edc-primary-light);">
                        {{ $cert->numero_certificat ?? $cert->code_verification ?? $cert->id }}
                    </td>

                    <td>
                        @if(str_ends_with($cert->numero_certificat, '-DUP'))
                            <span class="text-[10px] px-1.5 py-0.5 rounded font-sans" style="background-color: rgba(212,163,89,0.15); color: var(--edc-accent-gold);">Duplicata</span>
                        @else
                            <span class="text-[10px] px-1.5 py-0.5 rounded font-sans" style="background-color: rgba(59,130,246,0.15); color: #3B82F6;">Original</span>
                        @endif
                    </td>

                    <td class="font-medium" style="color: var(--edc-text-primary);">
                        {{ $cert->user?->prenom ?? '—' }} {{ $cert->user?->nom ?? '' }}
                    </td>

                    <td class="text-xs" style="color: var(--edc-text-secondary);">
                        {{ $cert->formation?->titre ?? '—' }}
                    </td>

                    <td>
                        <span class="font-bold" style="color: var(--edc-secondary);">
                            {{ $cert->note_obtenue ?? 0 }}/20
                        </span>
                    </td>

                    <td>
                        @if(!$cert->telecharge)
                            <span style="color: var(--edc-secondary);">✅ Non</span>
                        @else
                            <span style="color: var(--edc-text-muted);">✅ Oui</span>
                        @endif
                    </td>

                    <td class="text-xs" style="color: var(--edc-text-muted);">
                        {{ $cert->delivre_le ? $cert->delivre_le->format('d/m/Y') : ($cert->created_at ? $cert->created_at->format('d/m/Y') : '—') }}
                    </td>

                    <td>
                        <div class="flex items-center gap-3 whitespace-nowrap flex-wrap">

                            {{-- Télécharger le certificat --}}
                            <a href="{{ route('certificats.telecharger', ['certificat' => $cert, 'format' => 'pdf']) }}"
                                class="text-xs font-medium hover:underline"
                                style="color: var(--edc-primary-light);">
                                📄 PDF
                            </a>

                            @if(!$cert->telecharge)
                                <a href="{{ route('certificats.telecharger', ['certificat' => $cert, 'format' => 'jpg']) }}"
                                    class="text-xs font-medium hover:underline"
                                    style="color: #6B7280;">
                                    🖼️ JPG
                                </a>
                            @endif

                            {{-- Générer manuellement un duplicata (admin direct) --}}
                            @if(!str_ends_with($cert->numero_certificat, '-DUP'))
                                <form method="POST"
                                    action="{{ route('admin.certificats.duplicata', $cert) }}"
                                    class="inline"
                                    onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir générer un duplicata officiel pour cet apprenant ?');">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs font-medium hover:underline"
                                            style="color: var(--edc-accent-gold); cursor:pointer; background:none; border:none; padding:0;">
                                        🔄 Duplicata
                                    </button>
                                </form>
                            @endif

                            {{-- Valider un duplicata payé en attente --}}
                            @if(str_ends_with($cert->numero_certificat, '-DUP') && $cert->telecharge)
                                @php
                                    $demande = DemandeDuplicata::where('certificat_id', $cert->parent_id)
                                        ->where('statut', 'en_attente')
                                        ->first();
                                @endphp
                                @if($demande)
                                    <form action="{{ route('admin.duplicatas.valider', $demande) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-xs font-medium hover:underline text-emerald-500 hover:text-emerald-600" 
                                                style="cursor:pointer; background:none; border:none; padding:0;">
                                            ✅ Valider
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.duplicatas.rejeter', $demande) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-xs font-medium hover:underline text-red-500 hover:text-red-600" 
                                                style="cursor:pointer; background:none; border:none; padding:0;"
                                                onclick="return confirm('⚠️ Confirmer le rejet de cette demande ?');">
                                            ❌ Rejeter
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-500">✅ Validé</span>
                                @endif
                            @endif

                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center" style="color: var(--edc-text-muted);">
                        Aucun certificat trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    <div class="px-6 py-4" style="border-top: 1px solid var(--edc-border);">
        {{ $certificats->links() }}
    </div>
</div>

@endsection
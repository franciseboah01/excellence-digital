@extends('layouts.admin')

@section('title', 'Certificats')
@section('page_title', '🏆 Certificats délivrés')

@section('content')

<div class="grid grid-cols-3 gap-4 mt-6">
    @foreach([
        ['total', '🏆 Total certificats', 'var(--edc-accent-gold)'],
        ['ce_mois', '📅 Ce mois-ci', 'var(--edc-secondary)'],
        ['moyenne', '📊 Note moyenne', 'var(--edc-primary)'],
    ] as $stat)
    <div class="stat-card" style="border-left-color: {{ $stat[2] }};">
        <p class="stat-value">
            {{ $stat[0] === 'moyenne' ? $stats[$stat[0]].'/20' : $stats[$stat[0]] }}
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
                    <td class="font-mono text-xs font-bold" 
                        style="color: var(--edc-primary-light);">
                        {{ $cert->numero_certificat }}
                    </td>

                    <td class="font-medium" 
                        style="color: var(--edc-text-primary);">
                        {{ $cert->user->prenom }} {{ $cert->user->nom }}
                    </td>

                    <td class="text-xs" 
                        style="color: var(--edc-text-secondary);">
                        {{ $cert->formation->titre }}
                    </td>

                    <td>
                        <span class="font-bold" 
                              style="color: var(--edc-secondary);">
                            {{ $cert->note_obtenue }}/20
                        </span>
                    </td>

                    <td>
                        @if($cert->telecharge)
                            <span style="color: var(--edc-secondary);">
                                ✅ Oui
                            </span>
                        @else
                            <span style="color: var(--edc-text-muted);">
                                ❌ Non
                            </span>
                        @endif
                    </td>

                    <td class="text-xs" 
                        style="color: var(--edc-text-muted);">
                        {{ $cert->delivre_le->format('d/m/Y') }}
                    </td>

                    <td>
                        <div class="flex items-center gap-3 whitespace-nowrap">

                            {{-- Télécharger le certificat --}}
                            <a 
                                href="{{ route('certificats.telecharger', $cert) }}"
                                class="text-xs font-medium hover:underline"
                                style="color: var(--edc-primary-light);">
                                📄 Télécharger
                            </a>

                            {{-- Créer un duplicata --}}
                            <form 
                                method="POST"
                                action="{{ route('admin.certificats.duplicata', $cert) }}"
                                class="inline">
                                
                                @csrf

                                <button 
                                    type="submit"
                                    class="text-xs font-medium hover:underline"
                                    style="color: var(--edc-accent-gold); cursor:pointer;">
                                    
                                    🔄 Duplicata
                                </button>

                            </form>

                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center"
                        style="color: var(--edc-text-muted);">
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
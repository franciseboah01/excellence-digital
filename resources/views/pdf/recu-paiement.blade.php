<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size:12px; color:#1f2937; background:white; }
        .page { padding:30px; }

        /* HEADER */
        .header { background:#1e3a8a; color:white; padding:20px 24px; border-radius:8px; margin-bottom:20px; }
        .header h1 { font-size:20px; font-weight:900; letter-spacing:2px; }
        .header p  { font-size:11px; color:#bfdbfe; margin-top:2px; }
        .header-right { text-align:right; }
        .header-row { display:flex; justify-content:space-between; align-items:center; }

        /* REÇU TITRE */
        .recu-titre { text-align:center; margin:16px 0; }
        .recu-titre h2 { font-size:16px; font-weight:bold; color:#1e3a8a; text-transform:uppercase; letter-spacing:3px; }
        .recu-titre .ref { font-size:11px; color:#6b7280; margin-top:4px; }

        /* DIVIDER */
        .divider { border:none; border-top:2px solid #e5e7eb; margin:12px 0; }
        .divider-blue { border-top:2px solid #1e3a8a; }

        /* INFO SECTIONS */
        .row { display:flex; justify-content:space-between; margin-bottom:16px; }
        .col { width:48%; }
        .section-title { font-size:10px; font-weight:bold; color:#6b7280; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; }
        .info-line { display:flex; justify-content:space-between; padding:4px 0; border-bottom:1px solid #f3f4f6; font-size:11px; }
        .info-line .label { color:#6b7280; }
        .info-line .value { font-weight:bold; color:#1f2937; }

        /* MONTANTS */
        .montants { background:#f0f7ff; border:1px solid #bfdbfe; border-radius:8px; padding:16px; margin:16px 0; }
        .montant-row { display:flex; justify-content:space-between; padding:6px 0; font-size:12px; }
        .montant-row.total { border-top:2px solid #1e3a8a; margin-top:8px; padding-top:10px; font-size:15px; font-weight:bold; color:#1e3a8a; }
        .montant-row.restant { color:#dc2626; font-weight:bold; }

        /* STATUT */
        .statut { text-align:center; margin:16px 0; }
        .badge { display:inline-block; padding:6px 20px; border-radius:20px; font-weight:bold; font-size:13px; }
        .badge-complete  { background:#dcfce7; color:#166534; border:2px solid #166534; }
        .badge-partiel   { background:#fef9c3; color:#854d0e; border:2px solid #854d0e; }
        .badge-attente   { background:#f3f4f6; color:#374151; border:2px solid #374151; }

        /* FOOTER */
        .footer { margin-top:24px; text-align:center; font-size:10px; color:#9ca3af; border-top:1px solid #e5e7eb; padding-top:12px; }
        .footer .tagline { font-size:12px; font-weight:bold; color:#1e3a8a; margin-bottom:4px; }

        /* BARRE PROGRESSION */
        .progress-bar { background:#e5e7eb; border-radius:4px; height:8px; overflow:hidden; margin-top:8px; }
        .progress-fill { background:#1e3a8a; height:100%; border-radius:4px; }

        /* WATERMARK PAYÉ */
        @if($paiement->statut === 'complete')
        .watermark { position:fixed; top:40%; left:10%; transform:rotate(-30deg); font-size:60px; font-weight:900; color:rgba(22,163,74,0.08); text-transform:uppercase; letter-spacing:10px; }
        @endif
    </style>
</head>
<body>
<div class="page">

    @if($paiement->statut === 'complete')
    <div class="watermark">PAYÉ</div>
    @endif

    {{-- HEADER --}}
    <div class="header">
        <div class="header-row">
            <div>
                <h1>EDC</h1>
                <p>Excellence Digital Center</p>
                <p>Korhogo / Sirasso — +225 07 48 74 61 40</p>
            </div>
            <div class="header-right">
                <p style="font-size:10px;color:#bfdbfe;">Date d'émission</p>
                <p style="font-weight:bold;">{{ now()->format('d/m/Y') }}</p>
                <p style="font-size:10px;color:#bfdbfe;margin-top:4px;">N° de reçu</p>
                <p style="font-weight:bold;">{{ $paiement->reference }}</p>
            </div>
        </div>
    </div>

    {{-- TITRE --}}
    <div class="recu-titre">
        <h2>Reçu de Paiement</h2>
        <div class="ref">Référence : {{ $paiement->reference }}</div>
    </div>

    <hr class="divider divider-blue">

    {{-- INFOS CLIENT + PAIEMENT --}}
    <div class="row">
        <div class="col">
            <div class="section-title">Informations client</div>
            <div class="info-line">
                <span class="label">Nom</span>
                <span class="value">{{ $paiement->user->prenom }} {{ $paiement->user->nom }}</span>
            </div>
            <div class="info-line">
                <span class="label">Email</span>
                <span class="value">{{ $paiement->user->email }}</span>
            </div>
            @if($paiement->user->telephone)
            <div class="info-line">
                <span class="label">Téléphone</span>
                <span class="value">{{ $paiement->user->telephone }}</span>
            </div>
            @endif
        </div>

        <div class="col">
            <div class="section-title">Détails du paiement</div>
            <div class="info-line">
                <span class="label">Date</span>
                <span class="value">{{ $paiement->date_paiement?->format('d/m/Y') ?? now()->format('d/m/Y') }}</span>
            </div>
            <div class="info-line">
                <span class="label">Mode</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</span>
            </div>
            @if($paiement->enregistrePar)
            <div class="info-line">
                <span class="label">Enregistré par</span>
                <span class="value">{{ $paiement->enregistrePar->prenom }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- OBJET PAIEMENT --}}
    <div class="section-title" style="margin-top:8px;">Objet du paiement</div>
    <div class="info-line">
        <span class="label">Pour</span>
        <span class="value">
            @if($paiement->formation)
                Formation : {{ $paiement->formation->titre }}
            @elseif($paiement->service)
                Service : {{ $paiement->service->titre }}
            @else
                Service Excellence Digital Center
            @endif
        </span>
    </div>
    @if($paiement->notes)
    <div class="info-line">
        <span class="label">Notes</span>
        <span class="value">{{ $paiement->notes }}</span>
    </div>
    @endif

    <hr class="divider" style="margin-top:12px;">

    {{-- MONTANTS --}}
    <div class="montants">
        <div class="montant-row">
            <span>Montant total</span>
            <span>{{ number_format($paiement->montant_total, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="montant-row">
            <span>Montant payé</span>
            <span>{{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</span>
        </div>
        @if($paiement->montant_restant > 0)
        <div class="montant-row restant">
            <span>Reste à payer</span>
            <span>{{ number_format($paiement->montant_restant, 0, ',', ' ') }} FCFA</span>
        </div>
        @endif
        <div class="montant-row total">
            <span>Payé ({{ $paiement->pourcentage }}%)</span>
            <span>{{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</span>
        </div>

        {{-- Barre de progression --}}
        <div class="progress-bar">
            <div class="progress-fill" style="width:{{ $paiement->pourcentage }}%;"></div>
        </div>
    </div>

    {{-- STATUT --}}
    <div class="statut">
        @php
            $badgeClass = match($paiement->statut) {
                'complete'   => 'badge-complete',
                'partiel'    => 'badge-partiel',
                default      => 'badge-attente',
            };
            $badgeLabel = match($paiement->statut) {
                'complete'   => '✓ PAIEMENT COMPLET',
                'partiel'    => '⚠ PAIEMENT PARTIEL',
                default      => 'EN ATTENTE DE PAIEMENT',
            };
        @endphp
        <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="tagline">Former • Créer • Réussir 🚀</div>
        <p>Excellence Digital Center — Korhogo / Sirasso — +225 07 48 74 61 40</p>
        <p style="margin-top:4px;">Ce reçu est généré automatiquement et fait foi de paiement.</p>
    </div>
</div>
</body>
</html>
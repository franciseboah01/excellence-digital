<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            background: white;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }

        /* FOND ET BORDURE */
        .certificat {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 50%, #fefce8 100%);
            position: relative;
            padding: 20px 30px;
        }

        /* BORDURE DÉCORATIVE */
        .bordure-ext {
            position: absolute;
            top: 10px; left: 10px; right: 10px; bottom: 10px;
            border: 3px solid #1e3a8a;
            border-radius: 8px;
        }
        .bordure-int {
            position: absolute;
            top: 16px; left: 16px; right: 16px; bottom: 16px;
            border: 1px solid #93c5fd;
            border-radius: 6px;
        }

        /* COINS DÉCORATIFS */
        .coin {
            position: absolute;
            width: 30px;
            height: 30px;
            border-color: #1e3a8a;
            border-style: solid;
        }
        .coin-tl { top: 22px; left: 22px; border-width: 3px 0 0 3px; }
        .coin-tr { top: 22px; right: 22px; border-width: 3px 3px 0 0; }
        .coin-bl { bottom: 22px; left: 22px; border-width: 0 0 3px 3px; }
        .coin-br { bottom: 22px; right: 22px; border-width: 0 3px 3px 0; }

        /* CONTENU */
        .contenu {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 15px 50px;
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .logo-edc {
            font-size: 28px;
            font-weight: 900;
            color: #1e3a8a;
            letter-spacing: 3px;
        }
        .logo-sub {
            font-size: 9px;
            color: #64748b;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .numero-cert {
            font-size: 9px;
            color: #94a3b8;
            text-align: right;
        }

        /* TITRE */
        .titre-certificat {
            font-size: 11px;
            letter-spacing: 6px;
            text-transform: uppercase;
            color: #64748b;
            margin: 8px 0 4px;
        }
        .titre-principal {
            font-size: 38px;
            font-weight: 900;
            color: #1e3a8a;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .sous-titre {
            font-size: 12px;
            color: #64748b;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* SÉPARATEUR */
        .separateur {
            display: flex;
            align-items: center;
            margin: 10px auto;
            max-width: 500px;
        }
        .sep-ligne { flex: 1; height: 1px; background: #93c5fd; }
        .sep-etoile { margin: 0 12px; color: #fbbf24; font-size: 14px; }

        /* CORPS */
        .atteste {
            font-size: 11px;
            color: #475569;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 6px 0 4px;
        }
        .nom-apprenant {
            font-size: 32px;
            font-weight: 900;
            color: #0f172a;
            font-style: italic;
            margin: 4px 0;
            border-bottom: 2px solid #fbbf24;
            display: inline-block;
            padding: 0 30px 4px;
        }
        .texte-formation {
            font-size: 11px;
            color: #475569;
            margin: 8px 0 4px;
        }
        .nom-formation {
            font-size: 18px;
            font-weight: 700;
            color: #1e3a8a;
            margin: 4px 0;
        }
        .niveau-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
            border-radius: 20px;
            padding: 2px 16px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 4px 0;
        }

        /* NOTE */
        .note-section {
            display: inline-block;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: white;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            line-height: 1;
            padding-top: 12px;
            margin: 8px 0;
        }
        .note-valeur { font-size: 22px; font-weight: 900; display: block; }
        .note-sur { font-size: 10px; color: #bfdbfe; }

        /* FOOTER */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
        }
        .footer-gauche { text-align: left; }
        .footer-centre { text-align: center; }
        .footer-droite { text-align: right; }
        .footer-label { font-size: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        .footer-valeur { font-size: 11px; font-weight: bold; color: #1e3a8a; margin-top: 2px; }
        .signature-ligne { width: 120px; height: 1px; background: #1e3a8a; margin: 20px auto 4px; }

        /* FILIGRANE */
        .filigrane {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80px;
            font-weight: 900;
            color: rgba(30, 58, 138, 0.04);
            white-space: nowrap;
            z-index: 1;
            letter-spacing: 10px;
        }

        /* ÉTOILES DÉCORATIVES */
        .etoiles-deco {
            color: #fbbf24;
            font-size: 14px;
            letter-spacing: 4px;
            margin: 4px 0;
        }
    </style>
</head>
<body>
<div class="certificat">

    {{-- Bordures décoratives --}}
    <div class="bordure-ext"></div>
    <div class="bordure-int"></div>
    <div class="coin coin-tl"></div>
    <div class="coin coin-tr"></div>
    <div class="coin coin-bl"></div>
    <div class="coin coin-br"></div>

    {{-- Filigrane --}}
    <div class="filigrane">EDC</div>

    {{-- CONTENU PRINCIPAL --}}
    <div class="contenu">

        {{-- Header --}}
        <div class="header">
            <div>
                <div class="logo-edc">EDC</div>
                <div class="logo-sub">Excellence Digital Center</div>
                <div style="font-size:8px; color:#94a3b8; margin-top:2px;">Korhogo / Sirasso</div>
            </div>
            <div class="numero-cert">
                <div>Certificat N°</div>
                <div style="font-size:11px; font-weight:bold; color:#1e3a8a; margin-top:2px;">
                    {{ $certificat->numero_certificat }}
                </div>
                <div style="margin-top:4px;">Délivré le {{ $certificat->delivre_le->format('d/m/Y') }}</div>
            </div>
        </div>

        {{-- Titre --}}
        <div class="titre-certificat">Attestation de</div>
        <div class="titre-principal">Réussite</div>
        <div class="sous-titre">Formation Professionnelle</div>

        {{-- Séparateur --}}
        <div class="separateur">
            <div class="sep-ligne"></div>
            <div class="sep-etoile">✦ ✦ ✦</div>
            <div class="sep-ligne"></div>
        </div>

        {{-- Corps --}}
        <div class="atteste">Ce certificat est décerné à</div>

        <div class="nom-apprenant">
            {{ $certificat->user->prenom }} {{ strtoupper($certificat->user->nom) }}
        </div>

        <div class="texte-formation" style="margin-top:8px;">
            Pour avoir complété avec succès la formation
        </div>

        <div class="nom-formation">{{ $certificat->formation->titre }}</div>

        @if($certificat->session->qcm->niveau)
        <div class="niveau-badge">{{ $certificat->session->qcm->niveau->nom }}</div>
        @endif

        {{-- Note --}}
        <div style="margin: 6px 0;">
            <div class="note-section">
                <span class="note-valeur">{{ $certificat->note_obtenue }}</span>
                <span class="note-sur">/20</span>
            </div>
        </div>

        <div class="etoiles-deco">★ ★ ★ ★ ★</div>

        {{-- Footer --}}
        <div class="footer">
            <div class="footer-gauche">
                <div class="footer-label">Date de délivrance</div>
                <div class="footer-valeur">{{ $certificat->delivre_le->format('d/m/Y') }}</div>
            </div>

            <div class="footer-centre">
                <div class="signature-ligne"></div>
                <div class="footer-label">Directeur — Excellence Digital Center</div>
                <div class="footer-valeur" style="margin-top:2px;">Korhogo / Sirasso</div>
            </div>

            <div class="footer-droite">
                <div class="footer-label">Téléphone</div>
                <div class="footer-valeur">+225 07 48 74 61 40</div>
                <div class="footer-label" style="margin-top:4px;">Former • Créer • Réussir</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
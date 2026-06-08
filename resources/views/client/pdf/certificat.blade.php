<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', 'Inter', sans-serif;
            background: #0B0F1A;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .certificat {
            width: 277mm;
            height: 190mm;
            background: linear-gradient(145deg, #0f172a 0%, #1a2332 50%, #0d1520 100%);
            position: relative;
            border-radius: 16px;
            overflow: hidden;
        }

        /* ---- GLOW CORNER ---- */
        .glow-tl {
            position: absolute;
            top: -80px; left: -80px;
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%);
        }
        .glow-br {
            position: absolute;
            bottom: -80px; right: -80px;
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(251,191,36,0.10) 0%, transparent 70%);
        }

        /* ---- BORDURE SUBTILE ---- */
        .border-ext {
            position: absolute;
            top: 12px; left: 12px; right: 12px; bottom: 12px;
            border: 1px solid rgba(59,130,246,0.20);
            border-radius: 12px;
        }
        .border-int {
            position: absolute;
            top: 20px; left: 20px; right: 20px; bottom: 20px;
            border: 1px solid rgba(148,163,184,0.10);
            border-radius: 10px;
        }

        /* ---- LIGNE DÉCO HAUT ---- */
        .line-top {
            position: absolute;
            top: 28px; left: 60px; right: 60px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(59,130,246,0.4), rgba(251,191,36,0.3), rgba(59,130,246,0.4), transparent);
        }
        .line-bottom {
            position: absolute;
            bottom: 28px; left: 60px; right: 60px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(251,191,36,0.3), rgba(59,130,246,0.4), rgba(251,191,36,0.3), transparent);
        }

        /* ---- CONTENU ---- */
        .contenu {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 30px 60px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* ---- HEADER ---- */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        .logo-edc {
            font-size: 32px;
            font-weight: 900;
            letter-spacing: 4px;
            background: linear-gradient(135deg, #3B82F6, #60A5FA);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .logo-sub {
            font-size: 9px;
            color: #64748B;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .numero-cert {
            font-size: 9px;
            color: #64748B;
            text-align: right;
            line-height: 1.6;
        }
        .numero-cert .cert-number {
            font-size: 12px;
            font-weight: bold;
            color: #60A5FA;
        }

        /* ---- TITRE ---- */
        .badge-cert {
            display: inline-block;
            font-size: 9px;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: #FBBF24;
            border: 1px solid rgba(251,191,36,0.25);
            border-radius: 20px;
            padding: 4px 20px;
            margin-bottom: 10px;
        }
        .titre-principal {
            font-size: 42px;
            font-weight: 900;
            letter-spacing: 3px;
            text-transform: uppercase;
            background: linear-gradient(135deg, #F1F5F9, #94A3B8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 4px;
        }
        .sous-titre {
            font-size: 12px;
            color: #64748B;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        /* ---- SÉPARATEUR ---- */
        .separateur {
            display: flex;
            align-items: center;
            margin: 14px auto;
            max-width: 450px;
        }
        .sep-ligne {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(59,130,246,0.5), transparent);
        }
        .sep-diamant {
            margin: 0 16px;
            color: #FBBF24;
            font-size: 10px;
            letter-spacing: 6px;
        }

        /* ---- CORPS ---- */
        .atteste {
            font-size: 11px;
            color: #94A3B8;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 6px 0 4px;
        }
        .nom-apprenant {
            font-size: 36px;
            font-weight: 900;
            color: #F1F5F9;
            font-style: italic;
            margin: 4px 0;
            display: inline-block;
            padding: 0 30px 6px;
            border-bottom: 2px solid #FBBF24;
        }
        .texte-formation {
            font-size: 11px;
            color: #94A3B8;
            margin: 10px 0 4px;
        }
        .nom-formation {
            font-size: 20px;
            font-weight: 700;
            color: #60A5FA;
            margin: 4px 0;
        }
        .niveau-badge {
            display: inline-block;
            background: rgba(59,130,246,0.12);
            color: #60A5FA;
            border: 1px solid rgba(59,130,246,0.25);
            border-radius: 20px;
            padding: 3px 18px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 6px 0;
        }

        /* ---- NOTE ---- */
        .note-section {
            display: inline-block;
            background: linear-gradient(135deg, #1e3a8a, #3B82F6);
            color: #F1F5F9;
            border-radius: 50%;
            width: 76px;
            height: 76px;
            padding-top: 14px;
            margin: 10px 0;
            box-shadow: 0 0 30px rgba(59,130,246,0.30);
        }
        .note-valeur { font-size: 24px; font-weight: 900; display: block; }
        .note-sur { font-size: 10px; color: #93C5FD; }

        .etoiles-deco {
            color: #FBBF24;
            font-size: 12px;
            letter-spacing: 6px;
            margin: 4px 0;
        }

        /* ---- FOOTER ---- */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid rgba(42,53,82,0.5);
        }
        .footer-gauche { text-align: left; }
        .footer-centre { text-align: center; }
        .footer-droite { text-align: right; }
        .footer-label {
            font-size: 8px;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .footer-valeur {
            font-size: 11px;
            font-weight: bold;
            color: #F1F5F9;
            margin-top: 2px;
        }
        .signature-ligne {
            width: 140px;
            height: 1px;
            background: linear-gradient(90deg, transparent, #3B82F6, transparent);
            margin: 24px auto 4px;
        }

        /* ---- FILIGRANE ---- */
        .filigrane {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 120px;
            font-weight: 900;
            color: rgba(59,130,246,0.03);
            white-space: nowrap;
            z-index: 1;
            letter-spacing: 20px;
        }
    </style>
</head>
<body>
<div class="certificat">

    {{-- Glows --}}
    <div class="glow-tl"></div>
    <div class="glow-br"></div>

    {{-- Bordures --}}
    <div class="border-ext"></div>
    <div class="border-int"></div>

    {{-- Lignes décoratives --}}
    <div class="line-top"></div>
    <div class="line-bottom"></div>

    {{-- Filigrane --}}
    <div class="filigrane">EDC</div>

    {{-- CONTENU --}}
    <div class="contenu">

        {{-- Header --}}
        <div class="header">
            <div>
                <div class="logo-edc">EDC</div>
                <div class="logo-sub">Excellence Digital Center</div>
                <div style="font-size:8px; color:#64748B; margin-top:2px;">Korhogo / Sirasso</div>
            </div>
            <div class="numero-cert">
                <div>Certificat N°</div>
                <div class="cert-number">{{ $certificat->numero_certificat }}</div>
                <div style="margin-top:4px;">Délivré le {{ $certificat->delivre_le->format('d/m/Y') }}</div>
            </div>
        </div>

        {{-- Badge --}}
        <div class="badge-cert">Attestation de</div>

        {{-- Titre --}}
        <div class="titre-principal">Réussite</div>
        <div class="sous-titre">Formation Professionnelle</div>

        {{-- Séparateur --}}
        <div class="separateur">
            <div class="sep-ligne"></div>
            <div class="sep-diamant">✦ ✦ ✦</div>
            <div class="sep-ligne"></div>
        </div>

        {{-- Corps --}}
        <div class="atteste">Ce certificat est décerné à</div>

        <div class="nom-apprenant">
            {{ $certificat->user->prenom }} {{ strtoupper($certificat->user->nom) }}
        </div>

        <div class="texte-formation">
            Pour avoir complété avec succès la formation
        </div>

        <div class="nom-formation">{{ $certificat->formation->titre }}</div>

        @if($certificat->session->qcm->niveau)
        <div class="niveau-badge">{{ $certificat->session->qcm->niveau->nom }}</div>
        @endif

        {{-- Note --}}
        <div>
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
                <div class="footer-valeur">Korhogo / Sirasso</div>
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
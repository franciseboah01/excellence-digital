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
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            background-image: url("{{ $backgroundImage ?? asset('storage/certificats/default_bg.jpg') }}");
            background-size: cover;
            background-position: center;
        }

        /* ---- OVERLAY POUR LISIBILITÉ ---- */
        .overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(11, 15, 26, 0.30);
            z-index: 1;
        }

        /* ---- CONTENU POSITIONNÉ EN ABSOLU (comme GD) ---- */
        .contenu {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            padding: 30px 50px;
        }

        /* ---- STYLES DES ÉLÉMENTS (positions en mm pour le PDF) ---- */

        .cert-numero {
            position: absolute;
            top: {{ $positions['numero']['y'] / 2.83 ?? 7 }}mm;
            left: {{ $positions['numero']['x'] / 2.83 ?? 85 }}mm;
            font-size: {{ $positions['numero']['size'] ?? 12 }}px;
            color: {{ $fontColor ?? '#FFFFFF' }};
            font-weight: bold;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .cert-nom {
            position: absolute;
            top: {{ $positions['name']['y'] / 2.83 ?? 37 }}mm;
            left: {{ $positions['name']['x'] / 2.83 ?? 52 }}mm;
            font-size: {{ $positions['name']['size'] ?? 28 }}px;
            color: {{ $fontColor ?? '#FFFFFF' }};
            font-weight: bold;
            text-transform: uppercase;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .cert-formation {
            position: absolute;
            top: {{ $positions['formation']['y'] / 2.83 ?? 48 }}mm;
            left: {{ $positions['formation']['x'] / 2.83 ?? 52 }}mm;
            font-size: {{ $positions['formation']['size'] ?? 20 }}px;
            color: {{ $fontColor ?? '#FFFFFF' }};
            font-weight: bold;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .cert-performance {
            position: absolute;
            top: {{ $positions['performance']['y'] / 2.83 ?? 55 }}mm;
            left: {{ $positions['performance']['x'] / 2.83 ?? 52 }}mm;
            font-size: {{ $positions['performance']['size'] ?? 12 }}px;
            color: {{ $fontColor ?? '#FFFFFF' }};
            font-family: 'DejaVu Sans', sans-serif;
        }

        .cert-qr {
            position: absolute;
            bottom: {{ 55 - ($positions['metadata']['y'] / 2.83 ?? 65) }}mm;
            right: {{ 50 - ($positions['metadata']['x'] / 2.83 ?? 14) }}mm;
            width: {{ $qrSize / 2.83 ?? 35 }}mm;
            height: {{ $qrSize / 2.83 ?? 35 }}mm;
        }
        .cert-qr img {
            width: 100%;
            height: 100%;
        }

        .cert-date-lieu {
            position: absolute;
            bottom: {{ 45 - ($positions['metadata']['y'] / 2.83 ?? 65) }}mm;
            left: {{ $positions['metadata']['x'] / 2.83 ?? 14 }}mm;
            font-size: 12px;
            color: {{ $fontColor ?? '#FFFFFF' }};
            font-family: 'DejaVu Sans', sans-serif;
        }
    </style>
</head>
<body>
<div class="certificat">

    {{-- Overlay pour lisibilité --}}
    <div class="overlay"></div>

    {{-- CONTENU POSITIONNÉ --}}
    <div class="contenu">

        {{-- Numéro du certificat --}}
        <div class="cert-numero">
            {{ $certificat->numero_certificat }}
        </div>

        {{-- Nom de l'apprenant --}}
        <div class="cert-nom">
            {{ $certificat->user->prenom }} {{ strtoupper($certificat->user->nom) }}
        </div>

        {{-- Formation --}}
        <div class="cert-formation">
            {{ $certificat->formation->titre }}
        </div>

        {{-- Performance (Note + Mention) --}}
        @if($showNote)
        <div class="cert-performance">
            Note : {{ number_format($certificat->note_obtenue, 1) }}/20 
            @if($showMention)
            | Mention : {{ $certificat->mention }}
            @endif
        </div>
        @endif

        {{-- QR Code --}}
        @if($showQrCode)
        <div class="cert-qr">
            <img src="{{ $qrCodeDataUri }}" alt="QR Code">
        </div>
        @endif

        {{-- Date et Lieu --}}
        <div class="cert-date-lieu">
            Délivré le {{ $certificat->delivre_le->format('d/m/Y') }}
        </div>

    </div>
</div>
</body>
</html>
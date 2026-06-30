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
            width: 297mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
            @if($backgroundImage)
            background-image: url("{{ $backgroundImage }}");
            background-size: 297mm 210mm;
            background-position: center;
            background-repeat: no-repeat;
            @else
            background-color: #1a1a2e;
            @endif
        }

        /* ---- OVERLAY POUR LISIBILITÉ ---- */
        .overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(11, 15, 26, 0.30);
            z-index: 1;
        }

        /* ---- CONTENU POSITIONNÉ EN ABSOLU ---- */
        .contenu {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
        }

        /* ---- STYLES DES ÉLÉMENTS ---- */

        .cert-numero {
            position: absolute;
            top: {{ $positions['numero']['y'] }}px;
            left: {{ $positions['numero']['x'] }}px;
            font-size: {{ $positions['numero']['size'] }}px;
            color: {{ $fontColor ?? '#FFFFFF' }};
            font-weight: bold;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .cert-nom {
            position: absolute;
            top: {{ $positions['name']['y'] }}px;
            left: {{ $positions['name']['x'] }}px;
            font-size: {{ $positions['name']['size'] }}px;
            color: {{ $fontColor ?? '#FFFFFF' }};
            font-weight: bold;
            text-transform: uppercase;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .cert-formation {
            position: absolute;
            top: {{ $positions['formation']['y'] }}px;
            left: {{ $positions['formation']['x'] }}px;
            font-size: {{ $positions['formation']['size'] }}px;
            color: {{ $fontColor ?? '#FFFFFF' }};
            font-weight: bold;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .cert-performance {
            position: absolute;
            top: {{ $positions['performance']['y'] }}px;
            left: {{ $positions['performance']['x'] }}px;
            font-size: {{ $positions['performance']['size'] }}px;
            color: {{ $fontColor ?? '#FFFFFF' }};
            font-family: 'DejaVu Sans', sans-serif;
        }

        .cert-qr {
            position: absolute;
            top: {{ $positions['metadata']['y'] }}px;
            left: {{ $positions['metadata']['x'] }}px;
            width: {{ $qrSize }}px;
            height: {{ $qrSize }}px;
        }
        .cert-qr img {
            width: 100%;
            height: 100%;
        }

        .cert-date-lieu {
            position: absolute;
            top: {{ $positions['metadata']['y'] + $qrSize + 10 }}px;
            left: {{ $positions['metadata']['x'] }}px;
            font-size: 12px;
            color: {{ $fontColor ?? '#FFFFFF' }};
            font-family: 'DejaVu Sans', sans-serif;
        }

        .cert-mention {
            position: absolute;
            top: {{ $positions['performance']['y'] + 25 }}px;
            left: {{ $positions['performance']['x'] }}px;
            font-size: 14px;
            color: {{ $fontColor ?? '#FFFFFF' }};
            font-weight: bold;
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
            {{ $certificat->user->prenom ?? '' }} {{ strtoupper($certificat->user->nom ?? '') }}
        </div>

        {{-- Formation --}}
        <div class="cert-formation">
            {{ $certificat->formation->titre ?? '' }}
        </div>

        {{-- Performance (Note) --}}
        @if($showNote)
        <div class="cert-performance">
            Note : {{ number_format($certificat->note_obtenue ?? 0, 1) }}/20
        </div>
        @endif

        {{-- Mention --}}
        @if($showMention && isset($certificat->mention))
        <div class="cert-mention">
            Mention : {{ $certificat->mention }}
        </div>
        @endif

        {{-- QR Code --}}
        @if($showQrCode && $qrCodeDataUri)
        <div class="cert-qr">
            <img src="{{ $qrCodeDataUri }}" alt="QR Code">
        </div>
        @endif

        {{-- Date et Lieu --}}
        <div class="cert-date-lieu">
            Délivré le {{ $certificat->delivre_le ? $certificat->delivre_le->format('d/m/Y') : '—' }}
        </div>

    </div>
</div>
</body>
</html>
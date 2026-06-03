<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:20px; }
        .container { max-width:600px; margin:auto; background:white; border-radius:8px; overflow:hidden; }
        .header { background:#1e3a8a; color:white; padding:24px; text-align:center; }
        .body { padding:24px; }
        .footer { background:#f8f9fa; padding:16px; text-align:center; font-size:12px; color:#888; }
        .status-box { background:#f0f7ff; border-left:4px solid #1e3a8a; padding:16px; border-radius:6px; margin:16px 0; }
        .btn { display:inline-block; background:#1e3a8a; color:white; padding:12px 24px; border-radius:6px; text-decoration:none; margin-top:16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📋 Mise à jour de votre demande</h2>
            <p>Excellence Digital Center</p>
        </div>
        <div class="body">
            <p>Bonjour <strong>{{ $nom }}</strong>,</p>
            <p>Nous vous informons d'une mise à jour concernant votre demande de service :</p>

            <div class="status-box">
                <p style="margin:0; font-size:16px;">
                    <strong>Service :</strong> {{ $demande->service->titre }}
                </p>
                <p style="margin:8px 0 0; font-size:18px; font-weight:bold; color:#1e3a8a;">
                    Nouveau statut : {{ $statutLabel }}
                </p>
            </div>

            <p>{{ $messagePersonnalise }}</p>

            @if($demande->statut === 'termine')
            <p style="color:#15803d; font-weight:bold;">
                🎉 Merci de nous avoir fait confiance !
            </p>
            @endif

            <p>Pour toute question, contactez-nous sur WhatsApp :</p>
            <a href="https://wa.me/2250748746140" class="btn">
                💬 Nous contacter
            </a>
        </div>
        <div class="footer">
            Excellence Digital Center — Korhogo / Sirasso<br>
            Former • Créer • Réussir 🚀
        </div>
    </div>
</body>
</html>
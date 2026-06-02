<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; border-radius: 8px; overflow: hidden; }
        .header { background: #1e3a8a; color: white; padding: 24px; text-align: center; }
        .body { padding: 24px; }
        .footer { background: #f8f9fa; padding: 16px; text-align: center; font-size: 12px; color: #888; }
        .badge { display: inline-block; background: #dbeafe; color: #1e3a8a; padding: 4px 12px; border-radius: 20px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📩 Nouveau message de contact</h2>
            <p>Excellence Digital Center</p>
        </div>
        <div class="body">
            <p><strong>Nom :</strong> {{ $data['nom'] }}</p>
            <p><strong>Email :</strong> {{ $data['email'] }}</p>
            <p><strong>Sujet :</strong> <span class="badge">{{ $data['sujet'] }}</span></p>
            <hr>
            <p><strong>Message :</strong></p>
            <p style="background:#f8f9fa; padding:16px; border-radius:6px; line-height:1.6;">
                {{ $data['message'] }}
            </p>
        </div>
        <div class="footer">
            Excellence Digital Center — Korhogo / Sirasso — +225 07 48 74 61 40
        </div>
    </div>
</body>
</html>
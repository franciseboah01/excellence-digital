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
        .btn { display: inline-block; background: #1e3a8a; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; margin-top: 16px; }
        .status { display: inline-block; background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 20px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>✅ Demande bien reçue !</h2>
            <p>Excellence Digital Center</p>
        </div>
        <div class="body">
            <p>Bonjour <strong>{{ $demande->nom_visiteur }}</strong>,</p>
            <p>Nous avons bien reçu votre demande concernant le service :</p>
            <p style="font-size:18px; font-weight:bold; color:#1e3a8a;">
                {{ $demande->service->titre }}
            </p>
            <p>Statut actuel : <span class="status">⏳ En attente de traitement</span></p>
            <p>Notre équipe vous contactera dans les <strong>24 heures</strong> via :</p>
            <ul>
                <li>📧 Email : {{ $demande->email_visiteur }}</li>
                @if($demande->telephone_visiteur)
                <li>📲 WhatsApp : {{ $demande->telephone_visiteur }}</li>
                @endif
            </ul>
            <p style="margin-top:20px;">
                Pour toute urgence, contactez-nous directement sur WhatsApp :
            </p>
            <a href="https://wa.me/2250748746140" class="btn">
                💬 Contacter sur WhatsApp
            </a>
        </div>
        <div class="footer">
            Excellence Digital Center — Korhogo / Sirasso<br>
            Former • Créer • Réussir 🚀
        </div>
    </div>
</body>
</html>
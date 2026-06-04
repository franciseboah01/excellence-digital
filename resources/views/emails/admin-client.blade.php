<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background:#f0f4f8; padding:20px; }
        .wrapper { max-width:600px; margin:auto; }
        .header { background:linear-gradient(135deg, #1e3a8a, #2563eb); color:white; padding:30px; text-align:center; border-radius:12px 12px 0 0; }
        .body { background:white; padding:32px; }
        .message-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:20px; margin:20px 0; line-height:1.8; font-size:14px; color:#374151; }
        .expediteur { display:flex; align-items:center; background:#eff6ff; padding:12px 16px; border-radius:8px; margin-bottom:20px; }
        .avatar { width:40px; height:40px; background:#1e3a8a; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:bold; font-size:16px; margin-right:12px; flex-shrink:0; }
        .footer { background:#1e3a8a; color:#bfdbfe; padding:20px; text-align:center; border-radius:0 0 12px 12px; font-size:12px; }
        .btn { display:inline-block; background:#1e3a8a; color:white !important; padding:12px 28px; border-radius:8px; text-decoration:none; font-weight:bold; font-size:13px; margin-top:20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <p style="font-size:12px; color:#bfdbfe; margin-bottom:4px;">Message de l'Administration</p>
        <h2 style="font-size:20px; font-weight:900;">Excellence Digital Center</h2>
    </div>

    <div class="body">
        <p style="font-size:16px; font-weight:bold; color:#1e3a8a; margin-bottom:16px;">
            Bonjour {{ $destinataire->prenom }} 👋
        </p>

        <div class="expediteur">
            <div class="avatar">
                {{ strtoupper(substr($admin->prenom, 0, 1)) }}
            </div>
            <div>
                <p style="font-size:13px; font-weight:bold; color:#1e3a8a;">
                    {{ $admin->nom_complet }}
                </p>
                <p style="font-size:11px; color:#64748b;">Administrateur — Excellence Digital Center</p>
            </div>
        </div>

        <p style="font-size:13px; color:#6b7280; margin-bottom:8px;">
            Vous avez reçu le message suivant :
        </p>

        <div class="message-box">
            {!! nl2br(e($contenu)) !!}
        </div>

        <a href="https://wa.me/2250748746140" class="btn">
            💬 Répondre sur WhatsApp
        </a>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Excellence Digital Center — Korhogo / Sirasso</p>
        <p style="margin-top:4px;">
            <a href="https://wa.me/2250748746140" style="color:#93c5fd;">
                +225 07 48 74 61 40
            </a>
        </p>
    </div>
</div>
</body>
</html>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background:#f0f4f8; padding:20px; }
        .wrapper { max-width:600px; margin:auto; }
        .header { background:linear-gradient(135deg, #065f46, #059669); color:white; padding:30px; text-align:center; border-radius:12px 12px 0 0; }
        .body { background:white; padding:32px; }
        .message-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:20px; margin:20px 0; line-height:1.8; font-size:14px; color:#374151; }
        .expediteur { display:flex; align-items:center; background:#ecfdf5; padding:12px 16px; border-radius:8px; margin-bottom:20px; }
        .avatar { width:40px; height:40px; background:#059669; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:bold; font-size:16px; margin-right:12px; flex-shrink:0; }
        .footer { background:#065f46; color:#a7f3d0; padding:20px; text-align:center; border-radius:0 0 12px 12px; font-size:12px; }
        .btn { display:inline-block; background:#065f46; color:white !important; padding:12px 28px; border-radius:8px; text-decoration:none; font-weight:bold; font-size:13px; margin-top:20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <p style="font-size:12px; color:#a7f3d0; margin-bottom:4px;">Message de votre formateur</p>
        <h2 style="font-size:20px; font-weight:900;">Excellence Digital Center</h2>
        <p style="font-size:12px; color:#d1fae5; margin-top:6px;">🎓 Espace Formation</p>
    </div>

    <div class="body">
        <p style="font-size:16px; font-weight:bold; color:#065f46; margin-bottom:16px;">
            Bonjour {{ $apprenant->prenom }} 👋
        </p>

        <div class="expediteur">
            <div class="avatar">
                {{ strtoupper(substr($enseignant->prenom, 0, 1)) }}
            </div>
            <div>
                <p style="font-size:13px; font-weight:bold; color:#065f46;">
                    {{ $enseignant->nom_complet }}
                </p>
                <p style="font-size:11px; color:#64748b;">Formateur — Excellence Digital Center</p>
            </div>
        </div>

        <p style="font-size:13px; color:#6b7280; margin-bottom:8px;">
            Votre formateur vous a envoyé le message suivant :
        </p>

        <div class="message-box">
            {!! nl2br(e($contenu)) !!}
        </div>

        <a href="{{ route('client.dashboard') }}" class="btn">
            📚 Accéder à mes cours
        </a>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Excellence Digital Center — Korhogo / Sirasso</p>
    </div>
</div>
</body>
</html>
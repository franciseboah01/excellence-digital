<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:Arial,sans-serif; background:#f0f4f8; padding:20px; }
        .wrapper { max-width:600px; margin:auto; }
        .header { background:linear-gradient(135deg,#1e3a8a,#2563eb); color:white; padding:30px; text-align:center; border-radius:12px 12px 0 0; }
        .body { background:white; padding:32px; }
        .bubble { background:#eff6ff; border-left:4px solid #2563eb; padding:16px; border-radius:0 10px 10px 10px; margin:20px 0; font-size:14px; color:#374151; line-height:1.7; }
        .expediteur { display:flex; align-items:center; margin-bottom:16px; }
        .avatar { width:44px; height:44px; background:#1e3a8a; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:bold; font-size:18px; margin-right:12px; }
        .btn { display:inline-block; background:#1e3a8a; color:white!important; padding:12px 28px; border-radius:8px; text-decoration:none; font-weight:bold; font-size:13px; margin-top:20px; }
        .footer { background:#1e3a8a; color:#bfdbfe; padding:20px; text-align:center; border-radius:0 0 12px 12px; font-size:12px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <p style="font-size:12px;color:#bfdbfe;margin-bottom:4px;">Messagerie interne</p>
        <h2 style="font-size:20px;font-weight:900;">Excellence Digital Center</h2>
    </div>
    <div class="body">
        <p style="font-size:16px;font-weight:bold;color:#1e3a8a;margin-bottom:16px;">
            Bonjour {{ $destinataire->prenom }} 👋
        </p>

        <div class="expediteur">
            <div class="avatar">{{ strtoupper(substr($expediteur->prenom,0,1)) }}</div>
            <div>
                <p style="font-weight:bold;color:#1e3a8a;font-size:14px;">
                    {{ $expediteur->prenom }} {{ $expediteur->nom }}
                </p>
                <p style="font-size:12px;color:#64748b;">vous a envoyé un message</p>
            </div>
        </div>

        <div class="bubble">{{ $contenu }}</div>

        <a href="{{ url('/messages') }}" class="btn">💬 Répondre au message</a>
    </div>
    <div class="footer">
        © {{ date('Y') }} Excellence Digital Center — Korhogo / Sirasso
    </div>
</div>
</body>
</html>
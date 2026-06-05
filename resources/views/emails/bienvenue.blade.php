<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background:#f0f4f8; padding:20px; }
        .wrapper { max-width:600px; margin:auto; }
        .header { background:linear-gradient(135deg, #1e3a8a, #2563eb); color:white; padding:40px 30px; text-align:center; border-radius:12px 12px 0 0; }
        .logo { font-size:28px; font-weight:900; letter-spacing:2px; }
        .tagline { font-size:13px; color:#bfdbfe; margin-top:4px; }
        .body { background:white; padding:32px; }
        .greeting { font-size:20px; font-weight:bold; color:#1e3a8a; margin-bottom:16px; }
        .text { font-size:14px; color:#4b5563; line-height:1.7; margin-bottom:16px; }
        .card { background:#eff6ff; border-left:4px solid #2563eb; padding:16px; border-radius:8px; margin:20px 0; }
        .card p { font-size:13px; color:#1e3a8a; margin-bottom:6px; }
        .card p strong { font-weight:700; }
        .btn { display:inline-block; background:#1e3a8a; color:white !important; padding:14px 32px; border-radius:8px; text-decoration:none; font-weight:bold; font-size:14px; margin-top:20px; }
        .divider { border:none; border-top:1px solid #e5e7eb; margin:24px 0; }
        .services { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin:16px 0; }
        .service-item { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; text-align:center; font-size:12px; color:#475569; }
        .service-item .icon { font-size:22px; display:block; margin-bottom:4px; }
        .footer { background:#1e3a8a; color:#bfdbfe; padding:20px; text-align:center; border-radius:0 0 12px 12px; font-size:12px; }
        .footer a { color:#93c5fd; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- HEADER --}}
    <div class="header">
        <div class="logo">EDC</div>
        <div class="tagline">Excellence Digital Center</div>
        <p style="font-size:13px; color:#dbeafe; margin-top:10px;">
            Former • Créer • Réussir 🚀
        </p>
    </div>

    {{-- BODY --}}
    <div class="body">
        <p class="greeting">Bienvenue, {{ $user->prenom }} ! 🎉</p>

        <p class="text">
            Nous sommes ravis de vous accueillir sur <strong>Excellence Digital Center</strong>.
            Votre compte a été créé avec succès et vous pouvez maintenant accéder à tous nos services.
        </p>

        <div class="card">
            <p><strong>👤 Nom :</strong> {{ $user->nom_complet }}</p>
            <p><strong>📧 Email :</strong> {{ $user->email }}</p>
            <p><strong>📅 Inscription :</strong> {{ $user->created_at->format('d/m/Y à H:i') }}</p>
        </div>

        <p class="text">Voici ce que vous pouvez faire avec votre espace :</p>

        <div class="services">
            <div class="service-item">
                <span class="icon">📋</span>Demander des services
            </div>
            <div class="service-item">
                <span class="icon">🎓</span>Accéder aux formations
            </div>
            <div class="service-item">
                <span class="icon">📚</span>Consulter les ressources
            </div>
            <div class="service-item">
                <span class="icon">🔔</span>Recevoir des notifications
            </div>
        </div>

        <hr class="divider">

        <p class="text">
            Pour toute question, notre équipe est disponible sur WhatsApp :
        </p>
        <a href="https://wa.me/2250748746140" class="btn">
            💬 Contacter sur WhatsApp
        </a>
    </div>

    {{-- Bouton vérification email --}}
    <hr class="divider">
    <p class="text" style="color:#dc2626; font-weight:bold;">
        ⚠️ Vérifiez votre adresse email pour activer votre compte :
    </p>
    <a href="{{ $verificationUrl }}" class="btn"
        style="background:#059669; margin-top:10px;">
        ✅ Vérifier mon email
    </a>
    <p style="font-size:11px; color:#9ca3af; margin-top:8px;">
        Ce lien expire dans 60 minutes.
    </p>

    {{-- FOOTER --}}
    <div class="footer">
        <p>© {{ date('Y') }} Excellence Digital Center</p>
        <p style="margin-top:6px;">
            📍 Korhogo / Sirasso —
            <a href="https://wa.me/2250748746140">+225 07 48 74 61 40</a>
        </p>
    </div>

</div>
</body>
</html>
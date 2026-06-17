<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de Document Officiel - {{ \App\Models\Configuration::get('site_nom', 'EXCELLENCE DIGITAL CENTER') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-main: #020617;
            --card-bg: #0f172a;
            --border-color: #1e293b;
        }
        body { background-color: var(--bg-main); font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="text-slate-100 min-h-screen flex flex-col justify-between p-4 sm:p-6 antialiased">

    {{-- CONTENEUR CENTRAL --}}
    <main class="w-full max-w-2xl mx-auto my-auto space-y-6 pt-4 pb-8">
        
        {{-- EN-TÊTE INSTITUTIONNEL DYNAMIQUE --}}
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center px-4 py-1.5 rounded-full bg-slate-900 border border-slate-800 text-xs font-bold text-slate-400 tracking-wide uppercase">
                🛡️ Registre National des Diplômes
            </div>
            <h1 class="text-xl font-black text-slate-200 tracking-tight sm:text-2xl uppercase">
                {{ \App\Models\Configuration::get('site_nom', 'EXCELLENCE DIGITAL CENTER') }}
            </h1>
            <p class="text-xs text-slate-500">
                {{ \App\Models\Configuration::get('site_slogan', 'Système de vérification et d\'authentification cryptographique des compétences') }}
            </p>
        </div>

        @if($estValide)
            {{-- ══════════════════════ CAS VALIDE ══════════════════════ --}}
            
            <div class="rounded-2xl p-5 border border-emerald-500/20 bg-emerald-500/5 text-center space-y-2 shadow-xl shadow-emerald-950/20">
                <div class="w-12 h-12 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center mx-auto text-emerald-400 text-lg">
                    <i class="fas fa-check-shield"></i>
                </div>
                <h2 class="text-base font-bold text-emerald-400 uppercase tracking-wide">✓ Certificat Authentique</h2>
                <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                    Ce document a été validé et délivré officiellement par <strong>{{ \App\Models\Configuration::get('site_nom', 'EXCELLENCE DIGITAL CENTER') }}</strong>. Il est consigné de manière permanente dans notre registre de certification.
                </p>
            </div>

            {{-- CARTE DES SPÉCIFICATIONS DU BÉNÉFICIAIRE --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-xl space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800/60 pb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Détails du Titulaire</span>
                    <span class="text-xs font-mono px-2.5 py-0.5 rounded-md bg-slate-950 border border-slate-800 text-slate-300">{{ $certificat->numero_certificat }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1">
                        <span class="text-slate-500 block">Nom & Prénom de l'apprenant</span>
                        <span class="text-sm font-bold text-slate-200 block uppercase">{{ $certificat->user->name }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-slate-500 block">Programme validé</span>
                        <span class="text-sm font-bold text-emerald-400 block">{{ $certificat->formation->titre }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-slate-500 block">Date d'évaluation d'origine</span>
                        <span class="text-sm font-semibold text-slate-300 block">{{ \Carbon\Carbon::parse($certificat->delivre_le)->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-slate-500 block">Évaluation & Classement</span>
                        <span class="text-sm font-semibold text-slate-300 block">
                            Note : <strong class="text-slate-100">{{ number_format($certificat->note_obtenue, 1) }}/20</strong> 
                            <span class="text-slate-500 mx-1">|</span> 
                            Mention : <strong class="text-amber-400">{{ $certificat->mention }}</strong>
                        </span>
                    </div>
                </div>
            </div>

            {{-- HISTORIQUE DES DUPLICATAS --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-xl space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400"><i class="fas fa-history text-slate-500 mr-1.5"></i> Cycle de vie & Traçabilité documentaire</h3>
                
                <div class="space-y-3 relative before:absolute before:bottom-2 before:top-2 before:left-3.5 before:w-0.5 before:bg-slate-800">
                    @foreach($historique as $doc)
                        <div class="flex gap-4 items-start relative">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 z-10 text-[10px] border 
                                {{ $doc->id === $certificat->id ? 'bg-emerald-500/10 border-emerald-500 text-emerald-400 shadow-lg shadow-emerald-950' : 'bg-slate-950 border-slate-800 text-slate-500' }}">
                                <i class="fas {{ $doc->type === 'original' ? 'fa-certificate' : 'fa-copy' }}"></i>
                            </div>
                            <div class="p-3 rounded-xl border border-slate-800/50 bg-slate-950/40 flex-1 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <span class="text-xs font-bold block capitalize {{ $doc->id === $certificat->id ? 'text-slate-200' : 'text-slate-400' }}">
                                        Document {{ $doc->type }} {{ $doc->id === $certificat->id ? '(Consulté actuellement)' : '' }}
                                    </span>
                                    @if($doc->motif_duplicata)
                                        <span class="text-[11px] text-slate-500 block mt-0.5">Motif : {{ $doc->motif_duplicata }}</span>
                                    @endif
                                </div>
                                <span class="text-[11px] font-mono text-slate-500 shrink-0">le {{ \Carbon\Carbon::parse($doc->created_at)->format('d/m/Y à H:i') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        @else
            {{-- ══════════════════════ CAS INVALIDE ══════════════════════ --}}
            <div class="rounded-2xl p-6 border border-rose-500/20 bg-rose-500/5 text-center space-y-3 shadow-xl">
                <div class="w-12 h-12 rounded-full bg-rose-500/10 border border-rose-500/30 flex items-center justify-center mx-auto text-rose-500 text-lg">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <h2 class="text-base font-bold text-rose-500 uppercase tracking-wide">✗ Certificat Non Reconnu</h2>
                <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                    Le jeton d'authentification ou le numéro de référence transmis ne correspond à aucune pièce répertoriée dans notre école.
                </p>
                <div class="p-3 bg-slate-950 rounded-xl border border-slate-900 inline-block text-[11px] text-amber-500 font-medium">
                    ⚠️ Attention : Ce document peut être une contrefaçon ou avoir été révoqué par l'administration.
                </div>
            </div>
        @endif

    </main>

    {{-- PIED DE PAGE INSTITUTIONNEL TOTALEMENT DYNAMIQUE --}}
    <footer class="w-full max-w-2xl mx-auto border-t border-slate-900 pt-4 pb-2 text-center text-[11px] text-slate-600 space-y-1">
        <div>Organisme de Formation Technique : <strong>{{ \App\Models\Configuration::get('site_nom', 'EXCELLENCE DIGITAL CENTER') }}</strong> — {{ \App\Models\Configuration::get('site_adresse') }}</div>
        <div class="flex justify-center gap-4 text-slate-500 mt-1">
            <span><i class="fas fa-phone mr-1"></i> {{ \App\Models\Configuration::get('site_contact') }}</span>
            <span><i class="fas fa-envelope mr-1"></i> {{ \App\Models\Configuration::get('site_email') }}</span>
            <span><i class="fas fa-globe mr-1"></i> {{ \App\Models\Configuration::get('site_web') }}</span>
        </div>
    </footer>

</body>
</html>
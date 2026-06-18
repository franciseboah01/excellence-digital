@extends('layouts.admin')
@section('title', 'Configurations')
@section('page_title', '⚙️ Configurations Générales')
@section('page_subtitle', 'Gérez les constantes de marque, la sécurité et le calibrage des pièces officielles')

@section('content')
<div class="w-full mt-6">

    <form method="POST" action="{{ route('admin.configurations.update') }}" id="settingsForm" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        {{-- ══ BARRE DES ONGLETS (SESSIONS CLIQUABLES) ══ --}}
        <div class="tabs-container p-2 rounded-xl bg-slate-900 border border-slate-800">
            <div class="tabs-wrapper flex gap-2 overflow-x-auto scrollbar-none" id="tabsWrapper">
                <button type="button" onclick="showTab('institution')" id="tab-institution"
                    class="tab-btn px-4 py-2.5 rounded-lg border font-semibold text-xs transition-all whitespace-nowrap shrink-0 tab-active bg-emerald-500/10 border-emerald-500 text-emerald-400">
                    🏢 Institution & Marque
                </button>
                <button type="button" onclick="showTab('mission')" id="tab-mission"
                    class="tab-btn px-4 py-2.5 rounded-lg border font-semibold text-xs transition-all whitespace-nowrap shrink-0 bg-slate-950 border-slate-800 text-slate-400 hover:text-emerald-400 hover:border-emerald-500/30">
                    🎯 Mission & Valeurs
                </button>
                <button type="button" onclick="showTab('galerie')" id="tab-galerie"
                    class="tab-btn px-4 py-2.5 rounded-lg border font-semibold text-xs transition-all whitespace-nowrap shrink-0 bg-slate-950 border-slate-800 text-slate-400 hover:text-emerald-400 hover:border-emerald-500/30">
                    🖼️ Galerie
                </button>
                <button type="button" onclick="showTab('stockage')" id="tab-stockage"
                    class="tab-btn px-4 py-2.5 rounded-lg border font-semibold text-xs transition-all whitespace-nowrap shrink-0 bg-slate-950 border-slate-800 text-slate-400 hover:text-emerald-400 hover:border-emerald-500/30">
                    📁 Stockage & Fichiers
                </button>
                <button type="button" onclick="showTab('securite')" id="tab-securite"
                    class="tab-btn px-4 py-2.5 rounded-lg border font-semibold text-xs transition-all whitespace-nowrap shrink-0 bg-slate-950 border-slate-800 text-slate-400 hover:text-emerald-400 hover:border-emerald-500/30">
                    🔐 Sécurité & QCM
                </button>
                <button type="button" onclick="showTab('certificat')" id="tab-certificat"
                    class="tab-btn px-4 py-2.5 rounded-lg border font-semibold text-xs transition-all whitespace-nowrap shrink-0 bg-slate-950 border-slate-800 text-slate-400 hover:text-emerald-400 hover:border-emerald-500/30">
                    📜 Maquette Certificats
                </button>
                <button type="button" onclick="showTab('marque')" id="tab-marque"
                    class="tab-btn px-4 py-2.5 rounded-lg border font-semibold text-xs transition-all whitespace-nowrap shrink-0 bg-slate-950 border-slate-800 text-slate-400 hover:text-emerald-400 hover:border-emerald-500/30">
                    📊 Stats & Arguments
                </button>
            </div>
        </div>

        {{-- ══ PANEL 0 : INSTITUTION & MARQUE ══ --}}
        <div id="panel-institution" class="settings-panel space-y-6">
            <div class="edc-card p-6 sm:p-8 space-y-5">
                <h3 class="text-lg font-bold" style="color: var(--edc-text-primary);">🏢 Identité d'Établissement & Coordonnées Publiques</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="edc-label">Nom de l'organisme</label>
                        <input type="text" name="site_nom" value="{{ \App\Models\Configuration::get('site_nom', 'EXCELLENCE DIGITAL CENTER') }}" class="edc-input">
                    </div>
                    <div>
                        <label class="edc-label">Slogan / Sous-titre de vérification</label>
                        <input type="text" name="site_slogan" value="{{ \App\Models\Configuration::get('site_slogan') }}" class="edc-input">
                    </div>
                </div>

                <div>
                    <label class="edc-label">Adresse Géographique</label>
                    <input type="text" name="site_adresse" value="{{ \App\Models\Configuration::get('site_adresse') }}" class="edc-input">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="edc-label">Ligne Téléphonique</label>
                        <input type="text" name="site_contact" value="{{ \App\Models\Configuration::get('site_contact') }}" class="edc-input">
                    </div>
                    <div>
                        <label class="edc-label">Email de contact</label>
                        <input type="email" name="site_email" value="{{ \App\Models\Configuration::get('site_email') }}" class="edc-input">
                    </div>
                    <div>
                        <label class="edc-label">Adresse Web officielle</label>
                        <input type="text" name="site_web" value="{{ \App\Models\Configuration::get('site_web') }}" class="edc-input">
                    </div>
                        
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="edc-label">Numéro WhatsApp (format international sans +)</label>
                            <input type="text" name="site_whatsapp" 
                                value="{{ \App\Models\Configuration::get('site_whatsapp', '2250700000000') }}" 
                                class="edc-input" placeholder="2250700000000">
                            <p class="text-xs mt-1" style="color: var(--edc-text-muted);">Sans le signe +, ex: 225XXXXXXXX</p>
                        </div>
                        <div>
                            <label class="edc-label">Ville / Localité</label>
                            <input type="text" name="site_ville" 
                                value="{{ \App\Models\Configuration::get('site_ville', 'Korhogo / Sirasso') }}" 
                                class="edc-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="edc-label">Description courte (footer, meta)</label>
                            <textarea name="site_description" rows="2" class="edc-input">{{ \App\Models\Configuration::get('site_description', 'Services bureautiques, digital et formation à Korhogo / Sirasso. Votre centre de référence pour réussir dans l\'univers digital.') }}</textarea>
                        </div>
                        <div>
                            <label class="edc-label">Devise / Signature</label>
                            <input type="text" name="site_devise" 
                                value="{{ \App\Models\Configuration::get('site_devise', 'Former • Créer • Réussir') }}" 
                                class="edc-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="edc-label">Pays</label>
                            <input type="text" name="site_pays" 
                                value="{{ \App\Models\Configuration::get('site_pays', 'Côte d\'Ivoire') }}" 
                                class="edc-input">
                        </div>
                        <div>
                            <label class="edc-label">Copyright texte</label>
                            <input type="text" name="site_copyright" 
                                value="{{ \App\Models\Configuration::get('site_copyright', '© ' . date('Y') . ' Excellence Digital Center — Tous droits réservés') }}" 
                                class="edc-input">
                        </div>
                    </div>

                </div>

                    {{-- Google Maps embed --}}
                <div>
                    <label class="edc-label">Code embed Google Maps</label>
                    <textarea name="site_maps_embed" rows="4" class="edc-input" placeholder="<iframe src='https://...' ...></iframe>">{{ \App\Models\Configuration::get('site_maps_embed') }}</textarea>
                </div>

            </div>
        </div>

        {{-- ══ PANEL 1 : STOCKAGE & FICHIERS ══ --}}
        <div id="panel-stockage" class="settings-panel space-y-6">
            <div class="edc-card p-6 sm:p-8 space-y-5">
                <h3 class="text-lg font-bold" style="color: var(--edc-text-primary);">📁 Paramètres d'upload globaux</h3>
                
                <div>
                    <label class="edc-label">Taille maximale des fichiers (MB)</label>
                    <input type="number" name="upload_taille_max_mb" min="1" max="100"
                        value="{{ \App\Models\Configuration::get('upload_taille_max_mb', 20) }}" class="edc-input">
                    <p class="text-xs mt-1" style="color: var(--edc-text-muted);">
                        Actuellement configuré à : {{ \App\Models\Configuration::get('upload_taille_max_mb', 20) }} MB
                    </p>
                </div>

                <div>
                    <label class="edc-label">Types de fichiers autorisés (séparés par des virgules)</label>
                    <input type="text" name="upload_types_autorises"
                        value="{{ \App\Models\Configuration::get('upload_types_autorises', 'pdf,doc,docx,epub') }}"
                        class="edc-input" placeholder="pdf,doc,docx,epub,ppt,pptx">
                    <p class="text-xs mt-1" style="color: var(--edc-text-muted);">Ex : pdf,doc,docx,epub,ppt,pptx,xls,xlsx</p>
                </div>

                <div>
                    <label class="edc-label">Taille maximale des images (MB)</label>
                    <input type="number" name="upload_image_taille_max_mb" min="1" max="10"
                        value="{{ \App\Models\Configuration::get('upload_image_taille_max_mb', 2) }}" class="edc-input">
                </div>
            </div>
        </div>

        {{-- ══ PANEL 2 : SÉCURITÉ & QCM ══ --}}
        <div id="panel-securite" class="settings-panel space-y-6" style="display:none;">
            <div class="edc-card p-6 sm:p-8 space-y-5">
                <h3 class="text-lg font-bold" style="color: var(--edc-text-primary);">🔐 Sécurité des accès & Évaluations</h3>

                <div>
                    <label class="edc-label">Durée de validité des liens temporaires (minutes)</label>
                    <input type="number" name="url_signee_expiration_minutes" min="5" max="1440"
                        value="{{ \App\Models\Configuration::get('url_signee_expiration_minutes', 30) }}" class="edc-input">
                    <p class="text-xs mt-1" style="color: var(--edc-text-muted);">
                        Les URLs d'accès aux fichiers privés expireront de manière stricte après ce délai.
                    </p>
                </div>

                <div>
                    <label class="edc-label">Note minimale de validation par défaut pour les QCMs (/20)</label>
                    <input type="number" name="qcm_note_minimale" min="0" max="20"
                        value="{{ \App\Models\Configuration::get('qcm_note_minimale', 14) }}" class="edc-input">
                    <p class="text-xs mt-1" style="color: var(--edc-text-muted);">
                        Seuil requis pour générer automatiquement le certificat de réussite de l'apprenant.
                    </p>
                </div>

                <div class="rounded-xl p-4" style="background-color: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.20);">
                    <p class="text-sm font-semibold mb-2" style="color: var(--edc-primary-light);">🔒 Rappel du fonctionnement sécurisé</p>
                    <ul class="text-xs space-y-1.5" style="color: var(--edc-text-secondary);">
                        <li>• Les fichiers sont isolés du répertoire public (stockage crypté ou cloisonné).</li>
                        <li>• Chaque clic génère dynamiquement une clé de hachage temporaire unique pour l'apprenant.</li>
                        <li>• Aucun lien direct de téléchargement ne peut être partagé en externe de façon permanente.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ══ PANEL 3 : MAQUETTE & CERTIFICATS ══ --}}
        <div id="panel-certificat" class="settings-panel space-y-6" style="display:none;">
            <div class="edc-card p-6 sm:p-8 space-y-6">
                
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold" style="color: var(--edc-text-primary);">📜 Gabarit et Calibrage Pro (A4 Paysage - 297x210mm)</h3>
                    <span class="text-xs px-2.5 py-1 rounded-full font-mono bg-slate-950 border border-slate-800 text-emerald-400">Positionnement absolu</span>
                </div>

                {{-- SECTION A : IMPORTATION DE LA MAQUETTE VIERGE --}}
                <div class="space-y-3">
                    <label class="edc-label text-xs uppercase tracking-wider text-slate-400">1. Fond de page & Charte graphique</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl border border-slate-800 bg-slate-950 md:col-span-2 flex flex-col justify-between">
                            <div>
                                <input type="file" name="certificat_background_file" accept="image/*" onchange="previewCertificat(this)" class="text-xs text-slate-300 w-full">
                                <p class="text-[11px] text-slate-500 mt-2">Format requis : <strong>A4 Paysage (297mm × 210mm)</strong>. Exportez votre design depuis Canva ou Photoshop en PNG haute définition sans aucun texte dynamique.</p>
                            </div>
                            <div class="flex items-center gap-3 mt-4 pt-3 border-t border-slate-900">
                                <div class="flex flex-col w-full">
                                    <span class="text-xs text-slate-400 mb-1">Couleur par défaut du texte</span>
                                    <div class="flex gap-2">
                                        @php $color = \App\Models\Configuration::get('certificat_font_color_name', '#1e293b'); @endphp
                                        <input type="color" value="{{ $color }}" class="bg-slate-950 border border-slate-800 p-1 rounded-lg h-9 w-12 cursor-pointer" oninput="document.getElementById('color_text_input').value = this.value">
                                        <input type="text" id="color_text_input" name="certificat_font_color_name" value="{{ $color }}" class="edc-input h-9 text-xs" oninput="this.previousElementSibling.value = this.value">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-2 rounded-xl border border-slate-800 bg-slate-950 flex items-center justify-center min-h-[120px]">
                            @php $bgPath = \App\Models\Configuration::get('certificat_background'); @endphp
                            <div id="cert-preview" class="{{ $bgPath ? '' : 'hidden' }}">
                                <img id="cert-preview-img" src="{{ $bgPath ? asset('storage/' . $bgPath) : '' }}" alt="Gabarit" class="max-h-28 object-contain rounded border border-slate-700 p-0.5 bg-slate-900">
                            </div>
                            @if(!$bgPath)
                                <span id="cert-preview-empty" class="text-xs text-slate-600"><i class="fas fa-image mr-1"></i> Aucun fond enregistré</span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="border-slate-800">

                {{-- SECTION B : CALIBRAGE DES AXES ET TYPOGRAPHIES --}}
                <div class="space-y-5">
                    <label class="edc-label text-xs uppercase tracking-wider text-slate-400 block">2. Alignements des données dynamiques (Axes X / Y en pixels)</label>
                    
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-900 space-y-3">
                        <div class="text-xs font-bold text-slate-300 flex items-center gap-1.5">📌 Numéro Unique du Certificat <span class="text-[10px] font-normal text-slate-500">(Ex: N° CERT-2026-000154)</span></div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Axe X (Gauche)</label>
                                <input type="number" name="certificat_axis_x_numero" value="{{ \App\Models\Configuration::get('certificat_axis_x_numero', 240) }}" class="edc-input text-xs p-2">
                            </div>
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Axe Y (Haut)</label>
                                <input type="number" name="certificat_axis_y_numero" value="{{ \App\Models\Configuration::get('certificat_axis_y_numero', 20) }}" class="edc-input text-xs p-2">
                            </div>
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Taille Police</label>
                                <input type="number" name="certificat_font_size_numero" value="{{ \App\Models\Configuration::get('certificat_font_size_numero', 12) }}" class="edc-input text-xs p-2">
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-900 space-y-3">
                        <div class="text-xs font-bold text-emerald-400 flex items-center gap-1.5">⭐ Nom & Prénom de l'Apprenant <span class="text-[10px] font-normal text-slate-500">(Élément central principal)</span></div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Axe X (Gauche)</label>
                                <input type="number" name="certificat_axis_x_name" value="{{ \App\Models\Configuration::get('certificat_axis_x_name', 148) }}" class="edc-input text-xs p-2">
                            </div>
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Axe Y (Haut)</label>
                                <input type="number" name="certificat_axis_y_name" value="{{ \App\Models\Configuration::get('certificat_axis_y_name', 105) }}" class="edc-input text-xs p-2">
                            </div>
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Taille Police</label>
                                <input type="number" name="certificat_font_size_name" value="{{ \App\Models\Configuration::get('certificat_font_size_name', 28) }}" class="edc-input text-xs p-2">
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-900 space-y-3">
                        <div class="text-xs font-bold text-slate-300 flex items-center gap-1.5">📖 Intitulé du Programme & Spécialité</div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Axe X (Gauche)</label>
                                <input type="number" name="certificat_axis_x_formation" value="{{ \App\Models\Configuration::get('certificat_axis_x_formation', 148) }}" class="edc-input text-xs p-2">
                            </div>
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Axe Y (Haut)</label>
                                <input type="number" name="certificat_axis_y_formation" value="{{ \App\Models\Configuration::get('certificat_axis_y_formation', 135) }}" class="edc-input text-xs p-2">
                            </div>
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Taille Police</label>
                                <input type="number" name="certificat_font_size_formation" value="{{ \App\Models\Configuration::get('certificat_font_size_formation', 20) }}" class="edc-input text-xs p-2">
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-900 space-y-3">
                        <div class="text-xs font-bold text-slate-300 flex items-center justify-between">
                            <span>📊 Notes, Mentions & Performances</span>
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-1 text-[11px] font-normal text-slate-400 cursor-pointer">
                                    <input type="checkbox" name="certificat_show_note" value="1" {{ \App\Models\Configuration::get('certificat_show_note', 1) == 1 ? 'checked' : '' }} class="rounded border-slate-800 bg-slate-900 text-emerald-500 focus:ring-0"> Afficher Note
                                </label>
                                <label class="flex items-center gap-1 text-[11px] font-normal text-slate-400 cursor-pointer">
                                    <input type="checkbox" name="certificat_show_mention" value="1" {{ \App\Models\Configuration::get('certificat_show_mention', 1) == 1 ? 'checked' : '' }} class="rounded border-slate-800 bg-slate-900 text-emerald-500 focus:ring-0"> Afficher Mention
                                </label>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Axe X (Gauche)</label>
                                <input type="number" name="certificat_axis_x_performance" value="{{ \App\Models\Configuration::get('certificat_axis_x_performance', 148) }}" class="edc-input text-xs p-2">
                            </div>
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Axe Y (Haut)</label>
                                <input type="number" name="certificat_axis_y_performance" value="{{ \App\Models\Configuration::get('certificat_axis_y_performance', 155) }}" class="edc-input text-xs p-2">
                            </div>
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Taille Police</label>
                                <input type="number" name="certificat_font_size_perf" value="{{ \App\Models\Configuration::get('certificat_font_size_perf', 12) }}" class="edc-input text-xs p-2">
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-900 space-y-3">
                        <div class="text-xs font-bold text-slate-300 flex items-center justify-between">
                            <span>🔐 Bloc d'Authentification (Lieu, Date, Signatures & QR Code)</span>
                            <label class="flex items-center gap-1 text-[11px] font-normal text-slate-400 cursor-pointer">
                                <input type="checkbox" name="certificat_show_qrcode" value="1" {{ \App\Models\Configuration::get('certificat_show_qrcode', 1) == 1 ? 'checked' : '' }} class="rounded border-slate-800 bg-slate-900 text-emerald-500 focus:ring-0"> Inclure QR Code de vérification
                            </label>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Axe X (Lieu & Date)</label>
                                <input type="number" name="certificat_axis_x_metadata" value="{{ \App\Models\Configuration::get('certificat_axis_x_metadata', 40) }}" class="edc-input text-xs p-2">
                            </div>
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Axe Y (Lieu & Date)</label>
                                <input type="number" name="certificat_axis_y_metadata" value="{{ \App\Models\Configuration::get('certificat_axis_y_metadata', 185) }}" class="edc-input text-xs p-2">
                            </div>
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Dimension QR Code</label>
                                <input type="number" name="certificat_qr_size" min="40" max="200" value="{{ \App\Models\Configuration::get('certificat_qr_size', 70) }}" class="edc-input text-xs p-2" placeholder="Taille en px">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-800">

                {{-- SECTION C : CALIBRAGE ET POSITIONNEMENT DU QR CODE --}}
                <div class="edc-card p-6 mt-6 rounded-2xl border border-slate-800 bg-slate-900/50 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <h4 class="text-sm font-bold text-slate-300 uppercase tracking-wide flex items-center gap-2">
                            <i class="fas fa-qrcode text-emerald-400"></i> Options & Positionnement du QR Code
                        </h4>
                        
                        {{-- Toggle pour Activer / Désactiver le QR Code --}}
                        <div class="flex items-center gap-3 toggle-wrapper">
                            <span class="toggle-status text-xs font-bold w-16 text-right {{ \App\Models\Configuration::get('certificat_show_qrcode', 1) ? 'text-emerald-400' : 'text-slate-500' }}">
                                {{ \App\Models\Configuration::get('certificat_show_qrcode', 1) ? 'Activé' : 'Désactivé' }}
                            </span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="certificat_show_qrcode" value="1" 
                                    class="sr-only peer structural-toggle" 
                                    {{ \App\Models\Configuration::get('certificat_show_qrcode', 1) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Champs de réglage des dimensions et coordonnées (Masqués ou grisés via CSS si désactivé) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        
                        {{-- Taille du QR Code --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-2">
                                Taille du carré <span class="text-slate-600">(en pixels)</span>
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <input type="number" name="certificat_qr_size" 
                                    value="{{ \App\Models\Configuration::get('certificat_qr_size', 120) }}" 
                                    min="40" max="300" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-100 focus:outline-none focus:border-emerald-500 transition-colors">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-xs text-slate-500">px</div>
                            </div>
                        </div>

                        {{-- Axe X --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-2">
                                Position Horizontale (Axe X)
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <input type="number" name="certificat_axis_x_metadata" 
                                    value="{{ \App\Models\Configuration::get('certificat_axis_x_metadata', 50) }}" 
                                    min="0" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-100 focus:outline-none focus:border-emerald-500 transition-colors">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-xs text-slate-500">px</div>
                            </div>
                        </div>

                        {{-- Axe Y --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-2">
                                Position Verticale (Axe Y)
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <input type="number" name="certificat_axis_y_metadata" 
                                    value="{{ \App\Models\Configuration::get('certificat_axis_y_metadata', 450) }}" 
                                    min="0" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-100 focus:outline-none focus:border-emerald-500 transition-colors">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-xs text-slate-500">px</div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- SECTION D : REGULATION DUPLICATA --}}
                <div class="toggle-container p-4 rounded-xl border border-slate-800 bg-slate-950 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="space-y-0.5">
                        <label class="text-sm font-bold block" style="color: var(--edc-text-primary);">📜 Gestion des Duplicatas</label>
                        <span class="text-xs block" style="color: var(--edc-text-muted);">Autoriser ou bloquer les demandes de réédition en autonomie.</span>
                    </div>
                    @php $duplicata = \App\Models\Configuration::get('certificat_duplicata_active', '0'); @endphp
                    <label class="toggle-wrapper flex items-center gap-3 cursor-pointer relative select-none shrink-0">
                        <input type="checkbox" name="certificat_duplicata_active" value="1" class="absolute opacity-0 w-0 h-0 structural-toggle" {{ $duplicata == '1' ? 'checked' : '' }}>
                        <span class="toggle-slider w-11 h-6 bg-slate-850 rounded-full relative transition-all duration-200 block border border-slate-700 after:content-[''] after:absolute after:w-4 after:h-4 after:bg-white after:rounded-full after:top-0.5 after:left-0.5 after:transition-all"></span>
                        <span class="toggle-status text-xs font-bold w-16 {{ $duplicata == '1' ? 'text-emerald-400' : 'text-slate-500' }}">
                            {{ $duplicata == '1' ? 'Activé' : 'Désactivé' }}
                        </span>
                    </label>
                </div>

                {{-- ══ PRIX DU DUPLICATA ══ --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="text-xs font-semibold text-slate-400 block mb-2">💰 Prix du duplicata (FCFA)</label>
                        <input type="number" name="duplicata_prix" 
                            value="{{ \App\Models\Configuration::get('duplicata_prix', 1000) }}" 
                            min="500" max="10000" class="edc-input w-full">
                        <p class="text-[11px] text-slate-500 mt-1">Prix facturé au client pour une demande de duplicata.</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-400 block mb-2">⏱️ Délai de traitement (jours)</label>
                        <input type="number" name="duplicata_delai_jours" 
                            value="{{ \App\Models\Configuration::get('duplicata_delai_jours', 7) }}" 
                            min="1" max="30" class="edc-input w-full">
                        <p class="text-[11px] text-slate-500 mt-1">Délai estimé pour la validation administrative.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ PANEL 4 : STATS & ARGUMENTS ══ --}}
        <div id="panel-marque" class="settings-panel space-y-6" style="display:none;">
            
            {{-- ━━━ SECTION A : STATS DU HERO ━━━ --}}
            <div class="edc-card p-6 sm:p-8 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold" style="color: var(--edc-text-primary);">
                        📈 Chiffres Clés (Hero)
                    </h3>
                    <button type="button" onclick="addStatRow()"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20 transition">
                        + Ajouter une stat
                    </button>
                </div>
                <p class="text-xs text-slate-500 -mt-2">
                    Affichés sous le titre principal de la page d'accueil.
                </p>

                <div id="stats-container" class="space-y-3">
                    @php $stats = json_decode(\App\Models\Configuration::get('site_stats', '[]'), true); @endphp
                    @foreach($stats as $i => $stat)
                    <div class="stat-row flex flex-col sm:flex-row gap-3 p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <input type="text" name="site_stats[{{ $i }}][valeur]" value="{{ $stat['valeur'] ?? '' }}"
                            placeholder="Valeur (ex: 500+)" class="edc-input flex-1">
                        <input type="text" name="site_stats[{{ $i }}][description]" value="{{ $stat['description'] ?? '' }}"
                            placeholder="Description (ex: Clients satisfaits)" class="edc-input flex-[2]">
                        <button type="button" onclick="this.closest('.stat-row').remove()"
                            class="px-2 py-1 text-xs text-red-400 hover:text-red-300">✕</button>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ━━━ SECTION B : POURQUOI NOUS ━━━ --}}
            <div class="edc-card p-6 sm:p-8 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold" style="color: var(--edc-text-primary);">
                        🎯 Arguments "Pourquoi Nous"
                    </h3>
                    <button type="button" onclick="addArgumentRow()"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20 transition">
                        + Ajouter un argument
                    </button>
                </div>
                <p class="text-xs text-slate-500 -mt-2">
                    Grille 2×2 ou 4 colonnes sur la page d'accueil.
                </p>

                <div id="arguments-container" class="space-y-3">
                    @php $arguments = json_decode(\App\Models\Configuration::get('site_pourquoi_nous', '[]'), true); @endphp
                    @foreach($arguments as $i => $arg)
                    <div class="arg-row flex flex-col sm:flex-row gap-3 p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <input type="text" name="site_pourquoi_nous[{{ $i }}][icone]" value="{{ $arg['icone'] ?? '' }}"
                            placeholder="Icône (ex: ⚡)" class="edc-input w-24">
                        <input type="text" name="site_pourquoi_nous[{{ $i }}][titre]" value="{{ $arg['titre'] ?? '' }}"
                            placeholder="Titre (ex: Rapidité)" class="edc-input flex-1">
                        <input type="text" name="site_pourquoi_nous[{{ $i }}][description]" value="{{ $arg['description'] ?? '' }}"
                            placeholder="Description courte" class="edc-input flex-[2]">
                        <button type="button" onclick="this.closest('.arg-row').remove()"
                            class="px-2 py-1 text-xs text-red-400 hover:text-red-300">✕</button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ══ PANEL 5 : GALERIE ══ --}}
        <div id="panel-galerie" class="settings-panel space-y-6" style="display:none;">
            <div class="edc-card p-6 sm:p-8 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold" style="color: var(--edc-text-primary);">
                        🖼️ Galerie d'images (format 9:16)
                    </h3>
                    <button type="button" onclick="addGalerieRow()"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20 transition">
                        + Ajouter une image
                    </button>
                </div>
                <p class="text-xs text-slate-500 -mt-2">
                    Images au format portrait. Défilement automatique sur la page d'accueil.
                </p>

                <div id="galerie-container" class="space-y-4">
                    @php $galeries = json_decode(\App\Models\Configuration::get('site_galeries', '[]'), true); @endphp
                    @foreach($galeries as $i => $img)
                    <div class="galerie-row flex flex-col sm:flex-row gap-3 p-3 rounded-xl bg-slate-950 border border-slate-800 items-start">
                        <input type="text" name="site_galeries[{{ $i }}][titre]" value="{{ $img['titre'] ?? '' }}"
                            placeholder="Titre (optionnel)" class="edc-input flex-1">
                        <input type="text" name="site_galeries[{{ $i }}][image]" value="{{ $img['image'] ?? '' }}"
                            placeholder="Chemin (ex: galerie/photo1.jpg)" class="edc-input flex-[2]">
                        @if(!empty($img['image']))
                        <img src="{{ asset('storage/' . $img['image']) }}" class="h-12 w-8 object-cover rounded border border-slate-700">
                        @endif
                        <button type="button" onclick="this.closest('.galerie-row').remove()"
                            class="px-2 py-1 text-xs text-red-400 hover:text-red-300">✕</button>
                    </div>
                    @endforeach
                </div>

                {{-- Upload multiple --}}
                <div class="mt-4 p-4 rounded-xl border border-dashed border-slate-700 bg-slate-950/50">
                    <p class="text-xs text-slate-400 mb-2">📤 Uploader des images (seront ajoutées à la galerie)</p>
                    <input type="file" name="galerie_files[]" multiple accept="image/*" class="text-xs text-slate-300 w-full">
                    <p class="text-[10px] text-slate-500 mt-1">
                        Format 9:16 recommandé. Les images uploadées remplaceront la galerie actuelle.
                    </p>
                </div>
            </div>
        </div>

        {{-- ══ PANEL 6 : MISSION & VALEURS ══ --}}
        <div id="panel-mission" class="settings-panel space-y-6" style="display:none;">

            {{-- MISSION --}}
            <div class="edc-card p-6 sm:p-8 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold" style="color: var(--edc-text-primary);">🎯 Mission</h3>
                    <button type="button" onclick="addMissionRow()"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20 transition">+ Ajouter</button>
                </div>
                <div id="mission-container" class="space-y-3">
                    @php $mission = json_decode(\App\Models\Configuration::get('site_mission', '[]'), true); @endphp
                    @foreach($mission as $i => $m)
                    <div class="mission-row flex flex-col sm:flex-row gap-3 p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <input type="text" name="site_mission[{{ $i }}][icone]" value="{{ $m['icone'] ?? '' }}"
                            placeholder="Icône (ex: 🎯)" class="edc-input w-24">
                        <input type="text" name="site_mission[{{ $i }}][titre]" value="{{ $m['titre'] ?? '' }}"
                            placeholder="Titre" class="edc-input flex-1">
                        <input type="text" name="site_mission[{{ $i }}][description]" value="{{ $m['description'] ?? '' }}"
                            placeholder="Description" class="edc-input flex-[2]">
                        <button type="button" onclick="this.closest('.mission-row').remove()"
                            class="px-2 py-1 text-xs text-red-400 hover:text-red-300">✕</button>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- VALEURS --}}
            <div class="edc-card p-6 sm:p-8 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold" style="color: var(--edc-text-primary);">💎 Valeurs</h3>
                    <button type="button" onclick="addValeurRow()"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20 transition">+ Ajouter</button>
                </div>
                <div id="valeurs-container" class="space-y-3">
                    @php $valeurs = json_decode(\App\Models\Configuration::get('site_valeurs', '[]'), true); @endphp
                    @foreach($valeurs as $i => $v)
                    <div class="valeur-row flex flex-col sm:flex-row gap-3 p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <input type="text" name="site_valeurs[{{ $i }}][icone]" value="{{ $v['icone'] ?? '' }}"
                            placeholder="Icône (ex: 💎)" class="edc-input w-24">
                        <input type="text" name="site_valeurs[{{ $i }}][titre]" value="{{ $v['titre'] ?? '' }}"
                            placeholder="Titre" class="edc-input flex-1">
                        <input type="text" name="site_valeurs[{{ $i }}][description]" value="{{ $v['description'] ?? '' }}"
                            placeholder="Description" class="edc-input flex-[2]">
                        <button type="button" onclick="this.closest('.valeur-row').remove()"
                            class="px-2 py-1 text-xs text-red-400 hover:text-red-300">✕</button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ══ STICKY BANNER BARRE DE SAUVEGARDE GLOBALE ══ --}}
        <div class="form-actions sticky bottom-0 z-50 py-4 bg-gradient-to-t from-slate-950 via-slate-950/90 to-transparent">
            <div class="sticky-inner bg-slate-900 border border-slate-800 rounded-xl p-3 px-5 flex items-center justify-end gap-4 shadow-2xl">
                <span class="save-hint text-xs font-semibold text-amber-500 flex items-center gap-1.5 opacity-0 transition-opacity duration-300" id="saveHint">
                    <i class="fas fa-exclamation-circle"></i> Modifications non enregistrées
                </span>
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-lg text-xs font-bold transition-all shadow-lg shadow-emerald-500/10">
                    💾 Sauvegarder les configurations
                </button>
            </div>
        </div>
    </form>

    {{-- ══ CONTROLEUR D'ACTION INDÉPENDANTE (STYLE MAINTENANCE WIDGET) ══ --}}
    <div id="panel-action-test" class="settings-panel mt-6 mb-8" style="display:none;">
        <div class="edc-card p-6 sm:p-8">
            <h3 class="text-md font-bold mb-3" style="color: var(--edc-text-primary);"><i class="fas fa-vial text-amber-500 mr-1.5"></i> Calibrage & Validation à blanc</h3>
            <div class="p-4 rounded-xl border border-amber-500/20 bg-amber-500/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="text-xs font-bold text-amber-400">Générer un Certificat de Démonstration (Spécimen)</div>
                    <div class="text-[11px] text-slate-400 max-w-xl">Cette action crée un PDF de test avec un profil fictif pour vérifier instantanément si vos axes de coordonnées X / Y sont parfaits.</div>
                </div>
                <div class="shrink-0 w-full md:w-auto">
                    <button type="button" class="w-full md:w-auto px-4 py-2 border border-amber-500/30 text-amber-400 bg-amber-500/10 hover:bg-amber-500 hover:text-slate-950 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-file-pdf"></i> Générer un PDF spécimen
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    /* Masquer la scrollbar proprement */
    .scrollbar-none::-webkit-scrollbar { display: none; }
    .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
    
    /* Style structurel interne pour le Toggle Switch */
    .structural-toggle:checked + .toggle-slider { background-color: var(--edc-primary-light, #10b981); border-color: transparent; }
    .structural-toggle:checked + .toggle-slider::after { transform: translateX(20px); }
    .structural-toggle + .toggle-slider::after { background: #0f172a; top:3px; left:3px; }
</style>

<script>
let formDirty = false;

// Système de basculement dynamique et unique des onglets
function showTab(key) {
    // 1. Masquer tous les panels principaux
    document.querySelectorAll('.settings-panel').forEach(p => p.style.display = 'none');
    
    // 2. Réinitialiser les classes de tous les boutons d'onglets
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.className = "tab-btn px-4 py-2.5 rounded-lg border font-semibold text-xs transition-all whitespace-nowrap shrink-0 bg-slate-950 border-slate-800 text-slate-400 hover:text-emerald-400 hover:border-emerald-500/30";
    });
    
    // 3. Activer visuellement l'onglet sélectionné
    const currentBtn = document.getElementById('tab-' + key);
    if (currentBtn) {
        currentBtn.className = "tab-btn px-4 py-2.5 rounded-lg border font-semibold text-xs transition-all whitespace-nowrap shrink-0 tab-active bg-emerald-500/10 border-emerald-500 text-emerald-400";
        currentBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
    
    // 4. Affichage conditionnel des conteneurs de formulaires et du widget de test
    if (key === 'certificat') {
        document.getElementById('panel-certificat').style.display = 'block';
        if (document.getElementById('panel-action-test')) {
            document.getElementById('panel-action-test').style.display = 'block';
        }
    } else {
        const targetPanel = document.getElementById('panel-' + key);
        if (targetPanel) targetPanel.style.display = 'block';
        
        if (document.getElementById('panel-action-test')) {
            document.getElementById('panel-action-test').style.display = 'none';
        }
    }
}

// Initialisation forcée sur le premier onglet au chargement complet du DOM
document.addEventListener('DOMContentLoaded', () => {
    showTab('institution');
    
    // Initialisation des écouteurs pour changer la couleur du label de statut des Toggles
    document.querySelectorAll('.structural-toggle').forEach(cb => {
        cb.addEventListener('change', function() {
            const wrapper = this.closest('.toggle-wrapper');
            const status = wrapper.querySelector('.toggle-status');
            if (!status) return;
            status.textContent = this.checked ? 'Activé' : 'Désactivé';
            status.className = 'toggle-status text-xs font-bold w-16 ' + (this.checked ? 'text-emerald-400' : 'text-slate-500');
        });
    });
});

// Aperçu direct du fond d'écran du certificat lors du téléversement
function previewCertificat(input) {
    const wrapper = document.getElementById('cert-preview');
    const img = document.getElementById('cert-preview-img');
    const emptyLabel = document.getElementById('cert-preview-empty');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { 
            if (img) img.src = e.target.result; 
            if (wrapper) wrapper.classList.remove('hidden'); 
            if (emptyLabel) emptyLabel.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Compteurs pour les nouveaux éléments
let statIndex = {{ count($stats) }};
let argIndex = {{ count($arguments) }};
let galerieIndex = {{ count($galeries) }};

function addStatRow() {
    const container = document.getElementById('stats-container');
    const html = `
        <div class="stat-row flex flex-col sm:flex-row gap-3 p-3 rounded-xl bg-slate-950 border border-slate-800">
            <input type="text" name="site_stats[${statIndex}][valeur]" placeholder="Valeur (ex: 500+)" class="edc-input flex-1">
            <input type="text" name="site_stats[${statIndex}][description]" placeholder="Description" class="edc-input flex-[2]">
            <button type="button" onclick="this.closest('.stat-row').remove()" class="px-2 py-1 text-xs text-red-400 hover:text-red-300">✕</button>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    statIndex++;
}

function addArgumentRow() {
    const container = document.getElementById('arguments-container');
    const html = `
        <div class="arg-row flex flex-col sm:flex-row gap-3 p-3 rounded-xl bg-slate-950 border border-slate-800">
            <input type="text" name="site_pourquoi_nous[${argIndex}][icone]" placeholder="Icône (ex: ⚡)" class="edc-input w-24">
            <input type="text" name="site_pourquoi_nous[${argIndex}][titre]" placeholder="Titre" class="edc-input flex-1">
            <input type="text" name="site_pourquoi_nous[${argIndex}][description]" placeholder="Description" class="edc-input flex-[2]">
            <button type="button" onclick="this.closest('.arg-row').remove()" class="px-2 py-1 text-xs text-red-400 hover:text-red-300">✕</button>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    argIndex++;
}

function addGalerieRow() {
    const container = document.getElementById('galerie-container');
    const html = `
        <div class="galerie-row flex flex-col sm:flex-row gap-3 p-3 rounded-xl bg-slate-950 border border-slate-800 items-start">
            <input type="text" name="site_galeries[${galerieIndex}][titre]" placeholder="Titre" class="edc-input flex-1">
            <input type="text" name="site_galeries[${galerieIndex}][image]" placeholder="Chemin image" class="edc-input flex-[2]">
            <button type="button" onclick="this.closest('.galerie-row').remove()" class="px-2 py-1 text-xs text-red-400 hover:text-red-300">✕</button>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    galerieIndex++;
}

// Ajouter marque et galerie dans le showTab
const originalShowTab = showTab;
showTab = function(key) {
    originalShowTab(key);
    document.getElementById('panel-marque').style.display = (key === 'marque') ? 'block' : 'none';
    document.getElementById('panel-galerie').style.display = (key === 'galerie') ? 'block' : 'none';
};

let missionIndex = {{ count($mission ?? []) }};
let valeurIndex = {{ count($valeurs ?? []) }};

function addMissionRow() {
    const c = document.getElementById('mission-container');
    c.insertAdjacentHTML('beforeend', `
        <div class="mission-row flex flex-col sm:flex-row gap-3 p-3 rounded-xl bg-slate-950 border border-slate-800">
            <input type="text" name="site_mission[${missionIndex}][icone]" placeholder="Icône" class="edc-input w-24">
            <input type="text" name="site_mission[${missionIndex}][titre]" placeholder="Titre" class="edc-input flex-1">
            <input type="text" name="site_mission[${missionIndex}][description]" placeholder="Description" class="edc-input flex-[2]">
            <button type="button" onclick="this.closest('.mission-row').remove()" class="px-2 py-1 text-xs text-red-400 hover:text-red-300">✕</button>
        </div>`);
    missionIndex++;
}

function addValeurRow() {
    const c = document.getElementById('valeurs-container');
    c.insertAdjacentHTML('beforeend', `
        <div class="valeur-row flex flex-col sm:flex-row gap-3 p-3 rounded-xl bg-slate-950 border border-slate-800">
            <input type="text" name="site_valeurs[${valeurIndex}][icone]" placeholder="Icône" class="edc-input w-24">
            <input type="text" name="site_valeurs[${valeurIndex}][titre]" placeholder="Titre" class="edc-input flex-1">
            <input type="text" name="site_valeurs[${valeurIndex}][description]" placeholder="Description" class="edc-input flex-[2]">
            <button type="button" onclick="this.closest('.valeur-row').remove()" class="px-2 py-1 text-xs text-red-400 hover:text-red-300">✕</button>
        </div>`);
    valeurIndex++;
}

// Mise à jour showTab
const origShowTab = showTab;
showTab = function(key) {
    origShowTab(key);
    document.getElementById('panel-mission').style.display = (key === 'mission') ? 'block' : 'none';
};

// Détection des modifications dans le formulaire pour la Sticky bar d'alerte
const hintBar = document.getElementById('saveHint');
document.getElementById('settingsForm')?.addEventListener('input', () => {
    if (!formDirty) { formDirty = true; hintBar?.classList.remove('opacity-0'); }
});
document.getElementById('settingsForm')?.addEventListener('change', () => {
    if (!formDirty) { formDirty = true; hintBar?.classList.remove('opacity-0'); }
});
document.getElementById('settingsForm')?.addEventListener('submit', () => {
    formDirty = false;
    hintBar?.classList.add('opacity-0');
});
</script>
@endsection
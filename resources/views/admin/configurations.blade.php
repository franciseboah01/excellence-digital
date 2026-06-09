@extends('layouts.admin')
@section('title', 'Configurations')
@section('page_title', '⚙️ Configurations du système')
@section('page_subtitle', 'Paramètres des uploads et de la sécurité')

@section('content')
<div class="max-w-2xl mt-6">
    <div class="edc-card p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.configurations.update') }}" class="space-y-5">
            @csrf @method('PUT')

            <h3 class="text-lg font-bold" style="color: var(--edc-text-primary);">📁 Paramètres d'upload</h3>

            <div>
                <label class="edc-label">Taille maximale des fichiers (MB)</label>
                <input type="number" name="upload_taille_max_mb" min="1" max="100"
                    value="{{ \App\Models\Configuration::get('upload_taille_max_mb', 20) }}"
                    class="edc-input">
                <p class="text-xs mt-1" style="color: var(--edc-text-muted);">
                    Actuellement : {{ \App\Models\Configuration::get('upload_taille_max_mb', 20) }} MB
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
                    value="{{ \App\Models\Configuration::get('upload_image_taille_max_mb', 2) }}"
                    class="edc-input">
            </div>

            <hr style="border-color: var(--edc-border);">

            <h3 class="text-lg font-bold" style="color: var(--edc-text-primary);">🔐 Sécurité des URLs</h3>

            <div>
                <label class="edc-label">Durée de validité des liens temporaires (minutes)</label>
                <input type="number" name="url_signee_expiration_minutes" min="5" max="1440"
                    value="{{ \App\Models\Configuration::get('url_signee_expiration_minutes', 30) }}"
                    class="edc-input">
                <p class="text-xs mt-1" style="color: var(--edc-text-muted);">
                    Actuellement : {{ \App\Models\Configuration::get('url_signee_expiration_minutes', 30) }} minutes.
                    Les URLs signées expirent après ce délai.
                </p>
            </div>

            {{-- Info sécurité --}}
            <div class="rounded-xl p-4 mb-6" style="background-color: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.20);">
                <p class="text-sm font-semibold mb-2" style="color: var(--edc-primary-light);">🔒 Comment ça fonctionne ?</p>
                <ul class="text-xs space-y-1" style="color: var(--edc-text-secondary);">
                    <li>• Les fichiers sont stockés dans un dossier privé (non accessible publiquement)</li>
                    <li>• Chaque accès génère un lien temporaire unique et signé</li>
                    <li>• Le lien expire automatiquement après la durée configurée</li>
                    <li>• Seuls les clients inscrits et validés peuvent accéder aux ressources</li>
                </ul>
            </div>

            <button type="submit" class="btn-primary w-full">
                💾 Sauvegarder les configurations
            </button>
        </form>
    </div>
</div>
@endsection
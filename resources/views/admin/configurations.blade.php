@extends('layouts.admin')
@section('title', 'Configurations')
@section('page_title', 'Configurations du système')
@section('page_subtitle', 'Paramètres des uploads et de la sécurité')

@section('content')
<div class="max-w-2xl mt-6">
    <div class="bg-white rounded-xl shadow p-8">
        <form method="POST" action="{{ route('admin.configurations.update') }}">
            @csrf @method('PUT')

            <h3 class="text-lg font-bold text-blue-900 mb-5">📁 Paramètres d'upload</h3>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Taille maximale des fichiers (MB)
                </label>
                <input type="number" name="upload_taille_max_mb" min="1" max="100"
                    value="{{ \App\Models\Configuration::get('upload_taille_max_mb', 20) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400 mt-1">Actuellement : {{ \App\Models\Configuration::get('upload_taille_max_mb', 20) }} MB</p>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Types de fichiers autorisés (séparés par des virgules)
                </label>
                <input type="text" name="upload_types_autorises"
                    value="{{ \App\Models\Configuration::get('upload_types_autorises', 'pdf,doc,docx,epub') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="pdf,doc,docx,epub,ppt,pptx">
                <p class="text-xs text-gray-400 mt-1">Ex : pdf,doc,docx,epub,ppt,pptx,xls,xlsx</p>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Taille maximale des images (MB)
                </label>
                <input type="number" name="upload_image_taille_max_mb" min="1" max="10"
                    value="{{ \App\Models\Configuration::get('upload_image_taille_max_mb', 2) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <hr class="my-6">

            <h3 class="text-lg font-bold text-blue-900 mb-5">🔐 Sécurité des URLs</h3>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Durée de validité des liens temporaires (minutes)
                </label>
                <input type="number" name="url_signee_expiration_minutes" min="5" max="1440"
                    value="{{ \App\Models\Configuration::get('url_signee_expiration_minutes', 30) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400 mt-1">
                    Actuellement : {{ \App\Models\Configuration::get('url_signee_expiration_minutes', 30) }} minutes.
                    Les URLs signées expirent après ce délai.
                </p>
            </div>

            {{-- Info sécurité --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <p class="text-sm font-semibold text-blue-800 mb-2">🔒 Comment ça fonctionne ?</p>
                <ul class="text-xs text-blue-700 space-y-1">
                    <li>• Les fichiers sont stockés dans un dossier privé (non accessible publiquement)</li>
                    <li>• Chaque accès génère un lien temporaire unique et signé</li>
                    <li>• Le lien expire automatiquement après la durée configurée</li>
                    <li>• Seuls les clients inscrits et validés peuvent accéder aux ressources</li>
                </ul>
            </div>

            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition">
                💾 Sauvegarder les configurations
            </button>
        </form>
    </div>
</div>
@endsection
<?php

namespace App\Http\Controllers;

use App\Models\Ressource;
use App\Services\FichierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FichierController extends Controller
{
    /**
     * Afficher/télécharger un fichier via URL signée
     */
    public function afficher(Request $request, Ressource $ressource)
    {
        // Le middleware VerifierAccesFichier a déjà validé l'accès

        if (!$ressource->fichier_path) {
            abort(404, 'Aucun fichier associé à cette ressource.');
        }

        if (!FichierService::existe($ressource->fichier_path)) {
            abort(404, 'Le fichier est introuvable sur le serveur.');
        }

        $extension = pathinfo($ressource->fichier_path, PATHINFO_EXTENSION);
        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'epub' => 'application/epub+zip',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];

        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

        // PDF → affichage inline, autres → téléchargement
        $disposition = $extension === 'pdf' ? 'inline' : 'attachment';

        return response()->file(
            Storage::disk('local')->path($ressource->fichier_path),
            [
                'Content-Type'        => $mimeType,
                'Content-Disposition' => "{$disposition}; filename=\"{$ressource->titre}.{$extension}\"",
                'X-Frame-Options'     => 'SAMEORIGIN',
                'Cache-Control'       => 'private, no-store',
            ]
        );
    }

    /**
     * Générer et retourner une URL signée (AJAX)
     */
    public function urlSignee(Ressource $ressource)
    {
        $user = auth()->user();

        // Vérifier accès
        if (!$user->hasRole(['admin', 'enseignant'])) {
            $inscrit = \App\Models\InscriptionFormation::where('user_id', $user->id)
                ->where('formation_id', $ressource->formation_id)
                ->where('statut', 'valide')
                ->exists();

            if (!$inscrit) {
                return response()->json(['error' => 'Accès refusé.'], 403);
            }
        }

        $url = FichierService::genererUrlSignee($ressource->fichier_path, $ressource->id);

        return response()->json([
            'url'        => $url,
            'expiration' => now()->addMinutes(
                (int) \App\Models\Configuration::get('url_signee_expiration_minutes', 30)
            )->format('H:i'),
        ]);
    }
}
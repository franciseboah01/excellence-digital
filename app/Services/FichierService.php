<?php

namespace App\Services;

use App\Models\Configuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FichierService
{
    /**
     * Valider et uploader un fichier de ressource
     */
    public static function uploaderRessource(
        UploadedFile $fichier,
        int $formationId
    ): string {
        // Récupérer la config
        $tailleMaxMb    = (int) Configuration::get('upload_taille_max_mb', 20);
        $typesAutorises = explode(',', Configuration::get('upload_types_autorises', 'pdf,doc,docx,epub'));

        // Valider la taille
        $tailleMb = $fichier->getSize() / 1024 / 1024;
        if ($tailleMb > $tailleMaxMb) {
            throw new InvalidArgumentException(
                "Le fichier dépasse la taille maximale autorisée ({$tailleMaxMb} MB)."
            );
        }

        // Valider l'extension
        $extension = strtolower($fichier->getClientOriginalExtension());
        if (!in_array($extension, $typesAutorises)) {
            throw new InvalidArgumentException(
                "Type de fichier non autorisé. Extensions acceptées : " . implode(', ', $typesAutorises)
            );
        }

        // Valider le MIME type réel (double vérification)
        $mimesAutorises = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'epub' => 'application/epub+zip',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        $mimeReel = $fichier->getMimeType();
        if (isset($mimesAutorises[$extension]) && $mimeReel !== $mimesAutorises[$extension]) {
            throw new InvalidArgumentException(
                "Le contenu du fichier ne correspond pas à son extension."
            );
        }

        // Générer un nom sécurisé
        $nomFichier = Str::uuid() . '.' . $extension;
        $chemin     = "ressources/formation_{$formationId}/{$nomFichier}";

        // Stocker dans le disque local privé
        Storage::disk('local')->putFileAs(
            "ressources/formation_{$formationId}",
            $fichier,
            $nomFichier
        );

        return $chemin;
    }

    /**
     * Générer une URL signée temporaire pour accéder au fichier
     */
    public static function genererUrlSignee(string $cheminFichier, int $ressourceId): string
    {
        $expirationMinutes = (int) Configuration::get('url_signee_expiration_minutes', 30);

        return URL::temporarySignedRoute(
            'ressources.fichier',
            now()->addMinutes($expirationMinutes),
            ['ressource' => $ressourceId]
        );
    }

    /**
     * Supprimer un fichier du stockage
     */
    public static function supprimer(string $cheminFichier): bool
    {
        if (Storage::disk('local')->exists($cheminFichier)) {
            return Storage::disk('local')->delete($cheminFichier);
        }
        return false;
    }

    /**
     * Vérifier qu'un fichier existe
     */
    public static function existe(string $cheminFichier): bool
    {
        return Storage::disk('local')->exists($cheminFichier);
    }

    /**
     * Obtenir les infos d'un fichier
     */
    public static function infos(string $cheminFichier): array
    {
        if (!static::existe($cheminFichier)) {
            return [];
        }

        return [
            'taille'     => Storage::disk('local')->size($cheminFichier),
            'taille_mb'  => round(Storage::disk('local')->size($cheminFichier) / 1024 / 1024, 2),
            'modifie_le' => Storage::disk('local')->lastModified($cheminFichier),
            'extension'  => pathinfo($cheminFichier, PATHINFO_EXTENSION),
        ];
    }

    /**
     * Lister les types de fichiers autorisés (pour les formulaires)
     */
    public static function typesAutorises(): array
    {
        $types = Configuration::get('upload_types_autorises', 'pdf,doc,docx,epub');
        return array_map('trim', explode(',', $types));
    }

    /**
     * Taille max en MB
     */
    public static function tailleMaxMb(): int
    {
        return (int) Configuration::get('upload_taille_max_mb', 20);
    }
}
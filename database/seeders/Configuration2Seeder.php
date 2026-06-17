<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class Configuration2Seeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            // Onglet : Informations Institutionnelles (Nouveau)
            ['cle' => 'site_nom', 'valeur' => 'EXCELLENCE DIGITAL CENTER', 'description' => 'Nom officiel de l\'établissement d\'enseignement'],
            ['cle' => 'site_slogan', 'valeur' => 'Système de vérification et d\'authentification cryptographique des compétences', 'description' => 'Slogan ou sous-titre de l\'en-tête de validation'],
            ['cle' => 'site_adresse', 'valeur' => 'Abidjan, Côte d\'Ivoire', 'description' => 'Adresse physique de l\'établissement'],
            ['cle' => 'site_contact', 'valeur' => '+225 00 00 00 00 00', 'description' => 'Numéro de téléphone officiel de support'],
            ['cle' => 'site_email', 'valeur' => 'contact@edcformation.com', 'description' => 'Adresse email de contact principale'],
            ['cle' => 'site_web', 'valeur' => 'www.edcformation.com', 'description' => 'URL du site internet de la plateforme'],

            // Onglet : Stockage & Fichiers
            ['cle' => 'upload_taille_max_mb', 'valeur' => '20', 'description' => 'Taille maximale des fichiers en Mo'],
            ['cle' => 'upload_types_autorises', 'valeur' => 'pdf,doc,docx,epub', 'description' => 'Extensions de fichiers autorisées'],
            ['cle' => 'upload_image_taille_max_mb', 'valeur' => '2', 'description' => 'Taille maximale des images en Mo'],

            // Onglet : Sécurité
            ['cle' => 'url_signee_expiration_minutes', 'valeur' => '30', 'description' => 'Expiration des liens de fichiers temporaires'],
            ['cle' => 'qcm_note_minimale', 'valeur' => '14', 'description' => 'Note minimale de réussite sur 20'],

            // Onglet : Maquette Certificat
            ['cle' => 'certificat_axis_x_numero', 'valeur' => '240', 'description' => 'Position X du numéro de certificat'],
            ['cle' => 'certificat_axis_y_numero', 'valeur' => '20', 'description' => 'Position Y du numéro de certificat'],
            ['cle' => 'certificat_font_size_numero', 'valeur' => '12', 'description' => 'Taille de police du numéro'],
            ['cle' => 'certificat_axis_x_name', 'valeur' => '148', 'description' => 'Position X du nom de l\'apprenant'],
            ['cle' => 'certificat_axis_y_name', 'valeur' => '105', 'description' => 'Position Y du nom de l\'apprenant'],
            ['cle' => 'certificat_font_size_name', 'valeur' => '28', 'description' => 'Taille de police du nom de l\'apprenant'],
            ['cle' => 'certificat_axis_x_formation', 'valeur' => '148', 'description' => 'Position X du titre de formation'],
            ['cle' => 'certificat_axis_y_formation', 'valeur' => '135', 'description' => 'Position Y du titre de formation'],
            ['cle' => 'certificat_font_size_formation', 'valeur' => '20', 'description' => 'Taille de police de l\'intitulé formation'],
            ['cle' => 'certificat_axis_x_performance', 'valeur' => '148', 'description' => 'Position X des notes et mentions'],
            ['cle' => 'certificat_axis_y_performance', 'valeur' => '155', 'description' => 'Position Y des notes et mentions'],
            ['cle' => 'certificat_font_size_perf', 'valeur' => '12', 'description' => 'Taille de police des notes et mentions'],
            ['cle' => 'certificat_axis_x_metadata', 'valeur' => '40', 'description' => 'Position X du lieu et de la date'],
            ['cle' => 'certificat_axis_y_metadata', 'valeur' => '185', 'description' => 'Position Y du lieu et de la date'],
            ['cle' => 'certificat_qr_size', 'valeur' => '70', 'description' => 'Taille en pixels du QR Code de sécurité'],


            // QR code
            ['cle' => 'certificat_show_qrcode', 'valeur' => '1', 'description' => 'Afficher le QR Code sur le document (1 = Oui, 0 = Non)'],
            ['cle' => 'certificat_qr_size', 'valeur' => '120', 'description' => 'Taille du carré du QR Code en pixels'],
            ['cle' => 'certificat_axis_x_metadata', 'valeur' => '50', 'description' => 'Axe X (horizontal) pour le placement du QR Code'],
            ['cle' => 'certificat_axis_y_metadata', 'valeur' => '450', 'description' => 'Axe Y (vertical) pour le placement du QR Code'],
        ];

        foreach ($configs as $config) {
            Configuration::updateOrCreate(['cle' => $config['cle']], $config);
        }
    }
}
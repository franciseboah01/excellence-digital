<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            [
                'cle'         => 'upload_taille_max_mb',
                'valeur'      => '20',
                'description' => 'Taille maximale des fichiers uploadés en MB',
            ],
            [
                'cle'         => 'upload_types_autorises',
                'valeur'      => 'pdf,doc,docx,epub,ppt,pptx,xls,xlsx',
                'description' => 'Extensions de fichiers autorisées pour upload',
            ],
            [
                'cle'         => 'url_signee_expiration_minutes',
                'valeur'      => '30',
                'description' => 'Durée de validité des URLs signées en minutes',
            ],
            [
                'cle'         => 'upload_image_taille_max_mb',
                'valeur'      => '2',
                'description' => 'Taille maximale des images en MB',
            ],
        ];

        foreach ($configs as $config) {
            Configuration::firstOrCreate(
                ['cle' => $config['cle']],
                $config
            );
        }

        $this->command->info('✅ Configurations créées !');
    }
}
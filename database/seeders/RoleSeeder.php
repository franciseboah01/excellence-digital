<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les rôles
        $roles = ['admin', 'client', 'enseignant', 'visiteur'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Créer le compte Admin par défaut
        $admin = User::firstOrCreate(
            ['email' => 'admin@excellencedigital.ci'],
            [
                'nom'      => 'ADMIN',
                'prenom'   => 'Excellence',
                'email'    => 'admin@excellencedigital.ci',
                'password' => Hash::make('Admin@2024!'),
                'statut'   => 'actif',
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('admin');

        $this->command->info('✅ Rôles créés et compte admin configuré !');
        $this->command->info('📧 Email : admin@excellencedigital.ci');
        $this->command->info('🔑 Mot de passe : Admin@2024!');
    }
}
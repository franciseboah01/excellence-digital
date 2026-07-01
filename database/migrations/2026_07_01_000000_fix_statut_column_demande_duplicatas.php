<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * ⚠️ Correctif : la colonne `statut` était un ENUM ne contenant pas la
     * valeur 'paye', ce qui provoquait une troncature silencieuse par MySQL
     * (SQLSTATE[01000] Warning: 1265 Data truncated for column 'statut').
     *
     * On la convertit en VARCHAR(20) : plus flexible, évite ce problème pour
     * tout futur statut, et la validation applicative reste de toute façon
     * gérée côté modèle (DemandeDuplicata) plutôt que côté base de données.
     */
    public function up(): void
    {
        // Sécurité : si d'anciennes lignes ont une valeur tronquée/invalide
        // suite au bug (ex: chaîne vide), on les remet à 'en_attente' avant
        // la modification de colonne pour éviter tout blocage.
        DB::table('demande_duplicatas')
            ->whereNotIn('statut', ['en_attente', 'paye', 'valide', 'rejete'])
            ->update(['statut' => 'en_attente']);

        Schema::table('demande_duplicatas', function (Blueprint $table) {
            $table->string('statut', 20)->default('en_attente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demande_duplicatas', function (Blueprint $table) {
            $table->enum('statut', ['en_attente', 'paye', 'valide', 'rejete'])
                ->default('en_attente')
                ->change();
        });
    }
};
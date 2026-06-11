<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            // Ajoute la colonne certificat_id (nullable car les paiements de formations/services n'en ont pas)
            $table->foreignId('certificat_id')
                  ->nullable()
                  ->constrained('certificats')
                  ->onDelete('set null'); // Si le certificat est supprimé, le paiement reste mais passe à null
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['certificat_id']);
            $table->dropColumn('certificat_id');
        });
    }
};
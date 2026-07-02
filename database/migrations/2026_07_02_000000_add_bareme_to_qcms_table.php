<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * ⚠️ Correctif : la colonne `bareme` manquait dans la table `qcms`.
     * Le modèle Qcm l'a dans son $fillable, le formulaire enseignant
     * (create.blade.php) la propose (20/50/100), et QcmController::store()
     * tente de l'enregistrer — sans cette colonne, TOUTE création de QCM
     * échoue avec une erreur SQL "Unknown column 'bareme'".
     */
    public function up(): void
    {
        Schema::table('qcms', function (Blueprint $table) {
            $table->integer('bareme')->default(20)->after('duree_par_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qcms', function (Blueprint $table) {
            $table->dropColumn('bareme');
        });
    }
};
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
     * ⚠️ Correctifs critiques sur la table `paiements` :
     *
     * 1. `mode_paiement` était un ENUM limité à
     *    ['especes', 'mobile_money', 'virement', 'autre'], alors que le
     *    formulaire de paiement client envoie 'orange_money', 'mtn_money',
     *    'moov_money', 'visa', 'mastercard' — AUCUNE de ces valeurs n'était
     *    acceptée. Résultat : chaque paiement effectué par un client (
     *    formation, service, duplicata) provoquait une erreur MySQL
     *    "Data truncated for column 'mode_paiement'", exactement comme le
     *    bug déjà rencontré sur `statut` de `demande_duplicatas`.
     *    On élargit la colonne en VARCHAR pour accepter toutes les valeurs
     *    actuelles et futures sans avoir à refaire une migration à chaque
     *    nouveau moyen de paiement ajouté.
     *
     * 2. `certificat_id` n'existait pas du tout, alors que tout le flux de
     *    paiement des duplicatas de certificat en dépend
     *    (Paiement::create(['certificat_id' => ...])).
     *
     * 3. `type` n'existait pas non plus, alors qu'il est utilisé partout
     *    pour distinguer formation / service / duplicata
     *    (Paiement::create(['type' => ...])).
     *
     * Sans ce correctif, la création de N'IMPORTE QUEL paiement échoue.
     */
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            if (!Schema::hasColumn('paiements', 'certificat_id')) {
                $table->foreignId('certificat_id')->nullable()->after('demande_id')
                    ->constrained('certificats')->nullOnDelete();
            }

            if (!Schema::hasColumn('paiements', 'type')) {
                $table->string('type', 20)->nullable()->after('certificat_id');
            }
        });

        // Élargir mode_paiement : ENUM -> VARCHAR
        Schema::table('paiements', function (Blueprint $table) {
            $table->string('mode_paiement', 30)->default('especes')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            if (Schema::hasColumn('paiements', 'certificat_id')) {
                $table->dropForeign(['certificat_id']);
                $table->dropColumn('certificat_id');
            }
            if (Schema::hasColumn('paiements', 'type')) {
                $table->dropColumn('type');
            }
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->enum('mode_paiement', ['especes', 'mobile_money', 'virement', 'autre'])
                ->default('especes')->change();
        });
    }
};
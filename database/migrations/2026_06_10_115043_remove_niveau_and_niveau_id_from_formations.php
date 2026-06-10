<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            // Supprimer la clé étrangère si elle existe (peu importe le nom)
            $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'formations' AND COLUMN_NAME = 'niveau_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
            foreach ($foreignKeys as $fk) {
                DB::statement("ALTER TABLE formations DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            }

            if (Schema::hasColumn('formations', 'niveau_id')) {
                $table->dropColumn('niveau_id');
            }
            if (Schema::hasColumn('formations', 'niveau')) {
                $table->dropColumn('niveau');
            }
        });
    }

    public function down(): void
    {
        //
    }
};
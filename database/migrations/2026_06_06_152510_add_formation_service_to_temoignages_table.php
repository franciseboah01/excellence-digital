<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('temoignages', function (Blueprint $table) {
            $table->foreignId('formation_id')->nullable()->after('user_id')
                  ->constrained()->nullOnDelete();
            $table->foreignId('service_id')->nullable()->after('formation_id')
                  ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('temoignages', function (Blueprint $table) {
            $table->dropForeign(['formation_id']);
            $table->dropForeign(['service_id']);
            $table->dropColumn(['formation_id', 'service_id']);
        });
    }
};
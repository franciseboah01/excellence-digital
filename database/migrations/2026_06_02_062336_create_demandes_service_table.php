<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('demandes_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('nom_visiteur')->nullable();
            $table->string('email_visiteur')->nullable();
            $table->string('telephone_visiteur')->nullable();
            $table->text('message')->nullable();
            $table->string('fichiers')->nullable();
            $table->enum('statut', ['en_attente', 'en_cours', 'termine', 'annule'])
                ->default('en_attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes_service');
    }
};

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
        Schema::create('mails_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediteur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('destinataire_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email_destinataire')->nullable();
            $table->string('sujet');
            $table->longText('contenu');
            $table->enum('statut', ['envoye', 'echoue', 'en_attente'])->default('en_attente');
            $table->timestamp('date_envoi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails_log');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('formation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('demande_id')->nullable()->constrained('demandes_service')->nullOnDelete();
            $table->decimal('montant_total', 10, 2);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->enum('statut', ['en_attente', 'partiel', 'complete'])->default('en_attente');
            $table->enum('mode_paiement', ['especes', 'mobile_money', 'virement', 'autre'])->default('especes');
            $table->string('reference')->unique();
            $table->text('notes')->nullable();
            $table->foreignId('enregistre_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_paiement')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
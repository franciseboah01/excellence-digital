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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auteur_id')->constrained('users')->cascadeOnDelete();
            $table->string('titre');
            $table->string('slug')->unique();
            $table->text('extrait')->nullable();
            $table->longText('contenu');
            $table->string('image')->nullable();
            $table->string('categorie')->default('actualite');
            $table->enum('statut', ['publie', 'brouillon'])->default('brouillon');
            $table->timestamp('publie_le')->nullable();
            $table->integer('vues')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
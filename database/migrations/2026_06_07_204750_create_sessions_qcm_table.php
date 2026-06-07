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
        Schema::create('sessions_qcm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qcm_id')->constrained('qcms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('reponses_donnees')->nullable(); // {question_id: [reponse_ids]}
            $table->integer('score')->default(0);
            $table->integer('score_max')->default(0);
            $table->decimal('note', 5, 2)->default(0); // /20
            $table->boolean('reussi')->default(false);
            $table->integer('tentative')->default(1);
            $table->timestamp('debut_le')->nullable();
            $table->timestamp('fin_le')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions_qcm');
    }
};

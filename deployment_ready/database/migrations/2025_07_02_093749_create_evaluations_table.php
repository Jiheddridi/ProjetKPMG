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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('nom_entreprise'); // Nom de l'entreprise
            $table->string('taille_entreprise'); // Taille de l'entreprise
            $table->string('contraintes')->nullable(); // Contraintes
            $table->json('df_data'); // Données des 10 DF
            $table->json('canvas_data')->nullable(); // Canvas généré
            $table->boolean('completed')->default(false); // Évaluation terminée
            $table->integer('current_df')->default(1); // DF actuel (1-10)
            $table->string('user_name')->nullable(); // Nom utilisateur
            $table->decimal('score_global', 5, 2)->default(0); // Score global
            $table->timestamps();

            // Index pour les recherches
            $table->index(['completed', 'created_at']);
            $table->index(['current_df']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};

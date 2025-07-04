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
            $table->string('name')->nullable(); // Nom de l'évaluation
            $table->text('description')->nullable(); // Description
            $table->json('evaluation_data'); // Données d'évaluation (DF1-DF10)
            $table->json('results')->nullable(); // Résultats calculés
            $table->integer('current_step')->default(1); // Étape actuelle
            $table->boolean('is_completed')->default(false); // Évaluation terminée
            $table->string('status')->default('draft'); // draft, in_progress, completed
            $table->timestamp('completed_at')->nullable(); // Date de completion
            $table->string('created_by')->nullable(); // Créé par (nom/email)
            $table->json('metadata')->nullable(); // Métadonnées additionnelles
            $table->timestamps();

            // Index pour les recherches
            $table->index(['status', 'created_at']);
            $table->index(['is_completed', 'completed_at']);
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

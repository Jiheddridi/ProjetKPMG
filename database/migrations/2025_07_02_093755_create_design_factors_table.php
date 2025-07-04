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
        Schema::create('design_factors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // DF1, DF2, etc.
            $table->string('title'); // Titre du Design Factor
            $table->text('description')->nullable(); // Description détaillée
            $table->json('labels'); // Labels des critères d'évaluation
            $table->json('defaults'); // Valeurs par défaut
            $table->json('matrix')->nullable(); // Matrice de calcul COBIT
            $table->json('weights')->nullable(); // Poids pour les calculs
            $table->boolean('is_active')->default(true); // Actif/Inactif
            $table->integer('order')->default(0); // Ordre d'affichage
            $table->json('metadata')->nullable(); // Métadonnées additionnelles
            $table->timestamps();

            // Index pour les recherches
            $table->index(['is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_factors');
    }
};

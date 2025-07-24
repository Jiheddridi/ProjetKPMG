<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('canvas_historiques', function (Blueprint $table) {
            $table->id();
            $table->string('company_name'); // Nom de l'entreprise
            $table->string('company_size'); // Taille de l'entreprise
            $table->string('user_name')->nullable(); // Nom de l'utilisateur
            $table->string('user_role')->nullable(); // Rôle de l'utilisateur
            $table->json('evaluation_data'); // Données des 10 DF
            $table->json('canvas_results'); // Résultats calculés pour le canvas
            $table->json('domain_averages'); // Moyennes par domaine
            $table->decimal('score_global', 5, 2)->default(0); // Score global
            $table->integer('completed_dfs')->default(10); // Nombre de DF complétés
            $table->enum('status', ['En cours', 'Terminée'])->default('Terminée');
            $table->timestamp('evaluation_started_at')->nullable();
            $table->timestamp('evaluation_completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('canvas_historiques');
    }
};

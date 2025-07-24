<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nom_entreprise',
        'taille_entreprise',
        'contraintes',
        'df_data',
        'canvas_data',
        'completed',
        'current_df',
        'user_name',
        'score_global'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'df_data' => 'array',
        'canvas_data' => 'array',
        'completed' => 'boolean',
        'score_global' => 'decimal:2'
    ];

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    public function scopeInProgress($query)
    {
        return $query->where('completed', false);
    }

    /**
     * Vérifier si un DF spécifique est complété
     */
    public function isDFCompleted($dfNumber)
    {
        $dfData = $this->df_data ?? [];
        return isset($dfData["DF{$dfNumber}"]) &&
               isset($dfData["DF{$dfNumber}"]['completed']) &&
               $dfData["DF{$dfNumber}"]['completed'] === true;
    }

    /**
     * Obtenir le nombre de DF complétés
     */
    public function getCompletedDFsCount()
    {
        $count = 0;
        for ($i = 1; $i <= 10; $i++) {
            if ($this->isDFCompleted($i)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Obtenir le pourcentage de progression
     */
    public function getProgressPercentage()
    {
        return ($this->getCompletedDFsCount() / 10) * 100;
    }

    /**
     * Calculer le score global automatiquement
     */
    public function calculateGlobalScore()
    {
        if (!$this->df_data || !is_array($this->df_data)) {
            return 3.0; // Score par défaut
        }

        $totalScore = 0;
        $dfCount = 0;

        foreach ($this->df_data as $dfKey => $dfData) {
            if (isset($dfData['inputs']) && is_array($dfData['inputs'])) {
                $dfScore = array_sum($dfData['inputs']) / count($dfData['inputs']);
                $totalScore += $dfScore;
                $dfCount++;
            }
        }

        return $dfCount > 0 ? round($totalScore / $dfCount, 1) : 3.0;
    }

    /**
     * Obtenir le score global (calculé ou sauvegardé)
     */
    public function getScoreGlobalAttribute($value)
    {
        // Si le score est sauvegardé, l'utiliser
        if ($value && $value > 0) {
            return $value;
        }

        // Sinon, le calculer automatiquement
        return $this->calculateGlobalScore();
    }

    /**
     * Vérifier si tous les DF sont complétés
     */
    public function areAllDFsCompleted()
    {
        return $this->getCompletedDFsCount() === 10;
    }

    /**
     * Obtenir les données d'un DF spécifique
     */
    public function getDFData($dfNumber)
    {
        $dfData = $this->df_data ?? [];
        return $dfData["DF{$dfNumber}"] ?? null;
    }

    /**
     * Sauvegarder les données d'un DF
     */
    public function saveDFData($dfNumber, $inputs)
    {
        $dfData = $this->df_data ?? [];
        $dfData["DF{$dfNumber}"] = [
            'inputs' => $inputs,
            'completed' => count(array_filter($inputs)) > 0,
            'updated_at' => now()->toISOString()
        ];

        $this->update(['df_data' => $dfData]);

        // Mettre à jour current_df si nécessaire
        if ($dfNumber >= $this->current_df) {
            $this->update(['current_df' => min($dfNumber + 1, 10)]);
        }
    }
}

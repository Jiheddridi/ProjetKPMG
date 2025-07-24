<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanvasHistorique extends Model
{
    use HasFactory;

    protected $table = 'canvas_historiques';

    protected $fillable = [
        'company_name',
        'company_size',
        'user_name',
        'user_role',
        'evaluation_data',
        'canvas_results',
        'domain_averages',
        'score_global',
        'completed_dfs',
        'status',
        'evaluation_started_at',
        'evaluation_completed_at'
    ];

    protected $casts = [
        'evaluation_data' => 'array',
        'canvas_results' => 'array',
        'domain_averages' => 'array',
        'score_global' => 'decimal:2',
        'evaluation_started_at' => 'datetime',
        'evaluation_completed_at' => 'datetime'
    ];

    /**
     * Calculer le pourcentage de progression
     */
    public function getProgressPercentageAttribute()
    {
        return ($this->completed_dfs / 10) * 100;
    }

    /**
     * Obtenir le statut formaté
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'Terminée' ? 'success' : 'warning';
    }

    /**
     * Obtenir la durée de l'évaluation
     */
    public function getEvaluationDurationAttribute()
    {
        if (!$this->evaluation_started_at || !$this->evaluation_completed_at) {
            return null;
        }

        return $this->evaluation_started_at->diffForHumans($this->evaluation_completed_at, true);
    }

    /**
     * Scope pour les évaluations terminées
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'Terminée');
    }

    /**
     * Scope pour les évaluations en cours
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'En cours');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Evaluation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'evaluation_data',
        'results',
        'current_step',
        'is_completed',
        'status',
        'completed_at',
        'created_by',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'evaluation_data' => 'array',
        'results' => 'array',
        'metadata' => 'array',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Accessors & Mutators
     */
    protected function progressPercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => round(($this->current_step / 10) * 100, 2)
        );
    }

    /**
     * Méthodes utilitaires
     */
    public function markAsCompleted()
    {
        $this->update([
            'is_completed' => true,
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function getDesignFactorData($dfNumber)
    {
        $dfKey = "DF{$dfNumber}";
        return $this->evaluation_data[$dfKey] ?? null;
    }

    public function setDesignFactorData($dfNumber, $data)
    {
        $dfKey = "DF{$dfNumber}";
        $evaluationData = $this->evaluation_data ?? [];
        $evaluationData[$dfKey] = $data;

        $this->update(['evaluation_data' => $evaluationData]);
    }

    public function calculateCompletionStatus()
    {
        $evaluationData = $this->evaluation_data ?? [];
        $completedSteps = 0;

        for ($i = 1; $i <= 10; $i++) {
            if (isset($evaluationData["DF{$i}"])) {
                $completedSteps++;
            }
        }

        return [
            'completed_steps' => $completedSteps,
            'total_steps' => 10,
            'percentage' => round(($completedSteps / 10) * 100, 2)
        ];
    }
}

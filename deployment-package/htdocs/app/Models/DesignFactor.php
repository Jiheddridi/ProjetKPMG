<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignFactor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'title',
        'description',
        'labels',
        'defaults',
        'matrix',
        'weights',
        'is_active',
        'order',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'labels' => 'array',
        'defaults' => 'array',
        'matrix' => 'array',
        'weights' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Méthodes utilitaires
     */
    public static function getByCode($code)
    {
        return static::where('code', $code)->first();
    }

    public static function getAllActive()
    {
        return static::active()->ordered()->get();
    }

    public function getNumberFromCode()
    {
        return (int) str_replace('DF', '', $this->code);
    }

    public function getParametersAttribute()
    {
        $parameters = [];
        $labels = $this->labels ?? ['Paramètre 1', 'Paramètre 2', 'Paramètre 3'];
        $defaults = $this->defaults ?? [0, 0, 0];

        foreach ($labels as $index => $label) {
            $parameters[] = [
                'label' => $label,
                'min' => 0,
                'max' => 5,
                'default' => $defaults[$index] ?? 0,
                'description' => "Paramètre d'évaluation pour {$this->code}"
            ];
        }

        return $parameters;
    }

    public function validateInputs($inputs)
    {
        if (!is_array($inputs)) {
            return false;
        }

        $expectedCount = count($this->labels);
        if (count($inputs) !== $expectedCount) {
            return false;
        }

        foreach ($inputs as $input) {
            if (!is_numeric($input) || $input < 0 || $input > 5) {
                return false;
            }
        }

        return true;
    }

    public function calculateScores($inputs, $objectives)
    {
        if (!$this->validateInputs($inputs)) {
            throw new \InvalidArgumentException('Inputs invalides');
        }

        $matrix = $this->matrix ?? $this->generateDefaultMatrix(count($objectives));
        $scores = [];

        foreach ($matrix as $row) {
            $score = 0;
            for ($i = 0; $i < count($inputs); $i++) {
                $score += ($row[$i] ?? 0) * $inputs[$i];
            }
            $scores[] = round($score / count($inputs), 2);
        }

        return $scores;
    }

    private function generateDefaultMatrix($objectiveCount)
    {
        $inputCount = count($this->labels);
        $matrix = [];

        for ($i = 0; $i < $objectiveCount; $i++) {
            $row = [];
            for ($j = 0; $j < $inputCount; $j++) {
                // Valeurs aléatoires pour la démonstration
                $row[] = rand(0, 30) / 100; // Valeurs entre 0 et 0.3
            }
            $matrix[] = $row;
        }

        return $matrix;
    }
}

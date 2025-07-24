<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    private string $host;
    private string $model;

    public function __construct()
    {
        $this->host = env('OLLAMA_HOST', 'http://localhost:11434');
        $this->model = env('OLLAMA_MODEL', 'llama3.1:8b');
    }

    /**
     * Filtrer et prioriser les objectifs COBIT avec IA
     */
    public function filterBestObjectives(array $objectives, array $evaluationContext = []): array
    {
        try {
            // Préparer le prompt pour l'IA
            $prompt = $this->buildObjectiveFilterPrompt($objectives, $evaluationContext);
            
            // Appeler Ollama
            $response = $this->callOllama($prompt);
            
            // Parser la réponse
            $filteredObjectives = $this->parseObjectiveResponse($response, $objectives);
            
            // Valider et retourner entre 3-15 objectifs
            return $this->validateObjectiveCount($filteredObjectives);
            
        } catch (\Exception $e) {
            Log::error('Erreur Ollama filterBestObjectives: ' . $e->getMessage());
            
            // Fallback vers filtrage automatique
            return $this->fallbackFilter($objectives);
        }
    }

    /**
     * Construire le prompt pour filtrer les objectifs
     */
    private function buildObjectiveFilterPrompt(array $objectives, array $context): string
    {
        $objectivesList = collect($objectives)->map(function($obj) {
            return "- {$obj['objective']}: Score {$obj['score']}, Priorité {$obj['priority']}, Gap {$obj['gap']}";
        })->join("\n");

        $companyInfo = $context['company_name'] ?? 'Entreprise';
        $companySize = $context['company_size'] ?? 'Moyenne';
        $constraints = $context['constraints'] ?? 'Aucune contrainte spécifiée';

        return "
Tu es un expert consultant COBIT 2019. Analyse et filtre les objectifs COBIT pour identifier les 3-15 meilleurs.

CONTEXTE ENTREPRISE:
- Nom: {$companyInfo}
- Taille: {$companySize}
- Contraintes: {$constraints}

OBJECTIFS COBIT DISPONIBLES:
{$objectivesList}

CRITÈRES DE FILTRAGE:
1. Prioriser les objectifs avec scores élevés
2. Considérer les priorités (H > M > L)
3. Éviter les doublons
4. Adapter selon la taille d'entreprise
5. Tenir compte des contraintes
6. Retourner entre 3 et 15 objectifs maximum

INSTRUCTIONS:
- Analyse chaque objectif selon son impact business
- Considère les dépendances entre objectifs COBIT
- Priorise selon le contexte entreprise
- Retourne une liste JSON des codes objectifs sélectionnés

FORMAT DE RÉPONSE (JSON uniquement):
{
  \"selected_objectives\": [\"EDM01\", \"APO02\", \"BAI03\"],
  \"reasoning\": \"Explication du choix\"
}

Réponds uniquement en JSON valide, sans texte supplémentaire.
";
    }

    /**
     * Appeler l'API Ollama
     */
    private function callOllama(string $prompt): string
    {
        $response = Http::timeout(30)->post("{$this->host}/api/generate", [
            'model' => $this->model,
            'prompt' => $prompt,
            'stream' => false,
            'options' => [
                'temperature' => 0.3, // Plus déterministe pour le filtrage
                'top_p' => 0.8,
                'max_tokens' => 1000
            ]
        ]);

        if (!$response->successful()) {
            throw new \Exception('Erreur API Ollama: ' . $response->status());
        }

        $data = $response->json();
        return $data['response'] ?? '';
    }

    /**
     * Parser la réponse de l'IA
     */
    private function parseObjectiveResponse(string $response, array $originalObjectives): array
    {
        // Nettoyer la réponse
        $cleanResponse = trim($response);
        
        // Extraire le JSON
        if (preg_match('/\{.*\}/s', $cleanResponse, $matches)) {
            $jsonStr = $matches[0];
            $parsed = json_decode($jsonStr, true);
            
            if ($parsed && isset($parsed['selected_objectives']) && is_array($parsed['selected_objectives'])) {
                // Filtrer les objectifs selon la sélection IA
                $selectedCodes = $parsed['selected_objectives'];
                $filteredObjectives = [];
                
                foreach ($originalObjectives as $objective) {
                    if (in_array($objective['objective'], $selectedCodes)) {
                        $filteredObjectives[] = $objective;
                    }
                }
                
                // Trier par score décroissant
                usort($filteredObjectives, function($a, $b) {
                    return ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
                });
                
                return $filteredObjectives;
            }
        }

        // Si parsing échoue, fallback
        return $this->fallbackFilter($originalObjectives);
    }

    /**
     * Valider le nombre d'objectifs (3-15)
     */
    private function validateObjectiveCount(array $objectives): array
    {
        $count = count($objectives);
        
        if ($count < 3) {
            // Si moins de 3, prendre les 3 premiers disponibles
            return array_slice($objectives, 0, min(3, count($objectives)));
        } elseif ($count > 15) {
            // Si plus de 15, limiter à 15
            return array_slice($objectives, 0, 15);
        }
        
        return $objectives;
    }

    /**
     * Filtrage de fallback si Ollama échoue
     */
    private function fallbackFilter(array $objectives): array
    {
        // Supprimer les doublons
        $uniqueObjectives = [];
        $seenCodes = [];
        
        foreach ($objectives as $objective) {
            $code = $objective['objective'] ?? '';
            if (!in_array($code, $seenCodes)) {
                $uniqueObjectives[] = $objective;
                $seenCodes[] = $code;
            }
        }
        
        // Trier par score décroissant
        usort($uniqueObjectives, function($a, $b) {
            return ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
        });
        
        // Retourner entre 3 et 15 objectifs
        $count = count($uniqueObjectives);
        if ($count <= 3) {
            return $uniqueObjectives;
        } elseif ($count > 15) {
            return array_slice($uniqueObjectives, 0, 15);
        } else {
            return $uniqueObjectives;
        }
    }

    /**
     * Comparer plusieurs évaluations COBIT avec IA
     */
    public function compareEvaluations(array $evaluations): array
    {
        try {
            // Préparer le prompt de comparaison
            $prompt = $this->buildComparisonPrompt($evaluations);

            // Appeler Ollama
            $response = $this->callOllama($prompt);

            // Parser la réponse
            $analysis = $this->parseComparisonResponse($response, $evaluations);

            return [
                'success' => true,
                'analysis' => $analysis,
                'recommendation' => $this->generateBestEvaluationRecommendation($evaluations, $analysis)
            ];

        } catch (\Exception $e) {
            Log::error('Erreur Ollama compareEvaluations: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'fallback' => $this->fallbackComparison($evaluations)
            ];
        }
    }

    /**
     * Construire le prompt de comparaison
     */
    private function buildComparisonPrompt(array $evaluations): string
    {
        $evaluationsList = collect($evaluations)->map(function($eval, $index) {
            $letter = chr(65 + $index); // A, B, C, etc.
            return "ÉVALUATION {$letter} - {$eval['nom_entreprise']}:
- Taille: {$eval['taille_entreprise']}
- Score Global: {$eval['score_global']}/5
- Niveau Maturité: " . round($eval['score_global']) . "
- Contraintes: {$eval['contraintes']}
- Objectifs Complétés: " . count($eval['best_objectives'] ?? []) . "
- Date: {$eval['updated_at']}";
        })->join("\n\n");

        return "
Tu es un expert consultant COBIT 2019. Compare ces évaluations d'entreprises et détermine la meilleure.

ÉVALUATIONS À COMPARER:
{$evaluationsList}

CRITÈRES D'ANALYSE:
1. **Maturité Gouvernance IT**: Niveau de maturité COBIT (1-5)
2. **Performance Globale**: Score global et cohérence
3. **Couverture Objectifs**: Nombre et qualité des objectifs COBIT
4. **Contexte Entreprise**: Taille, contraintes, secteur
5. **Potentiel d'Amélioration**: Gaps et opportunités
6. **Risques Identifiés**: Vulnérabilités et points faibles

ANALYSE DEMANDÉE:
1. **Comparaison Détaillée**: Forces/faiblesses de chaque évaluation
2. **Benchmarking**: Positionnement relatif des entreprises
3. **Impact Stratégique**: Implications business de chaque niveau
4. **Recommandations**: Actions prioritaires par entreprise
5. **Classement Final**: Quelle entreprise a la meilleure gouvernance IT

FORMAT DE RÉPONSE (JSON uniquement):
{
  \"comparative_analysis\": {
    \"summary\": \"Résumé exécutif de la comparaison\",
    \"strengths_weaknesses\": {
      \"A\": {\"strengths\": [\"...\"], \"weaknesses\": [\"...\"]},
      \"B\": {\"strengths\": [\"...\"], \"weaknesses\": [\"...\"]}
    },
    \"benchmarking\": \"Analyse comparative des positions\",
    \"strategic_impact\": \"Impact business de chaque évaluation\"
  },
  \"ranking\": [
    {\"position\": 1, \"company\": \"Nom\", \"score\": 4.2, \"justification\": \"Pourquoi cette entreprise est la meilleure\"},
    {\"position\": 2, \"company\": \"Nom\", \"score\": 3.8, \"justification\": \"Points forts et axes d'amélioration\"}
  ],
  \"recommendations\": {
    \"best_company\": \"Nom de la meilleure entreprise\",
    \"why_best\": \"Justification détaillée du choix\",
    \"next_steps\": [\"Action 1\", \"Action 2\", \"Action 3\"],
    \"risk_mitigation\": \"Stratégies de réduction des risques\"
  }
}

Réponds uniquement en JSON valide, sans texte supplémentaire.
";
    }

    /**
     * Parser la réponse de comparaison
     */
    private function parseComparisonResponse(string $response, array $evaluations): array
    {
        // Nettoyer la réponse
        $cleanResponse = trim($response);

        // Extraire le JSON
        if (preg_match('/\{.*\}/s', $cleanResponse, $matches)) {
            $jsonStr = $matches[0];
            $parsed = json_decode($jsonStr, true);

            if ($parsed && isset($parsed['comparative_analysis'])) {
                return $parsed;
            }
        }

        // Fallback si parsing échoue
        return $this->fallbackAnalysis($evaluations);
    }

    /**
     * Générer la recommandation de la meilleure évaluation
     */
    private function generateBestEvaluationRecommendation(array $evaluations, array $analysis): array
    {
        if (isset($analysis['recommendations']['best_company'])) {
            return $analysis['recommendations'];
        }

        // Fallback : choisir celle avec le meilleur score
        $best = collect($evaluations)->sortByDesc('score_global')->first();

        return [
            'best_company' => $best['nom_entreprise'],
            'why_best' => 'Score global le plus élevé (' . $best['score_global'] . '/5)',
            'next_steps' => [
                'Maintenir le niveau de maturité actuel',
                'Identifier les axes d\'amélioration',
                'Mettre en place un plan de gouvernance'
            ],
            'risk_mitigation' => 'Surveillance continue des indicateurs COBIT'
        ];
    }

    /**
     * Analyse de fallback
     */
    private function fallbackAnalysis(array $evaluations): array
    {
        return [
            'comparative_analysis' => [
                'summary' => 'Comparaison automatique basée sur les scores COBIT',
                'benchmarking' => 'Analyse des niveaux de maturité relatifs',
                'strategic_impact' => 'Impact proportionnel aux scores de gouvernance'
            ],
            'ranking' => collect($evaluations)->sortByDesc('score_global')->values()->map(function($eval, $index) {
                return [
                    'position' => $index + 1,
                    'company' => $eval['nom_entreprise'],
                    'score' => $eval['score_global'],
                    'justification' => 'Classement basé sur le score global COBIT'
                ];
            })->toArray()
        ];
    }

    /**
     * Comparaison de fallback
     */
    private function fallbackComparison(array $evaluations): array
    {
        return [
            'analysis' => $this->fallbackAnalysis($evaluations),
            'recommendation' => $this->generateBestEvaluationRecommendation($evaluations, [])
        ];
    }

    /**
     * Tester la connexion Ollama
     */
    public function testConnection(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->host}/api/tags");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}

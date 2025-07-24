<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaCobitAnalysisService
{
    private string $host;
    private string $model;
    private bool $isAvailable;

    public function __construct()
    {
        $this->host = env('OLLAMA_HOST', 'http://localhost:11434');
        // Utiliser mistral qui suit mieux les instructions pour l'analyse de documents
        $this->model = env('OLLAMA_COBIT_MODEL', 'mistral');
        $this->isAvailable = $this->checkAvailability();
    }

    /**
     * Vérifier la disponibilité d'Ollama
     */
    private function checkAvailability(): bool
    {
        try {
            $response = Http::timeout(3)->get("{$this->host}/api/tags");
            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Ollama COBIT non disponible: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Analyser un document pour les Design Factors COBIT 2019
     */
    public function analyzeDocumentForDesignFactors(string $content, string $documentType, array $projectContext = []): array
    {
        Log::info('🚀 Début analyse Ollama pour: ' . ($projectContext['nom_entreprise'] ?? 'Entreprise'));

        if (!$this->isAvailable) {
            Log::warning('⚠️ Ollama indisponible - Utilisation du fallback');
            return $this->getFallbackAnalysis($content, $projectContext);
        }

        try {
            // Construire un prompt spécialisé pour l'analyse de document
            $prompt = $this->buildDocumentAnalysisPrompt($content, $documentType, $projectContext);

            Log::info('📝 Prompt construit - Appel Ollama...');
            $response = $this->callOllamaForDocumentAnalysis($prompt);

            if ($response) {
                Log::info('✅ Réponse Ollama reçue - Parsing...');
                $result = $this->parseOllamaResponse($response, $projectContext);

                if (!empty($result) && isset($result['df_values'])) {
                    Log::info('🎯 Analyse Ollama réussie - ' . count($result['df_values']) . ' DF générés');
                    return $result;
                } else {
                    Log::warning('⚠️ Parsing Ollama échoué ou résultat vide');
                }
            }
        } catch (\Exception $e) {
            Log::error('❌ Erreur Ollama COBIT Analysis: ' . $e->getMessage());
        }

        Log::info('🔄 Fallback vers analyse de base');
        return $this->getFallbackAnalysis($content, $projectContext);
    }

    /**
     * Construire un prompt spécialisé pour l'analyse de document
     */
    private function buildDocumentAnalysisPrompt(string $content, string $documentType, array $context): string
    {
        // Analyser le contenu complet pour une meilleure précision
        $analysisContent = strlen($content) > 2500 ? substr($content, 0, 2500) . "\n[...document tronqué...]" : $content;

        // Contexte du projet
        $companySize = $context['taille_entreprise'] ?? 'moyenne entreprise';
        $constraints = $context['contraintes'] ?? 'aucune contrainte spécifiée';
        $companyName = $context['nom_entreprise'] ?? 'l\'entreprise';

        // Analyser le secteur depuis le contenu
        $sector = $this->detectSector($content);

        return "EXPERT COBIT 2019 - ANALYSE DIFFÉRENTIELLE OBLIGATOIRE

Analysez ce document et donnez des scores TRÈS VARIABLES selon le profil détecté.

ENTREPRISE: {$companyName}
TAILLE: {$companySize}
SECTEUR: {$sector}
CONTRAINTES: {$constraints}

DOCUMENT:
{$analysisContent}

RÈGLES STRICTES:
1. IDENTIFIEZ le type d'entreprise (startup/banque/industrie/PME)
2. ADAPTEZ les scores selon les caractéristiques trouvées
3. UTILISEZ des scores TRÈS DIFFÉRENTS selon le profil

PROFILS ET SCORES OBLIGATOIRES:

SI STARTUP/TECH (mots: innovation, agile, croissance, startup):
- DF1: 4.2-4.8 (innovation forte)
- DF3: 2.0-3.0 (risques acceptés)
- DF6: 1.5-2.5 (conformité minimale)
- DF8: 3.8-4.5 (sourcing externe)
- DF9: 4.2-4.8 (méthodes agiles)
- Maturité: 2.5-3.2

SI BANQUE/FINANCE (mots: sécurité, conformité, réglementation):
- DF1: 3.0-3.5 (stratégie stable)
- DF3: 4.5-5.0 (risques très élevés)
- DF6: 4.5-5.0 (conformité maximale)
- DF8: 2.0-3.0 (sourcing interne)
- DF9: 2.5-3.5 (méthodes traditionnelles)
- Maturité: 4.0-4.8

SI INDUSTRIE (mots: production, qualité, efficacité):
- DF1: 3.2-3.8 (stratégie efficacité)
- DF3: 3.0-4.0 (risques modérés-élevés)
- DF6: 3.5-4.2 (conformité industrielle)
- DF8: 2.8-3.5 (sourcing mixte)
- DF9: 3.0-3.8 (méthodes structurées)
- Maturité: 3.5-4.2

SI PME/FAMILIALE (mots: familial, budget limité, simple):
- DF1: 2.0-2.8 (stratégie prudente)
- DF3: 1.8-2.5 (risques faibles)
- DF6: 2.0-3.0 (conformité basique)
- DF8: 3.5-4.2 (sourcing externe)
- DF9: 2.0-2.8 (méthodes simples)
- Maturité: 2.0-2.8

JSON OBLIGATOIRE:
{
  \"df_scores\": {
    \"DF1\": {\"score\": 0.0, \"reasoning\": \"Justification\"},
    \"DF2\": {\"score\": 0.0, \"reasoning\": \"Justification\"},
    \"DF3\": {\"score\": 0.0, \"reasoning\": \"Justification\"},
    \"DF4\": {\"score\": 0.0, \"reasoning\": \"Justification\"},
    \"DF5\": {\"score\": 0.0, \"reasoning\": \"Justification\"},
    \"DF6\": {\"score\": 0.0, \"reasoning\": \"Justification\"},
    \"DF7\": {\"score\": 0.0, \"reasoning\": \"Justification\"},
    \"DF8\": {\"score\": 0.0, \"reasoning\": \"Justification\"},
    \"DF9\": {\"score\": 0.0, \"reasoning\": \"Justification\"},
    \"DF10\": {\"score\": 0.0, \"reasoning\": \"Justification\"}
  },
  \"maturity_estimate\": 0.0
}";
    }

    /**
     * Appeler Ollama spécialement pour l'analyse de documents
     */
    private function callOllamaForDocumentAnalysis(string $prompt): ?string
    {
        try {
            Log::info('🤖 Appel Ollama Mistral pour analyse de document personnalisée');

            $startTime = microtime(true);

            $response = Http::timeout(60)->post("{$this->host}/api/generate", [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.8,  // Plus de créativité pour variabilité
                    'top_p' => 0.95,
                    'top_k' => 50,
                    'num_predict' => 1500, // Plus de tokens pour analyse complète
                    'repeat_penalty' => 1.2,
                    'seed' => time() + rand(1, 10000), // Seed très variable
                    'stop' => ['}'], // Arrêt après JSON complet
                ]
            ]);

            $duration = round((microtime(true) - $startTime), 2);
            Log::info("⏱️ Durée appel Ollama: {$duration}s");

            if ($response->successful()) {
                $data = $response->json();
                $responseText = $data['response'] ?? null;

                if ($responseText) {
                    Log::info('✅ Réponse Ollama reçue (' . strlen($responseText) . ' caractères)');
                    Log::info('📄 Début réponse: ' . substr($responseText, 0, 150) . '...');
                    return $responseText;
                } else {
                    Log::warning('⚠️ Réponse Ollama vide dans les données');
                }
            } else {
                Log::error('❌ Erreur HTTP Ollama: ' . $response->status());
                Log::error('📄 Corps erreur: ' . substr($response->body(), 0, 300));
            }
        } catch (\Exception $e) {
            Log::error('❌ Exception lors de l\'appel Ollama: ' . $e->getMessage());
            Log::error('📍 Trace: ' . $e->getTraceAsString());
        }

        return null;
    }

    /**
     * Parser la réponse Ollama pour l'analyse de document
     */
    private function parseOllamaResponse(string $response, array $context): ?array
    {
        Log::info('🔍 Parsing de la réponse Ollama pour analyse de document...');

        // Nettoyer la réponse
        $cleanResponse = trim($response);

        Log::info('📄 Réponse brute (' . strlen($cleanResponse) . ' caractères): ' . substr($cleanResponse, 0, 200) . '...');

        // Essayer de parser directement si ça commence par {
        if (str_starts_with($cleanResponse, '{')) {
            Log::info('🔍 Réponse commence par { - Parsing direct...');
            $parsed = json_decode($cleanResponse, true);

            if ($parsed && isset($parsed['df_scores'])) {
                Log::info('✅ Parsing direct réussi !');
                return $this->convertOllamaScoresToDFParameters($parsed, $context);
            } else {
                Log::warning('⚠️ Parsing direct échoué - Erreur JSON: ' . json_last_error_msg());
            }
        }

        // Chercher le JSON dans la réponse (peut être entouré de texte)
        $jsonStart = strpos($cleanResponse, '{');
        $jsonEnd = strrpos($cleanResponse, '}');

        if ($jsonStart !== false && $jsonEnd !== false) {
            $jsonStr = substr($cleanResponse, $jsonStart, $jsonEnd - $jsonStart + 1);

            Log::info('📋 JSON extrait (' . strlen($jsonStr) . ' caractères): ' . substr($jsonStr, 0, 200) . '...');

            $parsed = json_decode($jsonStr, true);

            if ($parsed && isset($parsed['df_scores'])) {
                $dfCount = count($parsed['df_scores']);
                Log::info("✅ JSON parsé avec succès - {$dfCount} Design Factors trouvés");

                // Valider les scores
                $validScores = 0;
                foreach ($parsed['df_scores'] as $df => $data) {
                    if (isset($data['score']) && is_numeric($data['score'])) {
                        $validScores++;
                    }
                }

                if ($validScores >= 3) { // Au moins 3 DF valides (plus permissif)
                    Log::info("🎯 {$validScores} scores valides trouvés - Conversion en paramètres DF");
                    return $this->convertOllamaScoresToDFParameters($parsed, $context);
                } else {
                    Log::warning("⚠️ Pas assez de scores valides: {$validScores}");
                }
            } else {
                Log::warning('⚠️ JSON invalide ou df_scores manquant');
                if ($parsed) {
                    Log::info('🔍 Clés trouvées dans JSON: ' . implode(', ', array_keys($parsed)));
                } else {
                    Log::error('❌ Erreur parsing JSON: ' . json_last_error_msg());
                }
            }
        } else {
            Log::warning('⚠️ Aucun JSON trouvé dans la réponse Ollama');
        }

        // Fallback si parsing échoue - retourner un array vide au lieu de null
        Log::info('🔄 Échec parsing - Retour array vide pour éviter l\'erreur de type');
        return [];
    }

    /**
     * Convertir les scores Ollama en paramètres DF spécifiques
     */
    private function convertOllamaScoresToDFParameters(array $ollamaResult, array $context): array
    {
        Log::info('🔄 Conversion des scores Ollama en paramètres DF...');

        $dfValues = [];
        $totalScore = 0;
        $scoreCount = 0;

        foreach ($ollamaResult['df_scores'] as $df => $data) {
            $score = floatval($data['score'] ?? 3.0);
            $reasoning = $data['reasoning'] ?? 'Analyse Ollama';

            // Convertir le score global en valeurs pour les paramètres spécifiques du DF
            $dfValues[$df] = $this->generateDFParameterValues($df, $score, 0.9, $context);

            $totalScore += $score;
            $scoreCount++;

            Log::info("📊 {$df}: Score {$score}/5 → " . count($dfValues[$df]) . " paramètres générés");
        }

        $averageMaturity = $scoreCount > 0 ? round($totalScore / $scoreCount, 2) : 3.0;

        $result = [
            'df_values' => $dfValues,
            'maturity_level' => $averageMaturity,
            'estimated_maturity' => $averageMaturity,
            'df_suggestions' => count($dfValues),
            'confidence' => 0.9, // Haute confiance car analysé par Ollama
            'project_specifics' => $ollamaResult['project_specifics'] ?? ['Analyse personnalisée par Ollama'],
            'ollama_enhanced' => true,
            'analysis_method' => 'Ollama Mistral - Analyse de document',
            'personalization' => [
                'company_size' => $context['taille_entreprise'] ?? 'non spécifiée',
                'company_name' => $context['nom_entreprise'] ?? 'non spécifiée',
                'constraints' => $context['contraintes'] ?? 'aucune',
                'customization_level' => 'Très élevé - Analyse Ollama personnalisée'
            ]
        ];

        Log::info("✅ Conversion terminée - Maturité: {$averageMaturity}/5, {$scoreCount} DF traités");

        return $result;
    }

    /**
     * Améliorer l'analyse avec les spécificités du projet (méthode legacy)
     */
    private function enhanceWithProjectSpecifics(array $analysis, array $context): array
    {
        $maturityLevel = $analysis['maturity_estimate'] ?? 3.0;

        $enhanced = [
            'df_values' => [],
            'maturity_level' => $maturityLevel,
            'estimated_maturity' => $maturityLevel, // Alias pour compatibilité
            'confidence' => $analysis['confidence_global'] ?? 0.8,
            'project_specifics' => $analysis['project_specifics'] ?? [],
            'ollama_enhanced' => true,
            'analysis_method' => 'Ollama COBIT Expert',
            'personalization' => $this->generatePersonalization($context)
        ];

        // Convertir les scores DF en valeurs pour les paramètres spécifiques de chaque DF
        foreach ($analysis['df_scores'] as $df => $data) {
            $score = max(1, min(5, $data['score']));
            $confidence = $data['confidence'] ?? 0.8;
            $reasoning = $data['reasoning'] ?? '';

            $enhanced['df_values'][$df] = $this->generateDFParameterValues(
                $df,
                $score,
                $confidence,
                $context
            );
        }

        return $enhanced;
    }

    /**
     * Générer des valeurs pour les paramètres d'évaluation d'un DF spécifique
     */
    private function generateContextualObjectiveValues(float $baseScore, float $confidence, string $reasoning, array $context): array
    {
        // Cette méthode est maintenant obsolète, utiliser generateDFParameterValues à la place
        return [];
    }

    /**
     * Générer des valeurs pour les paramètres d'un DF spécifique
     */
    private function generateDFParameterValues(string $dfCode, float $baseScore, float $confidence, array $context): array
    {
        // Récupérer la structure des paramètres pour ce DF
        $dfStructure = $this->getDFStructure($dfCode);
        $paramCount = count($dfStructure['labels']);

        if ($paramCount === 0) {
            // Fallback si pas de structure définie
            return [3.0, 3.0, 3.0, 3.0]; // 4 valeurs par défaut
        }

        $values = [];
        $companySize = $context['taille_entreprise'] ?? 'moyenne';

        // Facteurs d'ajustement selon le contexte
        $sizeMultiplier = $this->getSizeMultiplier($companySize);
        $variationRange = (1 - $confidence) * 0.8; // Variation contrôlée

        for ($i = 0; $i < $paramCount; $i++) {
            $paramLabel = $dfStructure['labels'][$i];
            $paramDefault = $dfStructure['defaults'][$i] ?? 3.0;
            $paramRange = $dfStructure['metadata'];

            // Ajustement contextuel selon le paramètre
            $contextAdjustment = $this->getParameterContextAdjustment($dfCode, $paramLabel, $context);

            // Variation aléatoire contrôlée
            $randomVariation = (rand(-100, 100) / 100) * $variationRange;

            // Score final avec tous les ajustements
            $finalScore = $baseScore + $contextAdjustment + $randomVariation;
            $finalScore *= $sizeMultiplier;

            // Respecter les limites du paramètre
            $minValue = $paramRange['min'] ?? 1;
            $maxValue = $paramRange['max'] ?? 5;

            // Assurer que la valeur est dans les limites
            $finalValue = max($minValue, min($maxValue, $finalScore));

            // Arrondir selon le type de paramètre
            if (isset($paramRange['step']) && $paramRange['step'] == 0.1) {
                $values[] = round($finalValue, 1); // Valeurs décimales pour DF5, DF6, etc.
            } else {
                $values[] = round($finalValue); // Valeurs entières pour DF1, DF2, etc.
            }
        }

        return $values;
    }

    /**
     * Obtenir le multiplicateur selon la taille de l'entreprise
     */
    private function getSizeMultiplier(string $size): float
    {
        return match(strtolower($size)) {
            'petite entreprise (< 100 employés)' => 0.9,
            'moyenne entreprise (100-500 employés)' => 1.0,
            'grande entreprise (500-5000 employés)' => 1.1,
            'très grande entreprise (> 5000 employés)' => 1.2,
            default => 1.0
        };
    }

    /**
     * Détecter le secteur d'activité
     */
    private function detectSector(string $content): string
    {
        $contentLower = strtolower($content);
        
        if (strpos($contentLower, 'banque') !== false || strpos($contentLower, 'finance') !== false) {
            return 'financier';
        } elseif (strpos($contentLower, 'santé') !== false || strpos($contentLower, 'médical') !== false) {
            return 'santé';
        } elseif (strpos($contentLower, 'éducation') !== false || strpos($contentLower, 'université') !== false) {
            return 'éducation';
        } elseif (strpos($contentLower, 'industrie') !== false || strpos($contentLower, 'manufacture') !== false) {
            return 'industriel';
        } elseif (strpos($contentLower, 'service') !== false || strpos($contentLower, 'conseil') !== false) {
            return 'services';
        }
        
        return 'général';
    }

    /**
     * Obtenir le type d'objectif (EDM, APO, BAI, DSS, MEA)
     */
    private function getObjectiveType(int $index): string
    {
        if ($index < 5) return 'EDM';
        if ($index < 19) return 'APO';
        if ($index < 30) return 'BAI';
        if ($index < 36) return 'DSS';
        return 'MEA';
    }

    /**
     * Ajustement selon le type d'objectif et le contexte
     */
    private function getTypeAdjustment(string $type, array $context): float
    {
        $constraints = strtolower($context['contraintes'] ?? '');
        
        // Ajustements contextuels
        if (strpos($constraints, 'budget') !== false) {
            return match($type) {
                'EDM' => 0.2,  // Gouvernance importante avec budget limité
                'APO' => 0.1,  // Planification cruciale
                'BAI' => -0.3, // Moins de nouveaux projets
                'DSS' => 0.0,  // Maintien des services
                'MEA' => 0.1,  // Mesure importante
                default => 0.0
            };
        }
        
        if (strpos($constraints, 'sécurité') !== false || strpos($constraints, 'rgpd') !== false) {
            return match($type) {
                'EDM' => 0.3,  // Gouvernance sécurité
                'APO' => 0.2,  // Planification sécurité
                'BAI' => 0.1,  // Implémentation sécurisée
                'DSS' => 0.4,  // Services sécurisés
                'MEA' => 0.2,  // Monitoring sécurité
                default => 0.0
            };
        }
        
        return 0.0;
    }

    /**
     * Générer la personnalisation du projet
     */
    private function generatePersonalization(array $context): array
    {
        return [
            'company_size' => $context['taille_entreprise'] ?? 'non spécifiée',
            'constraints_impact' => $this->analyzeConstraintsImpact($context['contraintes'] ?? ''),
            'sector_considerations' => 'Ajustements sectoriels appliqués',
            'customization_level' => 'Élevé - Analyse personnalisée'
        ];
    }

    /**
     * Analyser l'impact des contraintes
     */
    private function analyzeConstraintsImpact(string $constraints): array
    {
        $impacts = [];
        $constraintsLower = strtolower($constraints);
        
        if (strpos($constraintsLower, 'budget') !== false) {
            $impacts[] = 'Optimisation des coûts prioritaire';
        }
        if (strpos($constraintsLower, 'temps') !== false) {
            $impacts[] = 'Implémentation accélérée requise';
        }
        if (strpos($constraintsLower, 'sécurité') !== false) {
            $impacts[] = 'Renforcement sécurité critique';
        }
        if (strpos($constraintsLower, 'conformité') !== false) {
            $impacts[] = 'Conformité réglementaire prioritaire';
        }
        
        return $impacts ?: ['Aucune contrainte spécifique identifiée'];
    }

    /**
     * Analyse de fallback si Ollama indisponible
     */
    public function getFallbackAnalysis(string $content, array $context): array
    {
        Log::info('Utilisation de l\'analyse de fallback COBIT (Ollama indisponible)');
        
        // Analyse basique mais contextualisée
        $baseScores = $this->calculateBasicScores($content);
        $sizeMultiplier = $this->getSizeMultiplier($context['taille_entreprise'] ?? 'moyenne');
        
        $dfValues = [];
        for ($i = 1; $i <= 10; $i++) {
            $dfCode = "DF{$i}";
            $baseScore = $baseScores[$dfCode] ?? 3.0;
            $adjustedScore = $baseScore * $sizeMultiplier;
            $dfValues[$dfCode] = $this->generateDFParameterValues($dfCode, $adjustedScore, 0.6, $context);
        }

        $maturityLevel = array_sum($baseScores) / count($baseScores);

        return [
            'df_values' => $dfValues,
            'maturity_level' => $maturityLevel,
            'estimated_maturity' => $maturityLevel, // Alias pour compatibilité
            'df_suggestions' => count($dfValues),
            'confidence' => 0.6,
            'project_specifics' => ['Analyse de base avec personnalisation'],
            'ollama_enhanced' => false,
            'analysis_method' => 'Analyse de base contextualisée',
            'personalization' => $this->generatePersonalization($context)
        ];
    }

    /**
     * Calcul des scores de base
     */
    private function calculateBasicScores(string $content): array
    {
        $contentLower = strtolower($content);
        
        $dfKeywords = [
            'DF1' => ['stratégie', 'objectifs', 'vision'],
            'DF2' => ['kpi', 'métriques', 'performance'],
            'DF3' => ['risque', 'sécurité', 'menace'],
            'DF4' => ['enjeux', 'défis', 'problèmes'],
            'DF5' => ['menaces', 'cyberattaque', 'vulnérabilité'],
            'DF6' => ['conformité', 'réglementation', 'audit'],
            'DF7' => ['rôle', 'positionnement', 'fonction'],
            'DF8' => ['sourcing', 'externalisation', 'fournisseur'],
            'DF9' => ['méthodes', 'agile', 'implémentation'],
            'DF10' => ['taille', 'complexité', 'envergure']
        ];

        $scores = [];
        foreach ($dfKeywords as $df => $keywords) {
            $score = 2.0; // Score de base
            foreach ($keywords as $keyword) {
                $score += substr_count($contentLower, $keyword) * 0.3;
            }
            $scores[$df] = min(5, max(1, $score));
        }

        return $scores;
    }

    /**
     * Obtenir la structure d'un Design Factor
     */
    private function getDFStructure(string $dfCode): array
    {
        // Structure des Design Factors selon le seeder
        $dfStructures = [
            'DF1' => [
                'labels' => ['Croissance', 'Stabilité', 'Coût', 'Innovation'],
                'defaults' => [3, 3, 3, 3],
                'metadata' => ['min' => 1, 'max' => 5, 'step' => 1]
            ],
            'DF2' => [
                'labels' => ['Portefeuille agile', 'Risques métier', 'Conformité réglementaire', 'Objectif 4'],
                'defaults' => [1, 1, 1, 1],
                'metadata' => ['min' => 1, 'max' => 4, 'step' => 1]
            ],
            'DF3' => [
                'labels' => ['Investissement IT', 'Gestion programmes', 'Coûts IT', 'Expertise IT'],
                'defaults' => [3, 3, 3, 3],
                'metadata' => ['min' => 1, 'max' => 5, 'step' => 1]
            ],
            'DF4' => [
                'labels' => ['Problème IT 1', 'Problème IT 2', 'Problème IT 3', 'Problème IT 4'],
                'defaults' => [3, 3, 3, 3],
                'metadata' => ['min' => 1, 'max' => 5, 'step' => 1]
            ],
            'DF5' => [
                'labels' => ['Menaces externes', 'Menaces internes'],
                'defaults' => [0.5, 0.5],
                'metadata' => ['min' => 0, 'max' => 1, 'step' => 0.1]
            ],
            'DF6' => [
                'labels' => ['Exigences réglementaires', 'Exigences sectorielles', 'Exigences internes'],
                'defaults' => [0.5, 0.5, 0.5],
                'metadata' => ['min' => 0, 'max' => 1, 'step' => 0.1]
            ],
            'DF7' => [
                'labels' => ['Support', 'Factory', 'Turnaround'],
                'defaults' => [3, 3, 3],
                'metadata' => ['min' => 1, 'max' => 4, 'step' => 1]
            ],
            'DF8' => [
                'labels' => ['Modèle interne', 'Modèle externe'],
                'defaults' => [1, 1],
                'metadata' => ['min' => 1, 'max' => 3, 'step' => 1]
            ],
            'DF9' => [
                'labels' => ['Méthodes agiles', 'DevOps', 'Méthodes traditionnelles'],
                'defaults' => [0.5, 0.5, 0.5],
                'metadata' => ['min' => 0, 'max' => 1, 'step' => 0.1]
            ],
            'DF10' => [
                'labels' => ['Petite entreprise', 'Moyenne entreprise', 'Grande entreprise'],
                'defaults' => [0.5, 0.5, 0.5],
                'metadata' => ['min' => 0, 'max' => 1, 'step' => 0.1]
            ]
        ];

        return $dfStructures[$dfCode] ?? $dfStructures['DF1'];
    }

    /**
     * Ajustement contextuel pour un paramètre spécifique
     */
    private function getParameterContextAdjustment(string $dfCode, string $paramLabel, array $context): float
    {
        $constraints = strtolower($context['contraintes'] ?? '');
        $size = strtolower($context['taille_entreprise'] ?? '');

        $adjustment = 0.0;

        // Ajustements spécifiques par DF et paramètre
        switch ($dfCode) {
            case 'DF1': // Enterprise Strategy
                if (strpos($paramLabel, 'Croissance') !== false && strpos($constraints, 'croissance') !== false) {
                    $adjustment += 1.0;
                }
                if (strpos($paramLabel, 'Innovation') !== false && strpos($constraints, 'innovation') !== false) {
                    $adjustment += 0.8;
                }
                if (strpos($paramLabel, 'Coût') !== false && strpos($constraints, 'budget') !== false) {
                    $adjustment += 1.2;
                }
                break;

            case 'DF2': // Enterprise Goals
                if (strpos($paramLabel, 'Conformité') !== false && strpos($constraints, 'conformité') !== false) {
                    $adjustment += 1.0;
                }
                if (strpos($paramLabel, 'Risques') !== false && strpos($constraints, 'risque') !== false) {
                    $adjustment += 0.8;
                }
                break;

            case 'DF3': // Risk Profile
                if (strpos($constraints, 'sécurité') !== false || strpos($constraints, 'risque') !== false) {
                    $adjustment += 0.5; // Augmenter tous les paramètres de risque
                }
                break;

            case 'DF5': // Threat Landscape
                if (strpos($constraints, 'sécurité') !== false || strpos($constraints, 'cyber') !== false) {
                    $adjustment += 0.3; // Menaces plus élevées
                }
                break;

            case 'DF6': // Compliance Requirements
                if (strpos($constraints, 'conformité') !== false || strpos($constraints, 'rgpd') !== false) {
                    $adjustment += 0.4;
                }
                break;

            case 'DF8': // Sourcing Model
                if (strpos($constraints, 'équipe') !== false && strpos($constraints, 'réduite') !== false) {
                    if (strpos($paramLabel, 'externe') !== false) {
                        $adjustment += 1.0; // Plus d'externalisation si équipe réduite
                    }
                }
                break;

            case 'DF9': // Implementation Methods
                if (strpos($constraints, 'agile') !== false && strpos($paramLabel, 'agiles') !== false) {
                    $adjustment += 0.3;
                }
                if (strpos($constraints, 'devops') !== false && strpos($paramLabel, 'DevOps') !== false) {
                    $adjustment += 0.3;
                }
                break;

            case 'DF10': // Enterprise Size
                if (strpos($size, 'petite') !== false && strpos($paramLabel, 'Petite') !== false) {
                    $adjustment += 0.4;
                } elseif (strpos($size, 'moyenne') !== false && strpos($paramLabel, 'Moyenne') !== false) {
                    $adjustment += 0.4;
                } elseif (strpos($size, 'grande') !== false && strpos($paramLabel, 'Grande') !== false) {
                    $adjustment += 0.4;
                }
                break;
        }

        return $adjustment;
    }

    /**
     * Vérifier si Ollama est disponible
     */
    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }
}

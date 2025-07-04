<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\DesignFactor;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\JsonResponse;

class CobitController extends Controller
{
    /**
     * Données COBIT 2019 - Configuration statique
     */
    private $cobitData = [
        'objectives' => [
            'EDM01', 'EDM02', 'EDM03', 'EDM04', 'EDM05',
            'APO01', 'APO02', 'APO03', 'APO04', 'APO05', 'APO06', 'APO07', 'APO08', 'APO09', 'APO10', 'APO11', 'APO12', 'APO13', 'APO14',
            'BAI01', 'BAI02', 'BAI03', 'BAI04', 'BAI05', 'BAI06', 'BAI07', 'BAI08', 'BAI09', 'BAI10', 'BAI11',
            'DSS01', 'DSS02', 'DSS03', 'DSS04', 'DSS05', 'DSS06',
            'MEA01', 'MEA02', 'MEA03', 'MEA04'
        ],

        'domains' => [
            'EDM' => ['EDM01', 'EDM02', 'EDM03', 'EDM04', 'EDM05'],
            'APO' => ['APO01', 'APO02', 'APO03', 'APO04', 'APO05', 'APO06', 'APO07', 'APO08', 'APO09', 'APO10', 'APO11', 'APO12', 'APO13', 'APO14'],
            'BAI' => ['BAI01', 'BAI02', 'BAI03', 'BAI04', 'BAI05', 'BAI06', 'BAI07', 'BAI08', 'BAI09', 'BAI10', 'BAI11'],
            'DSS' => ['DSS01', 'DSS02', 'DSS03', 'DSS04', 'DSS05', 'DSS06'],
            'MEA' => ['MEA01', 'MEA02', 'MEA03', 'MEA04']
        ]
    ];

    /**
     * Obtenir les Design Factors depuis la base de données
     */
    private function getDesignFactors()
    {
        return DesignFactor::active()->ordered()->get()->keyBy('code');
    }

    /**
     * Page d'accueil - Dashboard principal
     */
    public function index()
    {
        $designFactors = $this->getDesignFactors();

        return view('cobit.index', [
            'designFactors' => $designFactors,
            'domains' => $this->cobitData['domains']
        ]);
    }

    /**
     * Page d'évaluation interactive
     */
    public function evaluation(Request $request)
    {
        $currentStep = $request->get('step', Session::get('cobit_current_step', 1));
        $evaluationData = Session::get('cobit_evaluation_data', []);
        $designFactors = $this->getDesignFactors();

        // Initialiser les données d'évaluation si nécessaire
        foreach ($designFactors as $df) {
            $dfKey = $df->code;
            if (!isset($evaluationData[$dfKey])) {
                $evaluationData[$dfKey] = [
                    'inputs' => $df->defaults,
                    'completed' => false
                ];
            }
        }

        Session::put('cobit_evaluation_data', $evaluationData);
        Session::put('cobit_current_step', $currentStep);

        return view('cobit.evaluation', [
            'currentStep' => $currentStep,
            'designFactors' => $designFactors,
            'evaluationData' => $evaluationData,
            'totalSteps' => $designFactors->count()
        ]);
    }

    /**
     * Page d'évaluation simplifiée
     */
    public function evaluationSimple(Request $request)
    {
        $designFactors = $this->getDesignFactors();

        return view('cobit.evaluation-simple', [
            'designFactors' => $designFactors
        ]);
    }

    /**
     * Page d'évaluation de test
     */
    public function evaluationTest(Request $request)
    {
        $designFactors = $this->getDesignFactors();

        return view('cobit.test', [
            'designFactors' => $designFactors
        ]);
    }

    /**
     * Page d'accueil KPMG
     */
    public function home(Request $request)
    {
        $designFactors = $this->getDesignFactors();
        $evaluationData = Session::get('cobit_evaluation_data', []);
        $user = \App\Http\Controllers\AuthController::user();

        // Calculer le statut de chaque DF
        $dfStatuses = [];
        for ($i = 1; $i <= 10; $i++) {
            $dfKey = "DF{$i}";
            if (isset($evaluationData[$dfKey])) {
                $dfStatuses[$dfKey] = [
                    'completed' => $evaluationData[$dfKey]['completed'] ?? false,
                    'score' => $evaluationData[$dfKey]['avgScore'] ?? 0,
                    'progress' => $evaluationData[$dfKey]['completion'] ?? 0
                ];
            } else {
                $dfStatuses[$dfKey] = [
                    'completed' => false,
                    'score' => 0,
                    'progress' => 0
                ];
            }
        }

        return view('cobit.home', [
            'designFactors' => $designFactors,
            'dfStatuses' => $dfStatuses,
            'user' => $user
        ]);
    }

    /**
     * Page de détail d'un Design Factor
     */
    public function dfDetail(Request $request, $number)
    {
        $designFactors = $this->getDesignFactors();
        $designFactor = $designFactors->firstWhere('code', "DF{$number}");

        if (!$designFactor) {
            abort(404, "Design Factor DF{$number} non trouvé");
        }

        return view('cobit.df-detail', [
            'designFactor' => $designFactor,
            'designFactors' => $designFactors
        ]);
    }

    /**
     * Canvas final des résultats
     */
    public function canvasFinal(Request $request)
    {
        $designFactors = $this->getDesignFactors();

        return view('cobit.canvas-final', [
            'designFactors' => $designFactors
        ]);
    }

    /**
     * Sauvegarder les données d'un DF
     */
    public function saveDFData(Request $request)
    {
        try {
            $dfNumber = $request->input('df');
            $inputs = $request->input('inputs');
            $scores = $request->input('scores');
            $avgScore = $request->input('avgScore', 0);
            $completion = $request->input('completion', 0);
            $completed = $request->input('completed', false);

            // Valider les données
            if (!$dfNumber || !is_array($inputs)) {
                return response()->json(['success' => false, 'message' => 'Données invalides']);
            }

            // Sauvegarder en session
            $evaluationData = Session::get('cobit_evaluation_data', []);
            $evaluationData["DF{$dfNumber}"] = [
                'inputs' => $inputs,
                'scores' => $scores ?? [],
                'avgScore' => $avgScore,
                'completion' => $completion,
                'completed' => $completed,
                'updated_at' => now()->toISOString()
            ];

            Session::put('cobit_evaluation_data', $evaluationData);

            // Calculer le statut global
            $completedDFs = 0;
            for ($i = 1; $i <= 10; $i++) {
                if (isset($evaluationData["DF{$i}"]) && $evaluationData["DF{$i}"]['completed']) {
                    $completedDFs++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "DF{$dfNumber} sauvegardé avec succès",
                'completedDFs' => $completedDFs,
                'canAccessCanvas' => $completedDFs >= 10
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }



    /**
     * Page des résultats finaux
     */
    public function results()
    {
        $evaluationData = Session::get('cobit_evaluation_data', []);
        $finalResults = $this->calculateFinalResults($evaluationData);

        return view('cobit.results', [
            'finalResults' => $finalResults,
            'evaluationData' => $evaluationData,
            'domains' => $this->cobitData['domains'],
            'objectives' => $this->cobitData['objectives']
        ]);
    }



    /**
     * Méthodes privées pour les calculs COBIT
     */

    /**
     * Calcul des scores basé sur les matrices COBIT
     */
    private function calculateScores($dfNumber, $inputs)
    {
        $df = DesignFactor::where('code', "DF{$dfNumber}")->first();
        if (!$df) {
            return array_fill(0, count($this->cobitData['objectives']), 0);
        }

        // Utiliser la matrice du DF ou générer une matrice par défaut
        $matrix = $df->matrix ?? $this->generateCobitMatrix($dfNumber, count($inputs));

        $scores = [];
        foreach ($this->cobitData['objectives'] as $index => $objective) {
            $score = 0;
            for ($i = 0; $i < count($inputs); $i++) {
                $score += ($matrix[$index][$i] ?? 0) * $inputs[$i];
            }
            $scores[] = round($score / count($inputs), 2);
        }

        return $scores;
    }

    /**
     * Calcul des baselines
     */
    private function calculateBaseline($dfNumber)
    {
        // Baseline simplifiée - dans un vrai projet, cela viendrait de données de référence
        $baselines = [];
        foreach ($this->cobitData['objectives'] as $objective) {
            $baselines[] = 2.5; // Baseline par défaut
        }
        return $baselines;
    }

    /**
     * Calcul des moyennes par domaine
     */
    private function calculateDomainAverages($scores, $baselines)
    {
        $domainAverages = [];
        $domainBaselines = [];

        foreach ($this->cobitData['domains'] as $domain => $objectives) {
            $domainScore = 0;
            $domainBaseline = 0;
            $count = count($objectives);

            foreach ($objectives as $objective) {
                $index = array_search($objective, $this->cobitData['objectives']);
                if ($index !== false) {
                    $domainScore += $scores[$index] ?? 0;
                    $domainBaseline += $baselines[$index] ?? 0;
                }
            }

            $domainAverages[$domain] = $count > 0 ? round($domainScore / $count, 2) : 0;
            $domainBaselines[$domain] = $count > 0 ? round($domainBaseline / $count, 2) : 0;
        }

        return [
            'labels' => array_keys($domainAverages),
            'avgData' => array_values($domainAverages),
            'baselineData' => array_values($domainBaselines)
        ];
    }

    /**
     * Calcul des résultats finaux
     */
    private function calculateFinalResults($evaluationData)
    {
        $finalScores = array_fill(0, count($this->cobitData['objectives']), 0);
        $finalBaselines = array_fill(0, count($this->cobitData['objectives']), 0);

        // Agrégation des scores de tous les Design Factors
        foreach ($evaluationData as $dfKey => $inputs) {
            $dfNumber = str_replace('DF', '', $dfKey);
            $scores = $this->calculateScores($dfNumber, $inputs);
            $baselines = $this->calculateBaseline($dfNumber);

            for ($i = 0; $i < count($finalScores); $i++) {
                $finalScores[$i] += $scores[$i] ?? 0;
                $finalBaselines[$i] += $baselines[$i] ?? 0;
            }
        }

        // Calcul des moyennes
        $dfCount = count($evaluationData);
        if ($dfCount > 0) {
            for ($i = 0; $i < count($finalScores); $i++) {
                $finalScores[$i] = round($finalScores[$i] / $dfCount, 2);
                $finalBaselines[$i] = round($finalBaselines[$i] / $dfCount, 2);
            }
        }

        // Calcul des gaps et priorités
        $results = [];
        foreach ($this->cobitData['objectives'] as $index => $objective) {
            $score = $finalScores[$index];
            $baseline = $finalBaselines[$index];
            $gap = $score - $baseline;
            $priority = abs($gap) > $baseline * 0.5 ? 'H' : (abs($gap) > $baseline * 0.2 ? 'M' : 'L');

            $results[] = [
                'objective' => $objective,
                'score' => $score,
                'baseline' => $baseline,
                'gap' => round($gap, 2),
                'priority' => $priority
            ];
        }

        return [
            'objectives' => $results,
            'domainAverages' => $this->calculateDomainAverages($finalScores, $finalBaselines),
            'summary' => [
                'totalObjectives' => count($results),
                'highPriority' => count(array_filter($results, fn($r) => $r['priority'] === 'H')),
                'mediumPriority' => count(array_filter($results, fn($r) => $r['priority'] === 'M')),
                'lowPriority' => count(array_filter($results, fn($r) => $r['priority'] === 'L'))
            ]
        ];
    }

    /**
     * Générer une matrice COBIT pour un Design Factor
     */
    private function generateCobitMatrix($dfNumber, $inputCount)
    {
        $objectiveCount = count($this->cobitData['objectives']);

        // Paramètres de génération basés sur le DF
        $params = $this->getMatrixParameters($dfNumber);

        $matrix = [];
        for ($i = 0; $i < $objectiveCount; $i++) {
            $row = [];
            for ($j = 0; $j < $inputCount; $j++) {
                // Générer des valeurs basées sur les paramètres du DF
                $baseValue = $params['base'] + (rand(0, 100) / 100) * $params['variance'];
                $row[] = round($baseValue, 3);
            }
            $matrix[] = $row;
        }

        return $matrix;
    }

    /**
     * Obtenir les paramètres de matrice pour un DF
     */
    private function getMatrixParameters($dfNumber)
    {
        $parameters = [
            1 => ['base' => 0.15, 'variance' => 0.20], // Enterprise Strategy
            2 => ['base' => 0.12, 'variance' => 0.15], // Enterprise Goals
            3 => ['base' => 0.18, 'variance' => 0.25], // Risk Profile
            4 => ['base' => 0.20, 'variance' => 0.30], // IT Issues
            5 => ['base' => 0.25, 'variance' => 0.35], // Threat Landscape
            6 => ['base' => 0.14, 'variance' => 0.18], // Compliance
            7 => ['base' => 0.16, 'variance' => 0.22], // Role of IT
            8 => ['base' => 0.10, 'variance' => 0.15], // Sourcing Model
            9 => ['base' => 0.13, 'variance' => 0.18], // Implementation Methods
            10 => ['base' => 0.08, 'variance' => 0.12], // Enterprise Size
        ];

        return $parameters[$dfNumber] ?? ['base' => 0.15, 'variance' => 0.20];
    }

    /**
     * Sauvegarder une évaluation
     */
    public function saveEvaluation(Request $request)
    {
        try {
            $step = $request->input('step');
            $inputs = $request->input('inputs');

            // Valider les données
            if (!$step || !is_array($inputs)) {
                return response()->json(['success' => false, 'message' => 'Données invalides']);
            }

            // Sauvegarder en session
            $evaluationData = Session::get('cobit_evaluation_data', []);
            $evaluationData["DF{$step}"] = [
                'inputs' => $inputs,
                'completed' => true,
                'updated_at' => now()
            ];

            Session::put('cobit_evaluation_data', $evaluationData);

            return response()->json(['success' => true, 'message' => 'Données sauvegardées']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur de sauvegarde: ' . $e->getMessage()]);
        }
    }

    /**
     * Réinitialiser l'évaluation
     */
    public function reset()
    {
        try {
            Session::forget('cobit_evaluation_data');
            Session::forget('cobit_current_step');

            return response()->json(['success' => true, 'message' => 'Évaluation réinitialisée']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur de réinitialisation: ' . $e->getMessage()]);
        }
    }

    /**
     * Calculer les résultats pour un DF spécifique
     */
    public function calculateResults($dfNumber)
    {
        try {
            $evaluationData = Session::get('cobit_evaluation_data', []);
            $dfKey = "DF{$dfNumber}";

            if (!isset($evaluationData[$dfKey])) {
                return response()->json(['success' => false, 'message' => 'Aucune donnée pour ce DF']);
            }

            $inputs = $evaluationData[$dfKey]['inputs'];
            $scores = $this->calculateScores($dfNumber, $inputs);
            $baselines = array_fill(0, count($scores), 2.5);

            $domainAverages = $this->calculateDomainAverages($scores, $baselines);

            return response()->json([
                'success' => true,
                'scores' => $scores,
                'baselines' => $baselines,
                'domainAverages' => $domainAverages
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur de calcul: ' . $e->getMessage()]);
        }
    }

    /**
     * Export PDF (placeholder)
     */
    public function exportPdf()
    {
        // TODO: Implémenter l'export PDF avec DomPDF
        return response()->json(['success' => false, 'message' => 'Export PDF en cours de développement']);
    }

    /**
     * Import de données (placeholder)
     */
    public function import(Request $request)
    {
        // TODO: Implémenter l'import de données
        return response()->json(['success' => false, 'message' => 'Import en cours de développement']);
    }

    /**
     * Mettre à jour les inputs d'un DF
     */
    public function updateInputs(Request $request)
    {
        try {
            $dfNumber = $request->input('df');
            $inputs = $request->input('inputs');

            // Valider les données
            if (!$dfNumber || !is_array($inputs)) {
                return response()->json(['success' => false, 'message' => 'Données invalides']);
            }

            // Sauvegarder en session
            $evaluationData = Session::get('cobit_evaluation_data', []);
            $evaluationData["DF{$dfNumber}"] = [
                'inputs' => $inputs,
                'completed' => count(array_filter($inputs)) > 0,
                'updated_at' => now()
            ];

            Session::put('cobit_evaluation_data', $evaluationData);

            // Calculer les résultats
            $scores = $this->calculateScores($dfNumber, $inputs);
            $baselines = array_fill(0, count($scores), 2.5);
            $domainAverages = $this->calculateDomainAverages($scores, $baselines);

            return response()->json([
                'success' => true,
                'scores' => $scores,
                'baselines' => $baselines,
                'domainAverages' => $domainAverages,
                'objectives' => $this->cobitData['objectives']
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }
}

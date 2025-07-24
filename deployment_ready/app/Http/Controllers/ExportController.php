<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;

class ExportController extends Controller
{
    /**
     * Exporter les résultats en PDF
     */
    public function exportPDF(Request $request)
    {
        // Vérifier l'authentification
        if (!AuthController::check()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $user = AuthController::user();
        $evaluationData = Session::get('cobit_evaluation_data', []);

        // Générer le contenu HTML pour le PDF
        $htmlContent = $this->generatePDFContent($evaluationData, $user);

        // Créer le PDF avec DomPDF (à installer: composer require dompdf/dompdf)
        try {
            $pdf = \PDF::loadHTML($htmlContent);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'COBIT_2019_Evaluation_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            // Fallback: retourner le HTML si PDF ne fonctionne pas
            return response($htmlContent)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'attachment; filename="COBIT_2019_Evaluation.html"');
        }
    }

    /**
     * Générer le contenu HTML pour le PDF
     */
    private function generatePDFContent($evaluationData, $user)
    {
        $globalMetrics = $this->calculateGlobalMetrics($evaluationData);
        $aiAnalysis = $this->generateAIAnalysis($evaluationData);
        
        return view('exports.pdf-report', [
            'user' => $user,
            'evaluationData' => $evaluationData,
            'globalMetrics' => $globalMetrics,
            'aiAnalysis' => $aiAnalysis,
            'generatedAt' => now()->format('d/m/Y H:i:s')
        ])->render();
    }

    /**
     * Calculer les métriques globales
     */
    private function calculateGlobalMetrics($evaluationData)
    {
        $metrics = [
            'totalDFs' => 10,
            'completedDFs' => 0,
            'globalScore' => 0,
            'averageCompletion' => 0,
            'maturityLevel' => 0,
            'totalObjectives' => 0,
            'criticalObjectives' => 0,
            'highPerformanceObjectives' => 0
        ];

        $totalScore = 0;
        $totalCompletion = 0;
        $validDFs = 0;

        foreach ($evaluationData as $dfKey => $dfData) {
            if (isset($dfData['completed']) && $dfData['completed']) {
                $metrics['completedDFs']++;
                $totalScore += $dfData['avgScore'] ?? 0;
                $totalCompletion += $dfData['completion'] ?? 0;
                $validDFs++;
            }
        }

        if ($validDFs > 0) {
            $metrics['globalScore'] = $totalScore / $validDFs;
            $metrics['averageCompletion'] = $totalCompletion / $validDFs;
            $metrics['maturityLevel'] = round($metrics['globalScore']);
        }

        return $metrics;
    }

    /**
     * Générer l'analyse IA
     */
    private function generateAIAnalysis($evaluationData)
    {
        $analysis = [
            'overallRisk' => 'Medium',
            'keyRecommendations' => [],
            'strengths' => [],
            'weaknesses' => [],
            'actionPlan' => [],
            'complianceStatus' => 'Partial',
            'nextSteps' => []
        ];

        $scores = [];
        foreach ($evaluationData as $dfData) {
            if (isset($dfData['avgScore'])) {
                $scores[] = $dfData['avgScore'];
            }
        }

        if (!empty($scores)) {
            $avgScore = array_sum($scores) / count($scores);
            
            // Déterminer le risque global
            if ($avgScore < 2) {
                $analysis['overallRisk'] = 'High';
                $analysis['complianceStatus'] = 'Non-Compliant';
            } elseif ($avgScore < 3.5) {
                $analysis['overallRisk'] = 'Medium';
                $analysis['complianceStatus'] = 'Partial';
            } else {
                $analysis['overallRisk'] = 'Low';
                $analysis['complianceStatus'] = 'Compliant';
            }

            // Générer les recommandations
            $analysis['keyRecommendations'] = $this->generateRecommendations($avgScore, $evaluationData);
            $analysis['actionPlan'] = $this->generateActionPlan($avgScore, $evaluationData);
        }

        return $analysis;
    }

    /**
     * Générer les recommandations IA
     */
    private function generateRecommendations($avgScore, $evaluationData)
    {
        $recommendations = [];

        if ($avgScore < 2) {
            $recommendations[] = "🚨 Action urgente requise: Refonte complète du système de gouvernance IT";
            $recommendations[] = "📋 Établir un comité de gouvernance IT avec des responsabilités claires";
            $recommendations[] = "🎯 Prioriser les Design Factors critiques pour un impact immédiat";
        } elseif ($avgScore < 3.5) {
            $recommendations[] = "📈 Amélioration continue nécessaire dans plusieurs domaines";
            $recommendations[] = "🔍 Identifier et traiter les lacunes spécifiques par domaine COBIT";
            $recommendations[] = "📊 Mettre en place des KPIs de suivi pour mesurer les progrès";
        } else {
            $recommendations[] = "✅ Excellente performance globale - Maintenir les standards";
            $recommendations[] = "🚀 Capitaliser sur les points forts pour l'innovation";
            $recommendations[] = "📚 Partager les bonnes pratiques avec d'autres unités";
        }

        return $recommendations;
    }

    /**
     * Générer le plan d'action
     */
    private function generateActionPlan($avgScore, $evaluationData)
    {
        $actionPlan = [];

        // Court terme (0-3 mois)
        $actionPlan['short_term'] = [
            "Évaluation détaillée des Design Factors sous-performants",
            "Formation des équipes sur COBIT 2019",
            "Mise en place d'un tableau de bord de suivi"
        ];

        // Moyen terme (3-12 mois)
        $actionPlan['medium_term'] = [
            "Implémentation des processus COBIT prioritaires",
            "Développement des compétences internes",
            "Audit intermédiaire des progrès"
        ];

        // Long terme (12+ mois)
        $actionPlan['long_term'] = [
            "Optimisation continue des processus",
            "Intégration avec d'autres frameworks",
            "Certification COBIT de l'organisation"
        ];

        return $actionPlan;
    }

    /**
     * Exporter en Excel
     */
    public function exportExcel(Request $request)
    {
        // Vérifier l'authentification
        if (!AuthController::check()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $evaluationData = Session::get('cobit_evaluation_data', []);
        
        // Générer le contenu CSV (simple)
        $csvContent = $this->generateCSVContent($evaluationData);
        
        $filename = 'COBIT_2019_Data_' . date('Y-m-d_H-i-s') . '.csv';
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Générer le contenu CSV
     */
    private function generateCSVContent($evaluationData)
    {
        $csv = "Design Factor,Score Moyen,Completion (%),Statut,Dernière Mise à Jour\n";
        
        for ($i = 1; $i <= 10; $i++) {
            $dfKey = "DF{$i}";
            $dfData = $evaluationData[$dfKey] ?? [];
            
            $score = $dfData['avgScore'] ?? 0;
            $completion = $dfData['completion'] ?? 0;
            $status = ($dfData['completed'] ?? false) ? 'Complété' : 'En cours';
            $updated = $dfData['updated_at'] ?? 'N/A';
            
            $csv .= "{$dfKey},{$score},{$completion},{$status},{$updated}\n";
        }
        
        return $csv;
    }
}

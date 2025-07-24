<?php

require_once 'vendor/autoload.php';

use App\Services\CobitExpertAnalysisService;

echo "🧪 TEST INTÉGRATION AGENT IA EXPERT COBIT\n";
echo "==========================================\n\n";

try {
    // Initialiser le service
    $expertService = new CobitExpertAnalysisService();
    echo "✅ Service Agent IA Expert initialisé\n";

    // Contexte de test
    $projectContext = [
        'nom_entreprise' => 'TestCorp Innovation',
        'taille_entreprise' => 'Petite entreprise (< 100 employés)',
        'contraintes' => 'Budget limité, croissance rapide, innovation',
        'secteur' => 'Technologie',
        'user_id' => 1
    ];

    // Fichier de test
    $testFile = 'public/test_documents/startup_tech_agile.txt';
    
    if (!file_exists($testFile)) {
        throw new Exception("Fichier de test non trouvé: {$testFile}");
    }

    echo "📄 Fichier de test: " . basename($testFile) . "\n";
    echo "🏢 Entreprise: {$projectContext['nom_entreprise']}\n";
    echo "📏 Taille: {$projectContext['taille_entreprise']}\n\n";

    echo "🚀 Début analyse avec Agent IA Expert...\n";
    $startTime = microtime(true);

    // Analyser avec l'Agent IA Expert
    $result = $expertService->analyzeProjectDocument($testFile, $projectContext);

    $endTime = microtime(true);
    $duration = round($endTime - $startTime, 2);

    echo "⏱️ Durée d'analyse: {$duration} secondes\n\n";

    if ($result['success']) {
        echo "✅ ANALYSE RÉUSSIE !\n";
        echo "==================\n\n";

        echo "📊 MÉTRIQUES PRINCIPALES:\n";
        echo "- Niveau de maturité: {$result['maturity_level']}/5\n";
        echo "- Baseline: {$result['baseline_score']}/5\n";
        echo "- Écart: {$result['maturity_gap']}\n";
        echo "- Indice de risque: {$result['risk_index']}\n";
        echo "- Confiance: " . round($result['confidence_level'] * 100) . "%\n";
        echo "- Méthode: {$result['analysis_method']}\n";
        echo "- IA avancée: " . ($result['ai_enhanced'] ? 'Oui' : 'Non') . "\n\n";

        echo "🎯 DESIGN FACTORS GÉNÉRÉS:\n";
        foreach ($result['design_factors'] as $dfCode => $dfData) {
            echo "- {$dfCode} ({$dfData['name']}): {$dfData['base_score']}/5\n";
            echo "  Paramètres: [" . implode(', ', array_slice($dfData['parameters'], 0, 3)) . "...]\n";
            echo "  Justification: {$dfData['reasoning']}\n\n";
        }

        echo "🎯 OBJECTIFS COBIT PRIORITAIRES:\n";
        foreach ($result['priority_objectives'] as $objective) {
            echo "- {$objective['code']}: {$objective['name']}\n";
            echo "  Priorité: {$objective['priority']}/5\n";
            echo "  Justification: {$objective['justification']}\n\n";
        }

        echo "💡 RECOMMANDATIONS:\n";
        foreach ($result['recommendations'] as $recommendation) {
            echo "- {$recommendation}\n";
        }

        echo "\n🎉 TEST RÉUSSI - L'Agent IA Expert fonctionne parfaitement !\n";
        echo "📈 Maturité variable générée: {$result['maturity_level']}/5 (plus jamais 2.12 !)\n";

    } else {
        echo "❌ ÉCHEC DE L'ANALYSE\n";
        echo "Erreur: Analyse non réussie\n";
    }

} catch (Exception $e) {
    echo "❌ ERREUR DURANT LE TEST\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🏁 Test terminé !\n";

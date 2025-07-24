<?php

echo "🧪 TEST AGENT IA EXPERT COBIT CORRIGÉ\n";
echo "=====================================\n\n";

// Test direct de l'API
$url = 'http://localhost:8000/cobit/ai-analyze';
$testFile = 'public/test_documents/startup_tech_agile.txt';

if (!file_exists($testFile)) {
    echo "❌ Fichier de test non trouvé: {$testFile}\n";
    echo "📁 Création du fichier de test...\n";
    
    $testContent = "STRATÉGIE IT 2024 - INNOVATECH STARTUP

PROFIL ENTREPRISE:
- Nom: InnovaTech Solutions
- Secteur: Technologies émergentes (IA, Blockchain, IoT)
- Taille: 25 employés (startup en hypercroissance)
- Chiffre d'affaires: 800k€ (croissance +400% par an)

OBJECTIFS 2024:
1. CROISSANCE EXPLOSIVE: Tripler le CA
2. INNOVATION PERMANENTE: 1 nouveau produit/mois
3. SCALING RAPIDE: 100 employés d'ici fin 2024
4. EXCELLENCE TECHNIQUE: Technologies de pointe

CONTRAINTES STARTUP:
- Budget IT ultra-serré: 150k€ total
- Équipe IT minimaliste: 3 développeurs full-stack
- Pression investisseurs: ROI rapide obligatoire
- Burn rate élevé: 200k€/mois

MÉTHODES DÉVELOPPEMENT:
- 100% Agile/Scrum
- Sprints de 1 semaine
- Déploiement continu (50+ déploiements/jour)
- Feature flags, A/B testing permanent

GESTION DES RISQUES:
- Risque technique: TRÈS ÉLEVÉ (technologies émergentes)
- Risque financier: CRITIQUE (dépendance financement)
- Risque concurrentiel: MAXIMUM (disruption permanente)

SOURCING EXTERNE MASSIF:
- 80% services externalisés
- SaaS-first strategy
- Freelances pour pics de charge
- Open source privilégié";

    if (!is_dir('public/test_documents')) {
        mkdir('public/test_documents', 0755, true);
    }
    
    file_put_contents($testFile, $testContent);
    echo "✅ Fichier de test créé\n\n";
}

echo "📤 Test de l'API Agent IA Expert...\n";
echo "URL: {$url}\n";
echo "Fichier: " . basename($testFile) . "\n\n";

// Préparer les données
$postData = [
    'documents' => [
        new CURLFile(realpath($testFile), 'text/plain', basename($testFile))
    ]
];

// Initialiser cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

echo "🚀 Envoi de la requête...\n";
$startTime = microtime(true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

$endTime = microtime(true);
$duration = round($endTime - $startTime, 2);

curl_close($ch);

echo "⏱️ Durée: {$duration} secondes\n";
echo "📊 Code HTTP: {$httpCode}\n";

if ($error) {
    echo "❌ Erreur cURL: {$error}\n";
    exit(1);
}

if ($httpCode !== 200) {
    echo "❌ Erreur HTTP {$httpCode}\n";
    echo "📄 Réponse:\n";
    echo substr($response, 0, 500) . "...\n";
    exit(1);
}

echo "📄 Réponse reçue (" . strlen($response) . " caractères)\n\n";

// Parser la réponse JSON
$data = json_decode($response, true);

if (!$data) {
    echo "❌ Erreur parsing JSON\n";
    echo "📄 Réponse brute:\n";
    echo substr($response, 0, 1000) . "...\n";
    exit(1);
}

if (!$data['success']) {
    echo "❌ Analyse échouée\n";
    echo "Message: " . ($data['message'] ?? 'Erreur inconnue') . "\n";
    exit(1);
}

echo "✅ ANALYSE RÉUSSIE !\n";
echo "==================\n\n";

$analysis = $data['analysis'];

echo "📊 RÉSULTATS PRINCIPAUX:\n";
echo "- Niveau de maturité: " . ($analysis['estimated_maturity'] ?? 'N/A') . "/5\n";
echo "- Méthode d'analyse: " . ($analysis['analysis_method'] ?? 'N/A') . "\n";
echo "- IA avancée: " . (($analysis['ollama_enhanced'] ?? false) ? 'Oui' : 'Non') . "\n";
echo "- Confiance: " . round(($analysis['confidence_global'] ?? 0) * 100) . "%\n";
echo "- Documents analysés: " . ($analysis['documents_analyzed'] ?? 0) . "\n";
echo "- Design Factors: " . ($analysis['df_suggestions'] ?? 0) . "\n\n";

if (isset($analysis['df_values'])) {
    echo "🎯 DESIGN FACTORS GÉNÉRÉS:\n";
    foreach ($analysis['df_values'] as $dfCode => $values) {
        echo "- {$dfCode}: [" . implode(', ', array_slice($values, 0, 3)) . "...] (" . count($values) . " paramètres)\n";
    }
    echo "\n";
}

if (isset($analysis['priority_objectives'])) {
    echo "🎯 OBJECTIFS COBIT PRIORITAIRES:\n";
    foreach ($analysis['priority_objectives'] as $objective) {
        echo "- {$objective['code']}: {$objective['name']}\n";
    }
    echo "\n";
}

echo "💬 Résumé: " . ($analysis['analysis_summary'] ?? 'N/A') . "\n\n";

// Vérifier la variabilité
$maturity = $analysis['estimated_maturity'] ?? 0;
if ($maturity == 2.12) {
    echo "⚠️ ATTENTION: Maturité toujours à 2.12 - Problème non résolu\n";
} else {
    echo "🎉 SUCCÈS: Maturité variable générée ({$maturity}/5) - Problème résolu !\n";
}

echo "\n🏁 Test terminé !\n";

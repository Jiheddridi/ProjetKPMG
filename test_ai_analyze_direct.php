<?php

echo "🔍 TEST DIRECT AI-ANALYZE\n";
echo "=========================\n\n";

// Test direct de la route ai-analyze
$url = 'http://localhost:8000/cobit/ai-analyze';

// Créer un fichier temporaire pour le test
$testContent = "STRATÉGIE IT - TESTCORP
CONTEXTE: Entreprise TestCorp, 50 employés
OBJECTIFS: Croissance rapide, innovation
CONTRAINTES: Budget limité, équipe réduite
ENJEUX: Cybersécurité, scalabilité";

$tempFile = tempnam(sys_get_temp_dir(), 'cobit_test_');
file_put_contents($tempFile, $testContent);

// Préparer les données multipart/form-data
$boundary = '----WebKitFormBoundary' . uniqid();
$postBody = '';

// Ajouter le token CSRF (simulé)
$postBody .= "--{$boundary}\r\n";
$postBody .= "Content-Disposition: form-data; name=\"_token\"\r\n\r\n";
$postBody .= "test_token\r\n";

// Ajouter le fichier
$postBody .= "--{$boundary}\r\n";
$postBody .= "Content-Disposition: form-data; name=\"documents[0]\"; filename=\"test.txt\"\r\n";
$postBody .= "Content-Type: text/plain\r\n\r\n";
$postBody .= $testContent . "\r\n";
$postBody .= "--{$boundary}--\r\n";

// Configuration cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: multipart/form-data; boundary=' . $boundary,
    'Content-Length: ' . strlen($postBody)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

echo "📤 Envoi de la requête vers {$url}...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "📊 Code HTTP: {$httpCode}\n";

if (curl_errno($ch)) {
    echo "❌ Erreur cURL: " . curl_error($ch) . "\n";
} else {
    echo "📄 Réponse reçue (" . strlen($response) . " caractères)\n";
    
    // Analyser la réponse
    if ($httpCode === 200) {
        // Vérifier si c'est du JSON
        $jsonResponse = json_decode($response, true);
        if ($jsonResponse) {
            echo "✅ Réponse JSON valide:\n";
            echo "   - Success: " . ($jsonResponse['success'] ? 'true' : 'false') . "\n";
            if (isset($jsonResponse['message'])) {
                echo "   - Message: " . $jsonResponse['message'] . "\n";
            }
            if (isset($jsonResponse['analysis'])) {
                echo "   - Analyse présente: Oui\n";
                $analysis = $jsonResponse['analysis'];
                if (isset($analysis['maturity_level'])) {
                    echo "   - Maturité: " . $analysis['maturity_level'] . "\n";
                }
                if (isset($analysis['ollama_enhanced'])) {
                    echo "   - Ollama: " . ($analysis['ollama_enhanced'] ? 'Oui' : 'Non') . "\n";
                }
            }
        } else {
            echo "❌ Réponse non-JSON (probablement HTML d'erreur):\n";
            echo "📄 Début de réponse:\n";
            echo substr($response, 0, 500) . "\n";
            
            // Chercher des indices d'erreur
            if (strpos($response, 'Fatal error') !== false) {
                echo "🚨 ERREUR FATALE PHP détectée\n";
            }
            if (strpos($response, 'Class') !== false && strpos($response, 'not found') !== false) {
                echo "🚨 CLASSE NON TROUVÉE détectée\n";
            }
            if (strpos($response, 'Call to undefined') !== false) {
                echo "🚨 MÉTHODE NON DÉFINIE détectée\n";
            }
        }
    } else {
        echo "❌ Erreur HTTP {$httpCode}\n";
        echo "📄 Réponse d'erreur:\n";
        echo substr($response, 0, 1000) . "\n";
    }
}

curl_close($ch);
unlink($tempFile);

echo "\n🔍 DIAGNOSTIC:\n";
echo "==============\n";

if ($httpCode === 200 && json_decode($response, true)) {
    echo "✅ Route ai-analyze fonctionne correctement\n";
    echo "✅ Pas d'erreur 'Unexpected token'\n";
} else {
    echo "❌ Problème détecté avec la route ai-analyze\n";
    echo "❌ C'est la cause de l'erreur 'Unexpected token'\n";
    echo "💡 Vérifiez les logs Laravel pour plus de détails\n";
}

echo "\n🚀 Test terminé !\n";

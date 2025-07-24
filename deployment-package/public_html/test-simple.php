<?php
/**
 * Test simple pour vérifier que PHP fonctionne sur InfinityFree
 * Accès: https://cobite2019platforme47.infinityfreeapp.com/test-simple.php
 */

echo "<h1>Test PHP - InfinityFree</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";

// Test de la base de données
echo "<h2>Test Base de Données</h2>";
try {
    $pdo = new PDO('mysql:host=sql200.infinityfree.com;dbname=if0_35478380_cobit', 'if0_35478380', 'Jihed123');
    echo "<p style='color: green;'>✅ Connexion base de données réussie!</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur base de données: " . $e->getMessage() . "</p>";
}

echo "<h2>Test Fichiers Laravel</h2>";
$laravel_files = [
    '../app/Http/Controllers/CobitController.php',
    '../routes/web.php',
    '../.env',
    '../vendor/autoload.php'
];

foreach ($laravel_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file existe</p>";
    } else {
        echo "<p style='color: red;'>❌ $file manquant</p>";
    }
}

echo "<p><strong>Si tous les tests sont verts, Laravel devrait fonctionner!</strong></p>";
echo "<p><a href='index.php'>Tester Laravel</a></p>";
?>

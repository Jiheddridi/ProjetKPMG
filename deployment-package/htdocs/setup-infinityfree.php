<?php
/**
 * Script de configuration automatique pour InfinityFree
 * Exécutez ce script après avoir uploadé tous les fichiers
 */

echo "<h1>Configuration Automatique pour InfinityFree</h1>";

// 1. Vérifier la structure des dossiers
echo "<h2>1. Vérification de la structure</h2>";

$requiredDirs = [
    'app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'storage', 'vendor'
];

$missingDirs = [];
foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        $missingDirs[] = $dir;
        echo "<p style='color: red;'>❌ Dossier manquant: $dir</p>";
    } else {
        echo "<p style='color: green;'>✅ Dossier trouvé: $dir</p>";
    }
}

// 2. Vérifier les fichiers critiques
echo "<h2>2. Vérification des fichiers critiques</h2>";

$requiredFiles = [
    '.env' => 'Configuration de l\'environnement',
    'artisan' => 'Interface en ligne de commande Laravel',
    'composer.json' => 'Configuration Composer',
    'vendor/autoload.php' => 'Autoloader Composer'
];

$missingFiles = [];
foreach ($requiredFiles as $file => $description) {
    if (!file_exists($file)) {
        $missingFiles[] = $file;
        echo "<p style='color: red;'>❌ Fichier manquant: $file ($description)</p>";
    } else {
        echo "<p style='color: green;'>✅ Fichier trouvé: $file</p>";
    }
}

// 3. Vérifier les permissions
echo "<h2>3. Vérification des permissions</h2>";

$dirsToCheck = ['storage', 'bootstrap/cache'];
foreach ($dirsToCheck as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<p style='color: green;'>✅ $dir est accessible en écriture</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ $dir n'est pas accessible en écriture</p>";
        }
    }
}

// 4. Test de la base de données
echo "<h2>4. Test de la base de données</h2>";

if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    
    // Extraire les informations de la base de données
    preg_match('/DB_HOST=(.*)/', $envContent, $hostMatch);
    preg_match('/DB_DATABASE=(.*)/', $envContent, $dbMatch);
    preg_match('/DB_USERNAME=(.*)/', $envContent, $userMatch);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
    
    $host = trim($hostMatch[1] ?? '');
    $database = trim($dbMatch[1] ?? '');
    $username = trim($userMatch[1] ?? '');
    $password = trim($passMatch[1] ?? '');
    
    if ($host && $database && $username) {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<p style='color: green;'>✅ Connexion à la base de données réussie</p>";
            
            // Vérifier les tables
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<p>Tables trouvées: " . count($tables) . "</p>";
            
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Erreur de connexion à la base de données: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Informations de base de données incomplètes dans .env</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Fichier .env non trouvé</p>";
}

// 5. Configuration automatique
echo "<h2>5. Configuration automatique</h2>";

if (empty($missingDirs) && empty($missingFiles)) {
    echo "<p style='color: green;'>✅ Tous les fichiers requis sont présents</p>";
    
    // Créer les liens symboliques si nécessaire
    if (!file_exists('public_html/storage')) {
        if (symlink('../storage/app/public', 'public_html/storage')) {
            echo "<p style='color: green;'>✅ Lien symbolique storage créé</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Impossible de créer le lien symbolique storage</p>";
        }
    }
    
    // Vérifier la configuration Laravel
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        
        try {
            $app = require_once 'bootstrap/app.php';
            echo "<p style='color: green;'>✅ Application Laravel chargée avec succès</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erreur lors du chargement de Laravel: " . $e->getMessage() . "</p>";
        }
    }
    
} else {
    echo "<p style='color: red;'>❌ Configuration impossible - fichiers manquants</p>";
}

// 6. Instructions finales
echo "<h2>6. Instructions finales</h2>";

if (empty($missingDirs) && empty($missingFiles)) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3 style='color: #155724;'>🎉 Configuration réussie!</h3>";
    echo "<p>Votre application Laravel est correctement configurée pour InfinityFree.</p>";
    echo "<p><strong>Prochaines étapes:</strong></p>";
    echo "<ol>";
    echo "<li>Supprimez ce fichier (setup-infinityfree.php) pour des raisons de sécurité</li>";
    echo "<li>Testez votre application: <a href='public_html/index.php'>Accéder à l'application</a></li>";
    echo "<li>Vérifiez les fonctionnalités COBIT</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3 style='color: #721c24;'>⚠️ Configuration incomplète</h3>";
    echo "<p>Veuillez corriger les problèmes suivants:</p>";
    echo "<ul>";
    foreach ($missingDirs as $dir) {
        echo "<li>Uploadez le dossier: $dir</li>";
    }
    foreach ($missingFiles as $file) {
        echo "<li>Uploadez le fichier: $file</li>";
    }
    echo "</ul>";
    echo "</div>";
}

// 7. Informations système
echo "<h2>7. Informations système</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><td><strong>PHP Version</strong></td><td>" . phpversion() . "</td></tr>";
echo "<tr><td><strong>Server Software</strong></td><td>" . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</td></tr>";
echo "<tr><td><strong>Document Root</strong></td><td>" . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</td></tr>";
echo "<tr><td><strong>Current Directory</strong></td><td>" . getcwd() . "</td></tr>";
echo "<tr><td><strong>Memory Limit</strong></td><td>" . ini_get('memory_limit') . "</td></tr>";
echo "<tr><td><strong>Max Execution Time</strong></td><td>" . ini_get('max_execution_time') . " seconds</td></tr>";
echo "</table>";

echo "<hr>";
echo "<p><small>Script de configuration automatique pour InfinityFree - " . date('Y-m-d H:i:s') . "</small></p>";
?>

<?php
/**
 * Script de Préparation pour Déploiement sur Hébergement Mutualisé
 * 
 * Ce script prépare automatiquement votre projet Laravel pour le déploiement
 * sur un hébergement mutualisé sans accès SSH.
 */

echo "🚀 PRÉPARATION DU DÉPLOIEMENT LARAVEL\n";
echo "=====================================\n\n";

$baseDir = __DIR__;
$deploymentDir = $baseDir . '/deployment_ready';

// Créer le dossier de déploiement
if (!is_dir($deploymentDir)) {
    mkdir($deploymentDir, 0755, true);
    echo "✅ Dossier de déploiement créé: $deploymentDir\n";
}

// Fonction pour copier récursivement
function copyDirectory($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copyDirectory($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

// Dossiers à copier
$foldersToCopy = [
    'app',
    'bootstrap',
    'config',
    'database',
    'resources',
    'routes',
    'storage',
    'vendor'
];

echo "📁 Copie des dossiers essentiels...\n";
foreach ($foldersToCopy as $folder) {
    if (is_dir($baseDir . '/' . $folder)) {
        copyDirectory($baseDir . '/' . $folder, $deploymentDir . '/' . $folder);
        echo "   ✅ $folder\n";
    } else {
        echo "   ⚠️  $folder (non trouvé)\n";
    }
}

// Fichiers à copier
$filesToCopy = [
    '.env',
    'composer.json',
    'composer.lock',
    'artisan'
];

echo "\n📄 Copie des fichiers essentiels...\n";
foreach ($filesToCopy as $file) {
    if (file_exists($baseDir . '/' . $file)) {
        copy($baseDir . '/' . $file, $deploymentDir . '/' . $file);
        echo "   ✅ $file\n";
    } else {
        echo "   ⚠️  $file (non trouvé)\n";
    }
}

// Copier les assets du dossier public
echo "\n🎨 Copie des assets publics...\n";
$publicAssets = ['css', 'js', 'images', 'fonts', 'favicon.ico'];
foreach ($publicAssets as $asset) {
    $srcPath = $baseDir . '/public/' . $asset;
    $dstPath = $deploymentDir . '/' . $asset;
    
    if (is_dir($srcPath)) {
        copyDirectory($srcPath, $dstPath);
        echo "   ✅ $asset/ (dossier)\n";
    } elseif (file_exists($srcPath)) {
        copy($srcPath, $dstPath);
        echo "   ✅ $asset (fichier)\n";
    }
}

// Copier le fichier index.php de production
if (file_exists($baseDir . '/index_production.php')) {
    copy($baseDir . '/index_production.php', $deploymentDir . '/index.php');
    echo "   ✅ index.php (version production)\n";
}

// Copier le fichier .htaccess de production
if (file_exists($baseDir . '/.htaccess_production')) {
    copy($baseDir . '/.htaccess_production', $deploymentDir . '/.htaccess');
    echo "   ✅ .htaccess (version production)\n";
}

// Créer les dossiers de cache nécessaires
$cacheDirs = [
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

echo "\n📂 Création des dossiers de cache...\n";
foreach ($cacheDirs as $dir) {
    $fullPath = $deploymentDir . '/' . $dir;
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0755, true);
        echo "   ✅ $dir\n";
    }
}

// Créer un fichier .gitkeep pour les dossiers vides
$gitkeepDirs = [
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs'
];

foreach ($gitkeepDirs as $dir) {
    $gitkeepPath = $deploymentDir . '/' . $dir . '/.gitkeep';
    file_put_contents($gitkeepPath, '');
}

echo "\n🎯 PRÉPARATION TERMINÉE !\n";
echo "========================\n\n";
echo "📁 Votre projet est prêt dans: $deploymentDir\n\n";
echo "📋 PROCHAINES ÉTAPES:\n";
echo "1. Modifiez le fichier .env avec vos vraies informations de base de données\n";
echo "2. Uploadez tout le contenu du dossier 'deployment_ready' vers votre hébergement\n";
echo "3. Assurez-vous que les permissions sont correctes (755 pour les dossiers, 644 pour les fichiers)\n";
echo "4. Testez votre site !\n\n";
echo "⚠️  N'OUBLIEZ PAS:\n";
echo "- Remplacer les informations de base de données dans .env\n";
echo "- Vérifier que le dossier vendor/ est bien uploadé\n";
echo "- Tester toutes les fonctionnalités importantes\n\n";
?>

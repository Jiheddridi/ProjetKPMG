<?php
/**
 * Laravel Application Entry Point - Optimisé pour Hébergement Mutualisé
 * 
 * Ce fichier doit être placé à la racine de votre hébergement (public_html/)
 * et renommé en index.php
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

// Définir le début de l'application
define('LARAVEL_START', microtime(true));

// Déterminer le chemin de base de l'application
// Ajustez ce chemin selon votre structure d'hébergement
$basePath = __DIR__;

// Vérifier si nous sommes dans un sous-dossier
if (basename(__DIR__) === 'public_html' || basename(__DIR__) === 'www' || basename(__DIR__) === 'htdocs') {
    // Structure typique d'hébergement mutualisé
    $basePath = __DIR__;
} else {
    // Structure alternative
    $basePath = dirname(__DIR__);
}

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
*/

if (file_exists($basePath.'/storage/framework/maintenance.php')) {
    require $basePath.'/storage/framework/maintenance.php';
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/

require $basePath.'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/

$app = require_once $basePath.'/bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Handle The Request
|--------------------------------------------------------------------------
*/

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);

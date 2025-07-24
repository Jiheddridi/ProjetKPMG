<?php
/**
 * Point d'entrée principal pour InfinityFree
 * Ce fichier doit être placé à la racine (htdocs/)
 */

// Vérifier si on est sur InfinityFree
$isInfinityFree = strpos($_SERVER['HTTP_HOST'], 'infinityfreeapp.com') !== false;

if ($isInfinityFree) {
    // Redirection permanente vers public_html
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];

    // Si on n'est pas déjà dans public_html, rediriger
    if (strpos($uri, '/public_html/') === false) {
        $newUrl = $protocol . '://' . $host . '/public_html/';
        header('Location: ' . $newUrl, true, 301);
        exit;
    }
}

// Sinon, redirection simple
header('Location: public_html/index.php');
exit;

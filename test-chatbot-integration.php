<?php
/**
 * Script de test pour l'intégration du chatbot COBIT 2019 dans Laravel
 * Vérifie que tous les composants sont en place et fonctionnels
 */

echo "🧪 Test d'intégration du Chatbot COBIT 2019 dans Laravel\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// Vérifications des fichiers
$checks = [
    'Contrôleur ChatbotController' => 'app/Http/Controllers/ChatbotController.php',
    'CSS du chatbot' => 'public/css/chatbot.css',
    'JavaScript du chatbot' => 'public/js/chatbot.js',
    'Composant Blade' => 'resources/views/components/chatbot.blade.php',
    'Vue home modifiée' => 'resources/views/cobit/home.blade.php',
    'Routes web' => 'routes/web.php'
];

$allGood = true;

foreach ($checks as $name => $file) {
    if (file_exists($file)) {
        echo "✅ $name : $file\n";
    } else {
        echo "❌ $name : $file (MANQUANT)\n";
        $allGood = false;
    }
}

echo "\n" . str_repeat("-", 60) . "\n";

// Vérification du contenu des routes
echo "🔍 Vérification des routes...\n";
$routesContent = file_get_contents('routes/web.php');
if (strpos($routesContent, 'ChatbotController') !== false) {
    echo "✅ Routes chatbot présentes\n";
} else {
    echo "❌ Routes chatbot manquantes\n";
    $allGood = false;
}

// Vérification de l'intégration dans home.blade.php
echo "🔍 Vérification de l'intégration dans home.blade.php...\n";
$homeContent = file_get_contents('resources/views/cobit/home.blade.php');
if (strpos($homeContent, 'components.chatbot') !== false) {
    echo "✅ Composant chatbot intégré dans home.blade.php\n";
} else {
    echo "❌ Composant chatbot non intégré dans home.blade.php\n";
    $allGood = false;
}

echo "\n" . str_repeat("-", 60) . "\n";

// Instructions de test
echo "📋 Instructions pour tester l'intégration :\n\n";

echo "1. 🚀 Démarrer le chatbot FastAPI :\n";
echo "   cd \"Documents\\augment-projects\\chatbot cobite\"\n";
echo "   python main.py\n\n";

echo "2. 🌐 Démarrer le serveur Laravel :\n";
echo "   php artisan serve\n\n";

echo "3. 🔗 Ouvrir la page d'accueil :\n";
echo "   http://127.0.0.1:8000/cobit/home\n\n";

echo "4. 🤖 Vérifier le chatbot :\n";
echo "   - Le bouton de chat doit apparaître en bas à droite\n";
echo "   - Cliquer dessus doit ouvrir le widget\n";
echo "   - Tester une question : \"Qu'est-ce que COBIT ?\"\n\n";

echo "5. 🔧 Tests API directs :\n";
echo "   - Health check : http://127.0.0.1:8000/cobit/chatbot/health\n";
echo "   - Suggestions : http://127.0.0.1:8000/cobit/chatbot/suggestions\n\n";

// Résumé final
echo str_repeat("=", 60) . "\n";
if ($allGood) {
    echo "🎉 INTÉGRATION RÉUSSIE !\n";
    echo "Tous les fichiers sont en place. Vous pouvez maintenant tester le chatbot.\n";
} else {
    echo "⚠️  INTÉGRATION INCOMPLÈTE\n";
    echo "Certains fichiers sont manquants. Veuillez vérifier l'installation.\n";
}

echo "\n📚 Fonctionnalités du chatbot intégré :\n";
echo "- Widget flottant moderne avec design KPMG\n";
echo "- Intégration transparente dans la page home\n";
echo "- API proxy Laravel vers FastAPI\n";
echo "- Suggestions de questions prédéfinies\n";
echo "- Historique des conversations\n";
echo "- Responsive design\n";
echo "- Gestion d'erreurs robuste\n";

echo "\n🔗 URLs importantes :\n";
echo "- Page home : http://127.0.0.1:8000/cobit/home\n";
echo "- API chatbot : http://127.0.0.1:8001\n";
echo "- Health check : http://127.0.0.1:8000/cobit/chatbot/health\n";

echo "\n" . str_repeat("=", 60) . "\n";
?>

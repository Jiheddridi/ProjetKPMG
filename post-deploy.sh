#!/bin/bash

# COBIT 2019 - Script Post-Déploiement
# À exécuter dans la console Render après le premier déploiement

echo "🚀 COBIT 2019 - Configuration Post-Déploiement"
echo "=============================================="

# Vérifier l'environnement
echo "📋 Vérification de l'environnement..."
php artisan about

# Exécuter les migrations
echo "🗄️ Exécution des migrations..."
php artisan migrate --force

# Vérifier les tables
echo "📊 Vérification des tables..."
php artisan tinker --execute="
echo 'Tables créées: ';
\$tables = DB::select('SELECT table_name FROM information_schema.tables WHERE table_schema = \'public\'');
foreach(\$tables as \$table) {
    echo '- ' . \$table->table_name . PHP_EOL;
}
"

# Exécuter les seeders
echo "🌱 Insertion des données COBIT..."
php artisan db:seed --class=DesignFactorSeeder

echo "👤 Création de l'utilisateur admin..."
php artisan db:seed --class=AdminUserSeeder

# Vérifier les données
echo "🔍 Vérification des données..."
php artisan tinker --execute="
echo 'Design Factors: ' . App\Models\DesignFactor::count() . PHP_EOL;
echo 'Utilisateurs: ' . App\Models\User::count() . PHP_EOL;
echo 'Évaluations: ' . App\Models\Evaluation::count() . PHP_EOL;
"

# Optimiser le cache
echo "⚡ Optimisation du cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Créer le lien de stockage
echo "🔗 Création du lien de stockage..."
php artisan storage:link

# Test de santé
echo "🏥 Test de santé de l'application..."
curl -s http://localhost/health || echo "Health check local non disponible (normal en production)"

echo ""
echo "✅ Configuration terminée !"
echo ""
echo "🎯 URLs de test :"
echo "- Health Check: https://cobit-2019-app.onrender.com/health"
echo "- Application: https://cobit-2019-app.onrender.com/"
echo "- Login: https://cobit-2019-app.onrender.com/login"
echo ""
echo "👤 Compte admin :"
echo "- Email: admin@cobit.local"
echo "- Mot de passe: password123"
echo ""
echo "🎉 COBIT 2019 est prêt à l'emploi !"

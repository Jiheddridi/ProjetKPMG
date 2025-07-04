#!/bin/bash

# Configuration pour Railway
export PORT=${PORT:-8080}

# Configurer Apache pour le port dynamique
echo "Listen $PORT" > /etc/apache2/ports.conf
sed -i "s/\${PORT}/$PORT/g" /etc/apache2/sites-available/000-default.conf

# Attendre que la base de données soit prête (si PostgreSQL)
if [ ! -z "$PGHOST" ]; then
    echo "Attente de PostgreSQL..."
    while ! php -r "
    try {
        \$pdo = new PDO('pgsql:host=$PGHOST;port=$PGPORT;dbname=$PGDATABASE', '$PGUSER', '$PGPASSWORD');
        echo 'Connected to PostgreSQL successfully';
        exit(0);
    } catch (PDOException \$e) {
        exit(1);
    }
    "; do
      echo "En attente de PostgreSQL..."
      sleep 2
    done
    echo "PostgreSQL prêt!"
elif [ ! -z "$DB_HOST" ]; then
    echo "Attente de la base de données..."
    while ! php -r "
    try {
        \$pdo = new PDO('mysql:host=$DB_HOST;port=${DB_PORT:-3306}', '$DB_USERNAME', '$DB_PASSWORD');
        echo 'Connected to MySQL successfully';
        exit(0);
    } catch (PDOException \$e) {
        exit(1);
    }
    "; do
      echo "En attente de MySQL..."
      sleep 2
    done
    echo "MySQL prêt!"
fi

# Copier le fichier .env approprié
if [ ! -z "$RAILWAY_ENVIRONMENT" ]; then
    echo "Environnement Railway détecté, utilisation de .env.railway"
    if [ -f .env.railway ]; then
        cp .env.railway .env
    else
        echo "Fichier .env.railway non trouvé, utilisation de .env.example"
        cp .env.example .env
    fi
elif [ ! -f .env ]; then
    echo "Copie du fichier .env.example vers .env"
    cp .env.example .env
fi

# Générer la clé d'application si elle n'existe pas
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Génération de la clé d'application..."
    php artisan key:generate
fi

# Exécuter les migrations
echo "Exécution des migrations..."
php artisan migrate --force

# Nettoyer et optimiser le cache
echo "Optimisation du cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Créer les liens symboliques pour le stockage
php artisan storage:link

# Démarrer Apache
echo "Démarrage d'Apache..."
apache2-foreground

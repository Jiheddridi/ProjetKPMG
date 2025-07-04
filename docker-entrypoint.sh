#!/bin/bash

# Universal Docker entrypoint for multiple platforms
export PORT=${PORT:-8080}

# Configure Apache for dynamic port
echo "Listen 80" > /etc/apache2/ports.conf
echo "Listen $PORT" >> /etc/apache2/ports.conf

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

# Copy appropriate .env file based on platform
if [ ! -z "$RENDER" ]; then
    echo "Render environment detected"
    if [ -f .env.render ]; then
        cp .env.render .env
    else
        cp .env.example .env
    fi
elif [ ! -z "$DYNO" ]; then
    echo "Heroku environment detected"
    # Heroku uses environment variables directly
    cp .env.example .env
elif [ ! -f .env ]; then
    echo "Using .env.example"
    cp .env.example .env
fi

# Générer la clé d'application si elle n'existe pas
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Génération de la clé d'application..."
    php artisan key:generate
fi

# Exécuter les migrations (avec gestion d'erreur)
echo "Exécution des migrations..."
php artisan migrate --force || echo "Migrations échouées, continuons..."

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

#!/bin/bash

echo "🚀 Building COBIT Laravel for Netlify..."

# Copier le fichier .env pour Netlify
cp .env.netlify .env

# Installer les dépendances PHP
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Créer la base de données SQLite
echo "🗄️ Setting up SQLite database..."
touch /tmp/database.sqlite
php artisan migrate --force

# Optimiser Laravel pour la production
echo "⚡ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Installer et compiler les assets
echo "🎨 Building frontend assets..."
npm install
npm run build

echo "✅ Build complete!"

#!/bin/bash

# COBIT 2019 - Script de Déploiement Multi-Plateforme
# Usage: ./deploy.sh [render|heroku|vercel]

PLATFORM=${1:-render}

echo "🚀 Déploiement COBIT 2019 sur $PLATFORM"

case $PLATFORM in
  "render")
    echo "📦 Préparation pour Render.com..."
    
    # Copier le fichier d'environnement approprié
    if [ -f .env.render ]; then
      echo "✅ Configuration Render détectée"
    else
      echo "❌ Fichier .env.render manquant"
      exit 1
    fi
    
    # Vérifier les fichiers nécessaires
    if [ ! -f render.yaml ]; then
      echo "❌ Fichier render.yaml manquant"
      exit 1
    fi
    
    echo "✅ Prêt pour Render.com"
    echo "👉 Allez sur https://render.com et connectez votre repository GitHub"
    ;;
    
  "heroku")
    echo "📦 Préparation pour Heroku..."
    
    # Vérifier Heroku CLI
    if ! command -v heroku &> /dev/null; then
      echo "❌ Heroku CLI non installé"
      echo "👉 Installez avec: winget install Heroku.CLI"
      exit 1
    fi
    
    # Login Heroku
    echo "🔐 Connexion à Heroku..."
    heroku login
    
    # Créer l'application
    echo "🏗️ Création de l'application Heroku..."
    heroku create cobit-2019-$(date +%s) || echo "Application existe déjà"
    
    # Ajouter les add-ons
    echo "🗄️ Ajout de PostgreSQL..."
    heroku addons:create heroku-postgresql:mini || echo "PostgreSQL existe déjà"
    
    echo "🔄 Ajout de Redis..."
    heroku addons:create heroku-redis:mini || echo "Redis existe déjà"
    
    # Configuration des variables
    echo "⚙️ Configuration des variables d'environnement..."
    heroku config:set APP_NAME="COBIT 2019"
    heroku config:set APP_ENV=production
    heroku config:set APP_DEBUG=false
    heroku config:set APP_KEY=base64:efPkkxsj/MkIPwYS2nHUlMpvIKBz0YLGbVHLIOV7Ono=
    
    # Déploiement
    echo "🚀 Déploiement sur Heroku..."
    git push heroku main
    
    # Migrations
    echo "🗄️ Exécution des migrations..."
    heroku run php artisan migrate --force
    heroku run php artisan db:seed --class=DesignFactorSeeder
    heroku run php artisan db:seed --class=AdminUserSeeder
    
    echo "✅ Déploiement Heroku terminé !"
    heroku open
    ;;
    
  "vercel")
    echo "📦 Préparation pour Vercel..."
    
    # Vérifier Vercel CLI
    if ! command -v vercel &> /dev/null; then
      echo "📥 Installation de Vercel CLI..."
      npm install -g vercel
    fi
    
    # Déploiement
    echo "🚀 Déploiement sur Vercel..."
    vercel --prod
    
    echo "✅ Déploiement Vercel terminé !"
    ;;
    
  *)
    echo "❌ Plateforme non supportée: $PLATFORM"
    echo "👉 Utilisez: render, heroku, ou vercel"
    exit 1
    ;;
esac

echo ""
echo "🎉 Déploiement terminé !"
echo "🔗 Testez votre application avec le health check: /health"
echo "👤 Connexion admin: admin@cobit.local / password123"

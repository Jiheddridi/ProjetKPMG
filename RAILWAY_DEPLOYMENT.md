# 🚂 Déploiement COBIT 2019 sur Railway.app

Ce guide vous explique comment déployer votre application COBIT 2019 Laravel sur Railway.app.

## 📋 Prérequis

- Compte GitHub avec le repository COBIT 2019
- Compte Railway.app (gratuit)
- Repository GitHub public ou privé

## 🚀 Étapes de Déploiement

### 1. Connexion à Railway

1. Allez sur [railway.app](https://railway.app)
2. Connectez-vous avec votre compte GitHub
3. Cliquez sur "New Project"

### 2. Sélection du Repository

1. Choisissez "Deploy from GitHub repo"
2. Sélectionnez votre repository `Cobit-2019`
3. Railway détectera automatiquement le Dockerfile

### 3. Configuration des Services

Railway créera automatiquement :
- **App Service** : Votre application Laravel
- **PostgreSQL** : Base de données (ajoutez-la manuellement)
- **Redis** : Cache et sessions (ajoutez-la manuellement)

#### Ajouter PostgreSQL :
1. Cliquez sur "+ New Service"
2. Sélectionnez "PostgreSQL"
3. Railway créera automatiquement les variables d'environnement

#### Ajouter Redis :
1. Cliquez sur "+ New Service"
2. Sélectionnez "Redis"
3. Railway créera automatiquement les variables d'environnement

### 4. Variables d'Environnement

Dans les paramètres de votre service App, ajoutez :

```bash
# Application
APP_NAME="COBIT 2019"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_URL=https://your-app.railway.app

# Base de données (automatique avec PostgreSQL)
DB_CONNECTION=pgsql
# Les variables PGHOST, PGPORT, etc. sont automatiques

# Cache et sessions (automatique avec Redis)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
# Les variables REDISHOST, REDISPORT, etc. sont automatiques

# Mail (configurez avec votre service)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 5. Génération de la Clé d'Application

1. Générez une clé Laravel localement :
```bash
php artisan key:generate --show
```

2. Copiez la clé générée dans la variable `APP_KEY`

### 6. Configuration du Domaine

1. Dans les paramètres du service App
2. Section "Networking"
3. Configurez votre domaine personnalisé ou utilisez le domaine Railway

### 7. Déploiement

1. Railway déploiera automatiquement à chaque push sur `main`
2. Surveillez les logs de déploiement
3. Vérifiez le health check : `https://your-app.railway.app/health`

## 🔧 Commandes Utiles

### Accès aux Logs
```bash
# Dans le dashboard Railway, onglet "Logs"
```

### Exécution de Commandes
```bash
# Dans le dashboard Railway, onglet "Console"
php artisan migrate --force
php artisan db:seed
php artisan config:cache
```

### Variables d'Environnement Automatiques

Railway fournit automatiquement :

**PostgreSQL :**
- `PGHOST`
- `PGPORT`
- `PGDATABASE`
- `PGUSER`
- `PGPASSWORD`

**Redis :**
- `REDISHOST`
- `REDISPORT`
- `REDISPASSWORD`

**Railway :**
- `PORT` (port dynamique)
- `RAILWAY_ENVIRONMENT`

## 🎯 Points de Vérification

### ✅ Checklist de Déploiement

- [ ] Repository GitHub connecté
- [ ] PostgreSQL service ajouté
- [ ] Redis service ajouté
- [ ] Variables d'environnement configurées
- [ ] APP_KEY générée et configurée
- [ ] Domaine configuré
- [ ] Health check fonctionnel (`/health`)
- [ ] Migrations exécutées
- [ ] Seeders exécutés (si nécessaire)

### 🔍 Tests Post-Déploiement

1. **Health Check** : `GET /health`
2. **Page d'accueil** : `GET /`
3. **Login** : `GET /login`
4. **API Status** : Vérifiez les endpoints COBIT

## 🚨 Dépannage

### Problèmes Courants

**1. Erreur de Base de Données**
- Vérifiez que PostgreSQL est bien connecté
- Vérifiez les variables d'environnement PG*

**2. Erreur de Cache**
- Vérifiez que Redis est bien connecté
- Vérifiez les variables d'environnement REDIS*

**3. Erreur 500**
- Vérifiez les logs dans Railway
- Vérifiez que APP_KEY est configurée
- Vérifiez les permissions de fichiers

**4. Assets non chargés**
- Vérifiez que `npm run build` s'exécute correctement
- Vérifiez les chemins des assets

### Logs Utiles

```bash
# Logs d'application
tail -f storage/logs/laravel.log

# Logs Apache
tail -f /var/log/apache2/error.log
```

## 📞 Support

- Documentation Railway : [docs.railway.app](https://docs.railway.app)
- Community Discord : [discord.gg/railway](https://discord.gg/railway)
- GitHub Issues : Créez une issue sur votre repository

## 🎉 Félicitations !

Votre application COBIT 2019 est maintenant déployée sur Railway ! 🚀

URL de production : `https://your-app.railway.app`

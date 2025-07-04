# 🚀 Guide de Déploiement COBIT 2019 - Plateformes Gratuites

Ce guide vous explique comment déployer votre application COBIT 2019 sur différentes plateformes gratuites.

## 🌟 Plateformes Recommandées

### 1. 🎯 Render.com (RECOMMANDÉ)
- ✅ **Gratuit** : 750h/mois
- ✅ **PostgreSQL gratuit**
- ✅ **Redis gratuit**
- ✅ **SSL automatique**
- ✅ **Support Docker complet**

### 2. 🔥 Heroku
- ✅ **Gratuit** avec limitations
- ✅ **PostgreSQL gratuit**
- ✅ **Add-ons disponibles**

### 3. ⚡ Vercel
- ✅ **Gratuit** pour projets personnels
- ✅ **Déploiement Git automatique**
- ⚠️ **Serverless** (nécessite adaptations)

### 4. 🌐 Netlify + PlanetScale
- ✅ **Frontend gratuit**
- ✅ **Base de données MySQL gratuite**

---

## 🎯 Option 1: Render.com (Recommandé)

### Étapes de Déploiement

1. **Créer un compte** sur [render.com](https://render.com)

2. **Connecter GitHub**
   - Cliquez "New +"
   - Sélectionnez "Web Service"
   - Connectez votre repository `Jiheddridi/Cobit-2019`

3. **Configuration du Service**
   ```
   Name: cobit-2019-app
   Environment: Docker
   Branch: main
   Dockerfile Path: ./Dockerfile
   ```

4. **Ajouter PostgreSQL**
   - Cliquez "New +"
   - Sélectionnez "PostgreSQL"
   - Name: `cobit-postgres`

5. **Ajouter Redis**
   - Cliquez "New +"
   - Sélectionnez "Redis"
   - Name: `cobit-redis`

6. **Variables d'Environnement**
   ```bash
   APP_NAME=COBIT 2019
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:efPkkxsj/MkIPwYS2nHUlMpvIKBz0YLGbVHLIOV7Ono=
   APP_URL=https://votre-app.onrender.com
   
   # Database (automatique avec PostgreSQL)
   DB_CONNECTION=pgsql
   
   # Cache (automatique avec Redis)
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   ```

7. **Déployer**
   - Render déploiera automatiquement
   - URL: `https://votre-app.onrender.com`

### Post-Déploiement Render
```bash
# Dans la console Render
php artisan migrate --force
php artisan db:seed --class=DesignFactorSeeder
php artisan db:seed --class=AdminUserSeeder
```

---

## 🔥 Option 2: Heroku

### Étapes de Déploiement

1. **Installer Heroku CLI**
   ```bash
   # Windows
   winget install Heroku.CLI
   ```

2. **Login et Création**
   ```bash
   heroku login
   heroku create cobit-2019-app
   ```

3. **Ajouter Add-ons**
   ```bash
   heroku addons:create heroku-postgresql:mini
   heroku addons:create heroku-redis:mini
   ```

4. **Configuration**
   ```bash
   heroku config:set APP_NAME="COBIT 2019"
   heroku config:set APP_ENV=production
   heroku config:set APP_DEBUG=false
   heroku config:set APP_KEY=base64:efPkkxsj/MkIPwYS2nHUlMpvIKBz0YLGbVHLIOV7Ono=
   ```

5. **Déployer**
   ```bash
   git push heroku main
   ```

### Post-Déploiement Heroku
```bash
heroku run php artisan migrate --force
heroku run php artisan db:seed --class=DesignFactorSeeder
```

---

## ⚡ Option 3: Vercel (Serverless)

### Préparation pour Vercel

1. **Créer vercel.json**
   ```json
   {
     "version": 2,
     "builds": [
       {
         "src": "public/index.php",
         "use": "vercel-php@0.6.0"
       }
     ],
     "routes": [
       {
         "src": "/(.*)",
         "dest": "public/index.php"
       }
     ],
     "env": {
       "APP_ENV": "production",
       "APP_DEBUG": "false"
     }
   }
   ```

2. **Déployer**
   ```bash
   npm i -g vercel
   vercel --prod
   ```

---

## 🌐 Option 4: Netlify + PlanetScale

### Étapes

1. **PlanetScale pour la DB**
   - Créer compte sur [planetscale.com](https://planetscale.com)
   - Créer base de données MySQL

2. **Netlify pour l'app**
   - Connecter GitHub repository
   - Configurer build settings

---

## 🔧 Commandes Utiles Post-Déploiement

### Migrations et Seeders
```bash
php artisan migrate --force
php artisan db:seed --class=DesignFactorSeeder
php artisan db:seed --class=AdminUserSeeder
```

### Optimisation
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Vérification
```bash
# Health check
curl https://votre-app.com/health

# Test login
curl https://votre-app.com/login
```

---

## 📊 Comparaison des Plateformes

| Plateforme | Gratuit | DB | Cache | SSL | Docker | Facilité |
|------------|---------|----|----|-----|--------|----------|
| **Render** | ✅ 750h | PostgreSQL | Redis | ✅ | ✅ | ⭐⭐⭐⭐⭐ |
| **Heroku** | ✅ Limité | PostgreSQL | Redis | ✅ | ✅ | ⭐⭐⭐⭐ |
| **Vercel** | ✅ | Externe | Externe | ✅ | ❌ | ⭐⭐⭐ |
| **Netlify** | ✅ | Externe | Externe | ✅ | ❌ | ⭐⭐⭐ |

## 🎯 Recommandation

**Render.com** est la meilleure option car :
- Support Docker complet
- PostgreSQL et Redis gratuits
- Configuration simple
- Performance excellente
- SSL automatique

---

## 🆘 Support

- **Render**: [render.com/docs](https://render.com/docs)
- **Heroku**: [devcenter.heroku.com](https://devcenter.heroku.com)
- **Vercel**: [vercel.com/docs](https://vercel.com/docs)

## 🎉 Félicitations !

Votre application COBIT 2019 est maintenant déployée gratuitement ! 🚀

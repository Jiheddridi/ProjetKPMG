# 🚀 Guide Complet de Déploiement Laravel sur Hébergement Mutualisé

## 📋 Prérequis

- ✅ Projet Laravel fonctionnel en local
- ✅ Compte d'hébergement mutualisé (AwardSpace, 000webhost, etc.)
- ✅ Accès FTP ou gestionnaire de fichiers
- ✅ Base de données MySQL disponible

---

## 🔧 Étape 1 : Préparation Locale

### A. Installation des dépendances (OBLIGATOIRE)
```bash
# Dans votre dossier Laravel local
composer install --optimize-autoloader --no-dev
```

### B. Configuration pour la production
```bash
# Optimiser les caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### C. Exécuter le script de préparation
```bash
php prepare_deployment.php
```

---

## 📁 Étape 2 : Structure des Fichiers

Après exécution du script, vous aurez cette structure dans `deployment_ready/` :

```
deployment_ready/
├── app/                    # Code de l'application
├── bootstrap/              # Fichiers de démarrage Laravel
├── config/                 # Configuration
├── database/               # Migrations et seeders
├── resources/              # Vues, assets sources
├── routes/                 # Définition des routes
├── storage/                # Stockage (logs, cache, sessions)
├── vendor/                 # Dépendances Composer (IMPORTANT!)
├── css/                    # Assets CSS
├── js/                     # Assets JavaScript
├── images/                 # Images
├── .env                    # Configuration environnement
├── .htaccess              # Configuration Apache
├── index.php              # Point d'entrée (adapté)
├── composer.json          # Dépendances
└── artisan                # CLI Laravel
```

---

## ⚙️ Étape 3 : Configuration de la Base de Données

### A. Créer la base de données
1. Connectez-vous à votre panneau d'hébergement
2. Créez une nouvelle base de données MySQL
3. Notez les informations :
   - Nom de la base
   - Nom d'utilisateur
   - Mot de passe
   - Serveur (généralement `localhost`)

### B. Modifier le fichier .env
```env
# Remplacez ces valeurs par vos vraies informations
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=votre_nom_base_donnees
DB_USERNAME=votre_username
DB_PASSWORD=votre_mot_de_passe

# URL de votre site
APP_URL=https://votre-domaine.com
```

---

## 📤 Étape 4 : Upload des Fichiers

### A. Via FTP (recommandé)
1. Connectez-vous à votre FTP
2. Naviguez vers le dossier racine (`public_html/`, `www/`, ou `htdocs/`)
3. Uploadez **TOUT** le contenu du dossier `deployment_ready/`
4. ⚠️ **IMPORTANT** : Assurez-vous que le dossier `vendor/` est bien uploadé !

### B. Via Gestionnaire de Fichiers
1. Compressez le dossier `deployment_ready/` en ZIP
2. Uploadez le fichier ZIP
3. Décompressez directement sur le serveur
4. Déplacez le contenu vers la racine si nécessaire

---

## 🔐 Étape 5 : Permissions des Fichiers

### Permissions recommandées :
- **Dossiers** : `755`
- **Fichiers** : `644`
- **Dossiers critiques** : `755` (storage/, bootstrap/cache/)

### Via FTP :
```
storage/                    → 755 (récursif)
bootstrap/cache/            → 755 (récursif)
.env                        → 644
index.php                   → 644
.htaccess                   → 644
```

---

## 🗄️ Étape 6 : Import de la Base de Données

### A. Export depuis votre base locale
```bash
# Si vous avez des données à migrer
mysqldump -u root -p votre_base_locale > backup.sql
```

### B. Import sur le serveur
1. Accédez à phpMyAdmin de votre hébergeur
2. Sélectionnez votre base de données
3. Importez le fichier SQL
4. Ou exécutez vos migrations si possible

---

## ✅ Étape 7 : Tests et Vérifications

### A. Tests de base
1. **Accès au site** : `https://votre-domaine.com`
2. **Page d'accueil** : Doit s'afficher sans erreur
3. **Connexion BDD** : Testez une page qui utilise la base
4. **Assets** : Vérifiez que CSS/JS se chargent

### B. Tests spécifiques COBIT
1. **Page COBIT** : `/cobit/home`
2. **Authentification** : Connexion utilisateur
3. **Évaluations** : Création d'une évaluation test
4. **Graphiques** : Vérifiez les charts interactifs

---

## 🚨 Résolution des Problèmes Courants

### Erreur 500 - Internal Server Error
```
Causes possibles :
- Permissions incorrectes sur storage/
- Fichier .env mal configuré
- Dossier vendor/ manquant
- Erreur dans .htaccess

Solutions :
1. Vérifiez les permissions (755/644)
2. Vérifiez la configuration .env
3. Re-uploadez le dossier vendor/
4. Testez avec un .htaccess minimal
```

### Erreur de base de données
```
Causes :
- Informations de connexion incorrectes
- Base de données non créée
- Utilisateur sans permissions

Solutions :
1. Vérifiez les informations dans .env
2. Testez la connexion via phpMyAdmin
3. Vérifiez les permissions utilisateur
```

### Assets non chargés (CSS/JS)
```
Causes :
- Chemins incorrects
- Fichiers non uploadés
- Problème de cache

Solutions :
1. Vérifiez que les dossiers css/, js/ sont uploadés
2. Vérifiez les chemins dans les templates
3. Videz le cache du navigateur
```

---

## 📞 Support et Maintenance

### Logs d'erreur
- Consultez `storage/logs/laravel.log`
- Activez temporairement `APP_DEBUG=true` pour déboguer

### Mise à jour
1. Modifiez en local
2. Re-exécutez le script de préparation
3. Re-uploadez les fichiers modifiés

### Sauvegarde
- Sauvegardez régulièrement votre base de données
- Gardez une copie de votre dossier `deployment_ready/`

---

## 🎉 Félicitations !

Si vous avez suivi toutes ces étapes, votre application Laravel COBIT devrait maintenant fonctionner parfaitement sur votre hébergement mutualisé !

**Testez toutes les fonctionnalités importantes avant de considérer le déploiement comme terminé.**

# 🚀 GUIDE DE DÉMARRAGE RAPIDE - PLATEFORME COBIT 2019

## ⚡ Installation en 5 Minutes

### 1. **Prérequis** ✅
```bash
# Vérifiez vos versions
php --version    # PHP 8.1+ requis
composer --version
node --version   # Node.js 16+ requis
mysql --version  # MySQL 5.7+ ou MariaDB 10.3+
```

### 2. **Installation** 📦
```bash
# Cloner et installer
git clone [URL_DU_REPO] cobit-platform
cd cobit-platform
composer install
npm install && npm run build
```

### 3. **Configuration** ⚙️
```bash
# Configuration de base
cp .env.example .env
php artisan key:generate

# Configurez votre base de données dans .env
DB_DATABASE=cobit_platform
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

### 4. **Base de données** 🗄️
```bash
# Créer la base de données
mysql -u root -p
CREATE DATABASE cobit_platform;
exit

# Migrations et données de test
php artisan migrate
php artisan db:seed
```

### 5. **Démarrage** 🎯
```bash
# Lancer le serveur
php artisan serve

# Accédez à http://localhost:8000
```

## 🎯 PREMIÈRE UTILISATION

### Étape 1: Créer un Compte
- Allez sur `http://localhost:8000/register`
- Créez votre compte administrateur
- Connectez-vous

### Étape 2: Première Évaluation
- Cliquez sur **"Commencer l'évaluation"**
- Renseignez votre entreprise :
  - Nom : "Ma Société"
  - Taille : "Moyenne entreprise (100-500 employés)"
  - Contraintes : "Conformité RGPD, Budget limité"

### Étape 3: Utiliser l'Agent IA 🤖
- **Uploadez un document** (PDF/Excel) contenant :
  - Stratégie IT, processus, audits, budgets...
- **Cliquez "Analyser avec l'IA"**
- **Attendez 5-10 secondes** pour l'analyse
- **Vérifiez les résultats** pré-remplis

### Étape 4: Évaluer les Design Factors
- **Parcourez DF1 à DF10** via la navigation
- **Ajustez les valeurs** suggérées par l'IA
- **Sauvegardez** régulièrement vos modifications
- **Utilisez les descriptions** pour vous guider

### Étape 5: Voir les Résultats
- **Canvas final** : Visualisation graphique
- **Graphiques radar** : Comparaison par domaine
- **Recommandations** : Actions prioritaires
- **Export PDF** : Rapport professionnel

## 📊 EXEMPLE DE DOCUMENT POUR L'IA

Créez un fichier `test_strategie.txt` avec ce contenu :

```
STRATÉGIE IT 2024 - MA SOCIÉTÉ

GOUVERNANCE
- Comité de direction IT mensuel
- Supervision par le COMEX
- Politiques IT formalisées

SÉCURITÉ
- Analyse des risques trimestrielle
- Conformité RGPD à 85%
- 3 incidents majeurs en 2024

BUDGET ET RESSOURCES
- Budget IT : 500k€ (15% du CA)
- Équipe IT : 12 personnes
- Investissements : Cloud, sécurité

PROCESSUS
- 80% des processus documentés
- Méthodes agiles sur 60% des projets
- COBIT 2019 en cours d'implémentation

TECHNOLOGIE
- Infrastructure cloud hybride
- Applications métier intégrées
- Modernisation progressive
```

Uploadez ce fichier et voyez l'IA pré-remplir intelligemment vos Design Factors !

## 🎯 DESIGN FACTORS - GUIDE RAPIDE

| DF | Focus | Questions Clés |
|----|-------|----------------|
| **DF1** | Stratégie | Avez-vous une stratégie IT claire ? |
| **DF2** | Objectifs | Les objectifs IT sont-ils mesurables ? |
| **DF3** | Risques | Comment gérez-vous les risques IT ? |
| **DF4** | Enjeux IT | Quels sont vos défis IT prioritaires ? |
| **DF5** | Menaces | Comment protégez-vous contre les cybermenaces ? |
| **DF6** | Conformité | Respectez-vous les réglementations ? |
| **DF7** | Rôle IT | Quel est le positionnement de l'IT ? |
| **DF8** | Sourcing | Comment organisez-vous vos ressources ? |
| **DF9** | Méthodes | Quelles méthodes utilisez-vous ? |
| **DF10** | Taille | Quelle est la complexité de votre organisation ? |

## 🔧 DÉPANNAGE RAPIDE

### Problème : Erreur de base de données
```bash
# Vérifiez la connexion
php artisan tinker
DB::connection()->getPdo();

# Recréez les tables si nécessaire
php artisan migrate:fresh --seed
```

### Problème : Assets non compilés
```bash
# Recompilez les assets
npm run build

# En développement
npm run dev
```

### Problème : Permissions de fichiers
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache

# Windows : Donnez les droits complets au dossier
```

### Problème : Agent IA ne fonctionne pas
- Vérifiez que les fichiers sont < 10MB
- Formats supportés : PDF, Excel (.xlsx, .xls)
- Vérifiez les logs : `storage/logs/laravel.log`

## 📈 OPTIMISATION PERFORMANCE

### Cache Redis (Optionnel)
```bash
# Installation Redis
# Windows : https://redis.io/download
# Linux : sudo apt install redis-server

# Configuration .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### Optimisation Production
```bash
# Cache des configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimisation Composer
composer install --optimize-autoloader --no-dev
```

## 🎯 PROCHAINES ÉTAPES

### 1. **Explorez les Fonctionnalités**
- Testez tous les Design Factors
- Essayez différents types de documents
- Explorez le canvas interactif

### 2. **Personnalisez**
- Modifiez les seuils de maturité
- Adaptez les recommandations
- Personnalisez l'interface

### 3. **Intégrez**
- Connectez à votre LDAP/AD
- Intégrez avec vos outils existants
- Automatisez les rapports

### 4. **Étendez**
- Ajoutez de nouveaux Design Factors
- Créez des templates personnalisés
- Développez des connecteurs

## 📞 AIDE ET SUPPORT

- **Documentation** : Voir `README.md` pour plus de détails
- **Issues** : Rapportez les bugs sur GitHub
- **COBIT 2019** : [Documentation officielle ISACA](https://www.isaca.org/resources/cobit)
- **Laravel** : [Documentation Laravel](https://laravel.com/docs)

## 🎉 FÉLICITATIONS !

Vous avez maintenant une plateforme COBIT 2019 fonctionnelle avec :
- ✅ Agent IA pour l'analyse de documents
- ✅ Évaluation complète des 10 Design Factors
- ✅ Visualisations interactives
- ✅ Rapports professionnels
- ✅ Système multi-utilisateurs

**🚀 Commencez dès maintenant à évaluer et améliorer votre gouvernance IT !**

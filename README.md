# 🚀 Plateforme COBIT 2019 – Évaluation de Gouvernance IT Augmentée par IA

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10-red?style=for-the-badge&logo=laravel" alt="Laravel 10">
  <img src="https://img.shields.io/badge/PHP-8.1+-blue?style=for-the-badge&logo=php" alt="PHP 8.1+">
  <img src="https://img.shields.io/badge/COBIT-2019-green?style=for-the-badge" alt="COBIT 2019">
  <img src="https://img.shields.io/badge/AI-Powered-purple?style=for-the-badge&logo=robot" alt="AI Powered">
</p>

## 🏆 Présentation

Plateforme web complète pour l’évaluation de la gouvernance IT selon **COBIT 2019**, intégrant :
- Un **agent IA expert** (analyse automatique de documents, pré-remplissage intelligent, recommandations personnalisées)
- Un **chatbot COBIT 2019** expert intégré
- Des **visualisations interactives** (canvas, radar, barres, matrices, comparaisons)
- Un système multi-utilisateurs sécurisé
- Des exports PDF professionnels

> **Objectif :** Accélérer, fiabiliser et professionnaliser l’évaluation de maturité IT, quel que soit le contexte (startup, PME, grand groupe, secteur régulé…)

---

## 🎯 Fonctionnalités Clés

### 🔍 Évaluation COBIT 2019
- **10 Design Factors** : Évaluation complète, scoring par paramètre
- **40 Objectifs de gouvernance** : Calcul détaillé, priorisation
- **Calcul de maturité** : Global, par domaine, par objectif
- **Recommandations personnalisées** : Actions prioritaires, roadmap IA

### 🤖 Agent IA Ollama Intégré
- **Analyse automatique** : Upload de documents (PDF, Excel, TXT)
- **Pré-remplissage intelligent** : Paramètres adaptés à votre contexte
- **Personnalisation avancée** : Taille, secteur, contraintes, variabilité garantie
- **Ultra-rapide** : Analyse en 1-2 secondes
- **Fallback automatique** : Toujours un résultat, même en cas d’erreur

### 💬 Chatbot COBIT 2019 Expert
- **Widget web** et **API** : Assistance 24/7 sur tous les concepts COBIT
- **Réponses structurées** : Explications, exemples, conseils pratiques
- **Suggestions intelligentes** : Questions fréquentes, navigation rapide
- **Couverture complète** : 10 DF, 40 objectifs, 7 enablers, 6 principes, maturité, bonnes pratiques

### 📊 Visualisations & Rapports
- **Canvas interactif** : Vue d’ensemble, détails, filtres dynamiques
- **Graphiques radar & barres** : Par domaine, par objectif, par priorité
- **Tableaux de bord** : KPIs, progression, matrices de priorités
- **Comparaison multi-évaluations** : Benchmarking, analyse IA comparative
- **Export PDF** : Rapports professionnels prêts à l’emploi

### 👥 Gestion Multi-Utilisateurs
- **Authentification sécurisée** (Laravel)
- **Profils & permissions**
- **Historique & traçabilité**
- **Collaboration & partage**

---

## 🛠️ Technologies

- **Backend** : Laravel 10, PHP 8.1+, MySQL
- **Frontend** : Blade, Tailwind CSS, Chart.js, JavaScript ES6
- **IA** : Agent Ollama, analyse contextuelle, prompts optimisés
- **Sécurité** : Auth Laravel, CSRF, validation stricte, upload sécurisé

---

## ⚡ Installation Rapide

### Prérequis
- PHP 8.1+
- Composer
- Node.js 16+
- MySQL 5.7+ ou MariaDB 10.3+

### Setup
```bash
# 1. Cloner le projet
 git clone [URL_DU_REPO] cobit-platform
 cd cobit-platform

# 2. Installer les dépendances
 composer install
 npm install && npm run build

# 3. Configurer l’environnement
 cp .env.example .env
 php artisan key:generate
 # Modifier .env pour la base de données

# 4. Préparer la base de données
 php artisan migrate
 php artisan db:seed

# 5. Lancer le serveur
 php artisan serve
# Accédez à http://localhost:8000
```

---

## 🚀 Première Utilisation

1. **Créer un compte** : http://localhost:8000/register
2. **Commencer une évaluation** : Saisir entreprise, taille, contraintes
3. **Utiliser l’Agent IA** : Uploader un document, cliquer "Analyser avec l’IA"
4. **Évaluer les Design Factors** : Ajuster, sauvegarder, parcourir DF1 à DF10
5. **Visualiser les résultats** : Canvas, graphiques, recommandations, export PDF
6. **Comparer plusieurs évaluations** (optionnel)

---

## 🧠 Aperçu de l’IA & du Chatbot

- **Agent IA Ollama** :
  - Analyse contextuelle (secteur, taille, contraintes)
  - Génère des valeurs variables et justifiées pour chaque DF
  - Prend en charge tous types d’organisation (startup, banque, industrie…)
  - 1-2 secondes d’analyse, fallback automatique

- **Chatbot COBIT 2019** :
  - Widget intégré (coin inférieur droit)
  - API REST : `/api/chatbot/query`
  - Réponses expertes sur tous les concepts COBIT
  - Suggestions, historique, recherche, liens directs

---

## 📊 Visualisations Interactives

- **Canvas final** : Vue d’ensemble, filtres Top 3/5/10/Tous, tri, détails par objectif
- **Radar chart** : Comparaison par domaine (EDM, APO, BAI, DSS, MEA)
- **Bar chart** : Scores par domaine, couleurs dynamiques
- **Matrice de priorités** : Objectifs à améliorer en priorité
- **Comparaison multi-évaluations** : Analyse IA comparative, recommandations croisées

---

## 📁 Structure Technique (extrait)

```
cobit-laravel/
├── app/
│   ├── Http/Controllers/         # Logique métier, API, IA, chatbot
│   ├── Models/                   # Évaluations, utilisateurs, historiques
│   └── Services/                 # Analyse IA, intégration Ollama
├── resources/views/cobit/        # Vues, canvas, graphiques, chatbot
├── public/js/                    # Scripts interactifs, IA, graphiques
├── database/migrations/          # Structure BDD
└── routes/web.php                # Routing principal
```

---

## 🔒 Sécurité & Performance

- Authentification Laravel, CSRF, validation stricte
- Upload sécurisé, nettoyage automatique
- Cache des résultats, optimisation requêtes, assets minifiés
- Analyse IA < 2 secondes, fallback automatique

---

## 🧪 Tests & Qualité

```bash
# Tests unitaires & intégration
php artisan test
```

---

## 🤝 Contribution & Support

- Fork, branche, PR bienvenus !
- Issues : via GitHub
- Documentation COBIT : [ISACA](https://www.isaca.org/resources/cobit)
- Documentation Laravel : [Laravel](https://laravel.com/docs)

---

## 📄 Licence

MIT

---

**🚀 Évaluez et améliorez votre gouvernance IT avec COBIT 2019, l’IA et des visualisations interactives !**

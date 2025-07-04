# COBIT 2019 - Laravel Application

![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2-blue.svg)
![Docker](https://img.shields.io/badge/Docker-Enabled-blue.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## 📋 Description

Application web Laravel pour l'évaluation et la gestion des Design Factors selon le framework COBIT 2019. Cette application permet aux organisations d'évaluer leur maturité en gouvernance IT et de générer des rapports détaillés.

## ✨ Fonctionnalités

- 🎯 **Évaluation COBIT 2019** - Évaluation complète des 10 Design Factors
- 📊 **Tableaux de bord interactifs** - Visualisation des résultats en temps réel
- 📄 **Export PDF/Excel** - Génération de rapports professionnels
- 🤖 **Chatbot intégré** - Assistant intelligent pour l'aide à l'évaluation
- 🔐 **Authentification sécurisée** - Gestion des utilisateurs et des sessions
- 💾 **Sauvegarde des évaluations** - Historique et suivi des évaluations
- 🎨 **Interface moderne** - Design responsive avec Tailwind CSS

## 🛠️ Technologies Utilisées

- **Backend**: Laravel 10.x, PHP 8.2
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Base de données**: MySQL 8.0
- **Cache**: Redis 7
- **Containerisation**: Docker & Docker Compose
- **Email**: MailHog (développement)
- **Build**: Vite.js

## 🚀 Installation

### Prérequis

- Docker & Docker Compose
- Git

### Étapes d'installation

1. **Cloner le repository**
```bash
git clone https://github.com/Jiheddridi/Cobit-2019.git
cd Cobit-2019
```

2. **Lancer l'environnement Docker**
```bash
docker-compose up -d
```

3. **Accéder à l'application**
- Application: http://localhost:8000
- phpMyAdmin: http://localhost:8080
- MailHog: http://localhost:8025

### Compte de test

- **Email**: admin@cobit.local
- **Mot de passe**: password123

## 🎯 Design Factors COBIT 2019

L'application évalue les 10 Design Factors suivants :

1. **DF1** - Enterprise Strategy
2. **DF2** - Enterprise Goals
3. **DF3** - Enterprise Risk Profile
4. **DF4** - IT-Related Issues
5. **DF5** - Threat Landscape
6. **DF6** - Compliance Requirements
7. **DF7** - Role of IT
8. **DF8** - Sourcing Model
9. **DF9** - IT Implementation Methods
10. **DF10** - Enterprise Size

## 🔧 Commandes Utiles

```bash
# Voir les logs
docker-compose logs app

# Redémarrer l'application
docker-compose restart app

# Accéder au conteneur
docker-compose exec app bash

# Exécuter des commandes Artisan
docker-compose exec app php artisan [commande]
```

## 🤝 Contribution

Les contributions sont les bienvenues ! Veuillez suivre ces étapes :

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 👨‍💻 Auteur

**Jihed Dridi**
- GitHub: [@Jiheddridi](https://github.com/Jiheddridi)

## 📝 License

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

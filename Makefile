# Makefile pour COBIT Laravel Docker

# Variables
DOCKER_COMPOSE = docker-compose
DOCKER = docker
APP_CONTAINER = cobit-laravel-app
MYSQL_CONTAINER = cobit-mysql

# Commandes principales
.PHONY: help build up down restart logs shell mysql-shell composer artisan npm test clean

# Afficher l'aide
help:
	@echo "Commandes disponibles pour COBIT Laravel:"
	@echo "  build          - Construire les images Docker"
	@echo "  up             - Démarrer tous les services"
	@echo "  down           - Arrêter tous les services"
	@echo "  restart        - Redémarrer tous les services"
	@echo "  logs           - Afficher les logs"
	@echo "  shell          - Accéder au shell du conteneur app"
	@echo "  mysql-shell    - Accéder au shell MySQL"
	@echo "  composer       - Exécuter des commandes Composer"
	@echo "  artisan        - Exécuter des commandes Artisan"
	@echo "  npm            - Exécuter des commandes NPM"
	@echo "  test           - Exécuter les tests"
	@echo "  clean          - Nettoyer les conteneurs et volumes"
	@echo "  setup          - Configuration initiale complète"

# Construire les images
build:
	$(DOCKER_COMPOSE) build --no-cache

# Démarrer les services
up:
	$(DOCKER_COMPOSE) up -d

# Arrêter les services
down:
	$(DOCKER_COMPOSE) down

# Redémarrer les services
restart: down up

# Afficher les logs
logs:
	$(DOCKER_COMPOSE) logs -f

# Accéder au shell du conteneur app
shell:
	$(DOCKER) exec -it $(APP_CONTAINER) bash

# Accéder au shell MySQL
mysql-shell:
	$(DOCKER) exec -it $(MYSQL_CONTAINER) mysql -u laravel_user -p cobit_laravel

# Exécuter des commandes Composer
composer:
	$(DOCKER) exec $(APP_CONTAINER) composer $(filter-out $@,$(MAKECMDGOALS))

# Exécuter des commandes Artisan
artisan:
	$(DOCKER) exec $(APP_CONTAINER) php artisan $(filter-out $@,$(MAKECMDGOALS))

# Exécuter des commandes NPM
npm:
	$(DOCKER) exec $(APP_CONTAINER) npm $(filter-out $@,$(MAKECMDGOALS))

# Exécuter les tests
test:
	$(DOCKER) exec $(APP_CONTAINER) php artisan test

# Nettoyer les conteneurs et volumes
clean:
	$(DOCKER_COMPOSE) down -v --remove-orphans
	$(DOCKER) system prune -f

# Configuration initiale complète
setup: build
	@echo "Configuration initiale de COBIT Laravel..."
	@cp .env.docker .env
	$(DOCKER_COMPOSE) up -d
	@echo "Attente du démarrage des services..."
	@sleep 30
	$(DOCKER) exec $(APP_CONTAINER) php artisan key:generate
	$(DOCKER) exec $(APP_CONTAINER) php artisan migrate --force
	$(DOCKER) exec $(APP_CONTAINER) php artisan db:seed
	@echo "Configuration terminée!"
	@echo "Application disponible sur: http://localhost:8000"
	@echo "phpMyAdmin disponible sur: http://localhost:8080"
	@echo "Mailhog disponible sur: http://localhost:8025"

# Permettre aux arguments d'être passés aux commandes
%:
	@:

SHELL := /bin/sh
DOCKER_COMPOSE_ARGS?=
DOCKER_COMPOSE?=docker compose $(DOCKER_COMPOSE_ARGS)
EXEC=$(DOCKER_COMPOSE) exec netflix
COMPOSER=$(EXEC) composer
CONSOLE_SF=$(EXEC) bin/console
CONSOLE_NPM=$(EXEC) npm

.DEFAULT_GOAL := help

.PHONY: help setup env-local rapidapi-key install build watch-assets serve start stop migrations test cache-clear clean

help:
	@printf "Commandes disponibles :\n"
	@printf "  make setup          Installe les dépendances, demande la clé RapidAPI, lance les migrations, build les assets.\n"
	@printf "  make rapidapi-key   Demande la clé RapidAPI et la stocke dans .env.local.\n"
	@printf "  make start          Lance le serveur Symfony en arrière-plan via symfony-cli.\n"
	@printf "  make stop           Arrête le serveur démarré avec make start.\n"
	@printf "  make watch-assets   Lance la compilation front en mode watch.\n"
	@printf "  make test           Exécute la suite de tests PHPUnit.\n"
	@printf "  make clean          Supprime les caches et les assets générés.\n"
	@printf "\nUtilisez \"make <commande>\" pour exécuter les autres recettes.\n"

setup: install migrations build ## Installe dépendances PHP/JS, configure la clé API, migre la base, build les assets

start:
	$(DOCKER_COMPOSE) up --remove-orphans --build -d

stop:
	$(DOCKER_COMPOSE) down

env-local: ## Crée .env.local si absent afin de garder les secrets hors versionnage
	@if [ ! -f .env.local ]; then \
		cp .env .env.local && echo ".env.local created from .env"; \
	else \
		echo ".env.local already exists"; \
	fi

install: ## Installe les dépendances PHP et JS
	$(COMPOSER) install
	$(CONSOLE_NPM) install

build: ## Compile les assets front pour la prod
	$(CONSOLE_NPM) run build

watch-assets: ## Recompile les assets à chaque changement (bloquant)
	$(CONSOLE_NPM) run dev -- --watch

migrations: ## Crée la base si besoin et applique les migrations
	mkdir -p var
	$(CONSOLE_SF) doctrine:migrations:migrate --no-interaction

test: ## Exécute la suite de tests PHP (prépare la base test automatiquement)
	$(EXEC) bin/console cache:clear --env=test
	$(EXEC) bin/console doctrine:migrations:migrate --no-interaction --env=test
	$(DOCKER_COMPOSE) exec -e APP_ENV=test netflix ./vendor/bin/phpunit

cc: ## Vide les caches Symfony et les logs
	$(EXEC) rm -rf var/cache/* var/log/*

clean: cache-clear ## Supprime aussi les assets générés
	$(EXEC) rm -rf public/build

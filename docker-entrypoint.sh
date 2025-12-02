#!/bin/sh
set -e

composer install
npm install
npm run build

# Crée le répertoire var si nécessaire
mkdir -p var

# Exécute les migrations (Doctrine crée le fichier SQLite automatiquement)
./bin/console doctrine:migrations:migrate --no-interaction

exec frankenphp run --config /etc/caddy/Caddyfile

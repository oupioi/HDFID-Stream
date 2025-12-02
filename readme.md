# HDFDI le nouveau netflix (Symfony 7 + RapidAPI)

Application Symfony qui reproduit une expérience « Netflix » : catalogue de films et de séries, fiches dynamiques, recherche, authentification complète (login/inscription) et gestion de compte. Toutes les données proviennent de l'API publique Movies Database via RapidAPI.

## Prérequis
- Docker et Docker Compose
- Compte RapidAPI (clé pour Movies Database)

## Installation avec Docker

1. **Cloner le dépôt**
   ```bash
   git clone le projet 
   cd netflix
   ```

2. **Configurer la clé RapidAPI**
   ```bash
   cp .env .env.local
   ```
   Éditez `.env.local` et ajoutez votre clé RapidAPI.

3. **Démarrer l'application**
   ```bash
   make start
   ```
   Au premier lancement, Docker :
   - construit l'image FrankenPHP (PHP 8.4 + Node.js 20) ;
   - installe les dépendances PHP (`composer install`) et JS (`npm install`) ;
   - exécute les migrations Doctrine SQLite ;
   - compile les assets front (`npm run build`).

   Rendez-vous ensuite sur http://localhost:8095.

## Commandes Make disponibles

| Commande            | Description                                      |
|---------------------|--------------------------------------------------|
| `make start`        | Démarre les conteneurs Docker                    |
| `make setup`        | Installe dépendances + migrations + build assets |
| `make install`      | Installe les dépendances PHP et JS               |
| `make build`        | Compile les assets front                         |
| `make watch-assets` | Recompile les assets à chaque changement         |
| `make migrations`   | Applique les migrations Doctrine                 |
| `make test`         | Exécute la suite de tests PHPUnit                |
| `make cc`           | Vide les caches Symfony et les logs              |
| `make clean`        | Supprime caches et assets générés                |

## Dépannage rapide
- **Cache bloqué** : `make cc` supprime `var/cache` et `var/log`.
- **Assets obsolètes** : `make clean && make build` pour repartir de zéro.
- **Clé RapidAPI invalide** : modifiez `.env.local` puis videz le cache avec `make cc`.

### 1️⃣ **Modifier le .env**
Créer un fichier .env, dont vous pouvez trouver un exemple dans le .env.example, et modifier les variables comme suit :
APP_ENV=prod
APP_DEBUG=0

### 2️⃣ **Installation des dépendances**

Installation des dépendances avec composer (vendor)

```bash
composer install --no-dev --optimize-autoloader

```

### 3️⃣ **Migration de la base de donnée**

Créez une migration pour la base de données :

```bash
php bin/console make:migration
```

Exécutez-la [migrations](#migration) que vous venez de créer pour préparer la base de données et changez "le_nom_de_la_migration" par le nom de la migration (sans mettre le .php) que vous avez créé :

```bash
php bin/console doctrine:migrations:execute DoctrineMigrations\le_nom_de_la_migration
```

```bash
php bin/console app:fetch-stations
```


### 4️⃣ **Supprimer le cache**
php bin/console cache:clear
php bin/console cache:warmup
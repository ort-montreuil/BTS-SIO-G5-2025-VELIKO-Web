# Projet Veliko

## Installation

### Etape 1 : Cloner le projet

```bash
git@github.com:ort-montreuil/BTS-SIO-G5-2025-VELIKO-Web.git
```

### Etape 2 : installation des dépendances

Installation des dépendances avec composer (vendor)
```
composer install
```
### Etape 3 : Installation des packages tiers
Installation des packages tiers
```
composer require orm
```
```
composer require doctrine
```
```
composer require doctrine/doctrine-migrations-bundle
```
```
composer require doctrine/dbal
```
```
composer require form 
```
```
composer require symfony/maker-bundle --dev
```
```
composer require symfonycasts/verify-email-bundle symfony/mailer
```
```
composer require doctrine/doctrine-migrations-bundle
```
### Etape 4 : Création de la base de données
```
symfony console doctrine:database:create
```
### Etape 5 : Création des migrations
Crez une migration pour la base de données
```
symfony console make:migration
```
executez la migration
```
symfony console doctrine:migrations:migrate
```
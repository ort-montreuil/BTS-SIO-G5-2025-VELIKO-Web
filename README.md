# Projet Veliko

## Description du projet

Le projet Veliko offre la possibilité d'ajouter des stations de vélos à vos favoris et de les visualiser sur une carte 
interactive. Il comprend un système de connexion et d'inscription. Parmi ses diverses fonctionnalités, vous trouverez
des informations sur la météo, la géolocalisation, ainsi que le nombre de vélos disponibles à chaque station.

## Initialisation du projet

- **Cloner le projet**

Cloner le projet depuis le dépôt gitHub :

```
git clone git@github.com:ort-montreuil/BTS-SIO-G5-2025-VELIKO-Web.git
```

### Etape 1 : installation des dépendances

Installation des dépendances avec composer (vendor)

```
composer install
```

Pour mettre à jour les dépendances (si besoin)

```
composer update
```

### Etape 2 : Démarrage du serveur
Dans la console, tapez la commande suivante pour démarrer le serveur :

I) Installation des images Docker

```
docker-compose up -d
```
> ⚠️ **Important:** : Si vous voulez enlever les images Docker proprement, utilisez la commande :
```
docker-compose down
```

II) Lancer le serveur symfony

```
symfony server:start
```

Puis cliquez sur le lien pour accéder au projet

---

Si vous voulez arrêter le serveur

```
symfony server:stop
```

### Structure du projet

```
.
├── config/                # Contient les fichiers de configuration
├── migrations/            # Contient les fichiers de migration de la base de données
├── public/                # Contient les fichiers publics du projet
├── src/                   # Contient le code source du projet
│   ├── Command/           # Fichier batch de l'application
│   ├── Controller/        # Contrôleurs de l'application
│   ├── DataFixtures/      # Données fictives pour peupler la base de données
│   ├── Entity/            # Entités de l'application
│   ├── Form/              # Formulaires de l'application
│   ├── Repository/        # Répertoires d'entités
│   └── Security/          # Gestion de la sécurité pour les emails
├── templates/             # Contient les fichiers Twig
├── vendor/                # Contient les dépendances du projet
├── .env                   # Contient les variables d'environnement
├── .env.example           # Exemple de fichier d'environnement
├── .gitignore             # Contient les fichiers à ignorer par Git
├── composer.json          # Contient les dépendances du projet
├── composer.lock          # Verrouille les versions des dépendances
├── docker-compose.yml     # Contient les images Docker
├── README.md              # Contient les informations du projet
└── symfony.lock           # Verrouille les versions des dépendances Symfony

```

### Technologies utilisées

Dans ce projet, plusieurs technologies ont été utilisées :

- Utilisation d'[API](#api) pour la météo ([OpenWeatherMap](#openweathermap))
- Utilisation de l'[API](#api) d'[OpenStreetMap](#openstreetmap) pour la géolocalisation et la carte
- Utilisation de l'[API](#api) de Vélib pour les informations sur les stations de vélos
- Utilisation de [Symfony](#symfony) pour le [back-end](#back-end)
- Utilisation de [Twig](#twig) pour le [front-end](#front-end)
- Utilisation de [Bootstrap](#bootstrap) pour le design
- Utilisation de [Docker](#docker) pour l'environnement de développement
- Utilisation de [Mailtrap](#mailtrap) pour les mails
- Utilisation de l'[ORM Doctrine](#orm-doctrine) pour la base de données
- Utilisation de [PHP](#php) pour le développement
- Utilisation d'une base de données [MySQL](#mysql)

### Fonctionnalités

- **Inscription et connexion**
- **Ajout de stations de vélos à vos favoris**
- **Visualisation des stations de vélos sur une carte**
- **Informations sur la météo**
- **Géolocalisation**
- **Nombre de vélos disponibles à chaque station**


### Lexique

---

#### API
**Définition :**  
Une API (Interface de Programmation d'Applications) est un ensemble de règles qui permet à des applications différentes de communiquer entre elles. Elle définit comment envoyer des demandes et recevoir des réponses, facilitant ainsi l'échange de données et de services.

---

#### Symfony
**Définition :**  
Symfony est un framework PHP open-source qui permet de développer des applications web.

---

#### Twig
**Définition :**  
Twig est un moteur de template pour PHP. C'est lui qui donne le visuel pour l'utilisateur.

---

#### Bootstrap
**Définition :**  
Bootstrap est une bibliothèque CSS qui permet de styliser les pages web.

---

#### Docker
**Définition :**  
Docker est une plateforme open-source qui permet de simuler un environnement de développement.

---

#### ORM Doctrine
**Définition :**  
Doctrine est un ORM (Object-Relational Mapping) qui permet de faire le lien entre la base de données et le code PHP.

---

#### Mailtrap
**Définition :**  
Mailtrap est un outil de test pour les emails. Il permet de vérifier si les emails sont envoyés correctement.

---

#### OpenWeatherMap
**Définition :**  
OpenWeatherMap est une API qui permet de récupérer les informations météorologiques.

---

#### OpenStreetMap
**Définition :**  
OpenStreetMap est une API qui permet de récupérer des informations géographiques.

---

#### Front-end
**Définition :**  
Le front-end est la partie visible de l'application. C'est ce que l'utilisateur voit.

---

#### Back-end
**Définition :**  
Le back-end est la partie invisible de l'application. C'est ce qui permet de faire fonctionner l'application.

---

#### PHP
**Définition :**
PHP est un langage de programmation qui permet de créer des sites web dynamiques.

---

#### MySQL
**Définition :**
MySQL est un système de gestion de base de données relationnelles. Une base de données est un ensemble de données organisées.

---


### Auteurs

Pour nous contacter :

Aaron Edery : [GitHub](https://github.com/Aedery16-11)

Noam Baroukh :[GitHub](https://github.com/N-Baroukh)

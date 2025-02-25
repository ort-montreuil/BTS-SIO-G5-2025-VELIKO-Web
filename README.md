# 🚴‍♂️ **Projet Veliko**


##  **Les collaborateurs**

Les personnes ayant travaillé sur le projet sont Noam Baroukh et Aaron Edery.

---

## 🌟 **Description du projet**
Le projet Veliko permet d'ajouter des stations de vélos à vos favoris et de les visualiser sur une carte interactive.
Il inclut un système d'inscription/connexion, des informations sur la météo, la géolocalisation, et le nombre de vélos 
disponibles à chaque station.

---

## 🛠️ **Initialisation du projet**

### 📋 **Prérequis**
Avant de commencer, assurez-vous d'avoir installé les outils suivants :

- **Docker**
- **PHP** (version utilisée : 8.1)
- **Symfony** (version utilisée : 6.4)


### 1️⃣ **Cloner le projet**
Cloner le projet depuis GitHub :
```
git clone git@github.com:ort-montreuil/BTS-SIO-G5-2025-VELIKO-Web.git
```

> ⚠️ **Important :** : Créez un fichier `.env` à la racine du projet et copiez-collez le contenu du fichier `.env.example` dans le fichier `.env`

### 2️⃣ **Installation des dépendances**

Installation des dépendances avec composer (vendor)

```bash
composer install
```

Pour mettre à jour les dépendances (si besoin)

```bash
composer update
```
### 3️⃣ **Installation des images docker**

Dans la console, tapez la commande suivante pour démarrer le serveur :

Installation des images Docker

```bash
docker-compose up -d
```
> ⚠️ **Important :** : Si vous voulez enlever les images Docker proprement, utilisez la commande :

```bash
docker-compose down
```

### 4️⃣ **Modification du fichier [.env](#.env)**

Copiez le contenu du fichier `.env.example` et créez un nouveau fichier `.env` (ou bien, renommez `.env.example` en `.env`). Modifiez les variables d'environnement suivant :

```
DATABASE_URL="mysql://id:mdp@127.0.0.1:3306/app_db?serverVersion=11.5.2-MariaDB&charset=utf8mb4"
```

"id" est le nom d'utilisateur (par défaut "root") et mdp le mot de passe de la base de données (aussi root par défaut, modifiable dans docker-compose.yaml ligne 16).

127.0.0.1:3306 est l'adresse IP et le port de la base de données

app_db est le nom de la base de données

serverVersion=11.5.2 est la version de la base de données : (assurez-vous de mettre la bonne version)

```
MAILER_DSN="smtp://**********:********@sandbox.smtp.mailtrap.io:2525"  
```

Afin de recevoir les mails, vous devez créer un compte sur [Mailtrap](https://mailtrap.io/), puis allez sur "my inbox" 
descendez jusqu'à "Code Samples" ensuite dans la section Symfony, choisissez "symfony 5+".
Ensuite, copiez le code et collez-le dans le fichier .env dans la variable MAILER_DSN. 
(cliquez bien sur "copy" et non faire un copier-coller)

---

Pour remplir la variable API_METEO dans le fichier .env, vous devez créer un compte sur [OpenWeatherMap](https://home.openweathermap.org/users/sign_up) et récupérer votre clé API dans l'onglet "API keys".

### 5️⃣ **Migration de la base de donnée**

Une migration est déjà disponible contenant la bonne structure de base de données.
Exécutez-la [migration](#migration) pour remplir la base de données :

```bash
symfony console doctrine:migrations:migrate
```

Si vous modifiez les entités et souhaitez créer une migration pour la base de données :

```bash
symfony console make:migration
```
Et répétez la commande précédente juste après.

Afin de remplir la table "station" de la base de donnée, lancez cette commande :
```bash
php bin/console app:fetch-stations
```
### 6️⃣ **Démarrage du serveur**

Lancer le serveur symfony

```bash
symfony server:start
```

Puis cliquez sur le lien pour accéder au projet
> ℹ️ **Information :** : Si vous voulez arrêter le serveur, utilisez la commande :

```bash
symfony server:stop
```
### 7️⃣ **Remplir la base de donnée**

Utiliser la commande pour remplir la base de donnée

```bash
symfony console d:f:l
```
> ⚠️ **Important :** : Le mot de passe de tous les utilisateurs et de l'admin est `Bonjour12345!`
> L'adresse mail est renseigné dans la base de donnée directement 
---
> ⚠️ **Important :** : Si les fixtures ont été faites, il faut faire la commande suivante pour restructurer la table des stations `
```bash
php bin/console app:fetch-stations
```


---

### 📂 **Structure du projet**

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

---

### 💻 **Technologies utilisées**

Dans ce projet, plusieurs technologies ont été utilisées :

- [API](#api) pour la météo ([OpenWeatherMap](#openweathermap))
- API d'[OpenStreetMap](#openstreetmap) pour la géolocalisation et la carte
- API de Vélib pour les informations sur les stations de vélos
- [Symfony](#symfony) pour le [back-end](#back-end)
- [Twig](#twig) pour le [front-end](#front-end)
- [Bootstrap](#bootstrap) pour le design
- [Docker](#docker) pour l'environnement de développement
- [Mailtrap](#mailtrap) pour les mails
- [ORM Doctrine](#orm-doctrine) pour la base de données
- [PHP](#php) pour le développement
- [MySQL](#mysql) pour la base de données

---

### 🔥 **Fonctionnalités**


- 🔑 **Inscription et connexion**
- ⭐ **Ajout de stations de vélos à vos favoris**
- 🗺️ **Visualisation des stations de vélos sur une carte**
- 🌦️ **Informations sur la météo**
- 📍 **Géolocalisation**
- 🚲 **Nombre de vélos disponibles à chaque station**


---

### 📖 **Lexique**


#### <a id="api"></a>🌐 API
**Définition :**  
Une API (Interface de Programmation d'Applications) est un ensemble de règles qui permet à des applications différentes de communiquer entre elles. Elle définit comment envoyer des demandes et recevoir des réponses, facilitant ainsi l'échange de données et de services.

#### <a id="back-end"></a>⚙️ Back-end
**Définition :**  
Le back-end est la partie invisible de l'application. C'est ce qui permet de faire fonctionner l'application.

#### <a id="bootstrap"></a>🎨 Bootstrap
**Définition :**  
Bootstrap est une bibliothèque CSS qui permet de styliser les pages web.

#### <a id="docker"></a>🐳 Docker
**Définition :**  
Docker est une plateforme open-source qui permet de simuler un environnement de développement.

#### <a id=".env"></a>🔐 .env
**Définition :**
Le fichier .env est un fichier de configuration qui contient les variables d'environnement.

#### <a id="mailtrap"></a>📧 Mailtrap
**Définition :**  
Mailtrap est un outil de test pour les emails. Il permet de vérifier si les emails sont envoyés correctement.

#### <a id="migration"></a>🔄 Migration
**Définition :**  
Une migration est un fichier qui permet de mettre à jour la base de données. Elle permet de créer, modifier ou supprimer des tables.

#### <a id="mysql"></a>🛢️ MySQL
**Définition :**  
MySQL est un système de gestion de base de données relationnelles. Une base de données est un ensemble de données organisées.

#### <a id="openstreetmap"></a>🗺️ OpenStreetMap
**Définition :**  
OpenStreetMap est une API qui permet de récupérer des informations géographiques.

#### <a id="openweathermap"></a>🌦️ OpenWeatherMap
**Définition :**  
OpenWeatherMap est une API qui permet de récupérer les informations météorologiques.

#### <a id="orm-doctrine"></a>🗄️ ORM Doctrine
**Définition :**  
Doctrine est un ORM (Object-Relational Mapping) qui permet de faire le lien entre la base de données et le code PHP.

#### <a id="php"></a>🐘 PHP
**Définition :**  
PHP est un langage de programmation qui permet de créer des sites web dynamiques.

#### <a id="symfony"></a>🛠️ Symfony
**Définition :**  
Symfony est un framework PHP open-source qui permet de développer des applications web.

#### <a id="twig"></a>🌿 Twig
**Définition :**  
Twig est un moteur de template pour PHP. C'est lui qui donne le visuel pour l'utilisateur.

#### <a id="front-end"></a>🖥️ Front-end
**Définition :**  
Le front-end est la partie visible de l'application. C'est ce que l'utilisateur voit.


---

### ✍️ **Auteurs**

Pour nous contacter :

Aaron Edery : [GitHub](https://github.com/Aedery16-11)

Noam Baroukh : [GitHub](https://github.com/N-Baroukh)

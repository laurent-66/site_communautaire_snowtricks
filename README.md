# site communautaire snowtricks

<p align="center"><img src="public\images\logo+nom.JPG"></p>

## A propos

Snowtricks est un site collaboratif pour faire connaître le sport du snowboard auprès du grand public et aider à l'apprentissage des figures.

## Mise en place de l'environnement de travail

* Installez le gestionnaire de versions de fichiers GIT  https://git-scm.com/downloads
* Installez l'environnement de développement pour PHP et MySQL sur votre ordinateur avec XAMPP https://www.apachefriends.org/fr/index.html
* Installez le gestionnaire de dépendances de PHP : composer https://getcomposer.org/download/
* Installez NodeJS et NPM https://nodejs.org/en/
* Installez l'interpréteur de commandes symfony (CLI Symfony)
* Version minimale de PHP 7.4.9

### Testez votre configuration

1. Ouvrez	votre terminal
2. Tapez	la commande **git** et	assurez vous qu'il n'y a pas de message d'erreur particulier
3. Tapez	la commande **php	-v** et	assurez vous que vous avez la version 7.4.9 au minimum
4. Tapez	la commande **composer	-v** et	assurez vous qu'il n'y a pas de message d'erreur particulier
5. Tapez	la commande **node	-v** et	assurez vous qu'il n'y a pas de message d'erreur particulier
6. Tapez	la commande **npm	-v** et	assurez vous qu'il n'y a pas de message d'erreur particulier


## Installation projet

### Cloner le dépôt git distant en local
Dans votre terminal, positionnez vous dans le bon répertoire est cloner le dépot git en local 

```bash
git clone https://github.com/laurent-66/site_communautaire_snowtricks.git
```

### Installer les dépendances
Installer les dépendances avec composer à partir du fichier composer.lock

```bash
composer install
```
Mise à jour des dépendances vers leurs dernières versions (optionnel)

```bash
php composer.phar update
```

### Paramétrer les variables d'environnement

Dans l'aborescence du projet rendez vous dans le fichier .env 
les réglages qui vont y être fait seront pour une configuration en local:
* Utilisation de xampp comme serveur pour la base de donnée en SQL avec utilisation de phpmyadmin
* L'adresse de l'application sera http://127.0.0.1:8000
* l'adresse du serveur pour la base de données http://127.0.0.1:3306

Dans le fichier .env penser à commenté la ligne concernant le pgresql et décommenté la ligne SQL

Sur la ligne SQL rentré les information de la manière suivante

DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"

* db_user : entrée un identifiant pour l'accés à la base de donnée
* db_password : entrée mot de passe
* db_name : entrée le nom de la base de donnée par exemple snowTricksProject

sur la ligne mailer
MAILER_DSN=gmail+smtp://USERNAME:PASSWORD@default
* USERNAME : adresse mail
* PASSWORD@default: le mot de passe suivie de @default sans espace

Enregistrez le fichier .env

### création de la base de donnée

Dans votre terminal
```bash
symfony console doctrine:database:create
```
Cette commande va créer la base de donnée en récupérant le nom que nous avons donnés dans le fichier .env

### Jouer les migrations pour alimenter la base de données

1-Lancer l'application Xampp démarrer les modules Apach et MySQL

2-Tapez la commande dans votre terminal
```bash
symfony console doctrine:migrations:migrate
```

### Charger les fixtures

Dans votre terminal

```bash
symfony console doctrine:fixtures:load
```
### Chargement de l'application

1-Lancer le serveur

Dans votre terminal

```bash
symfony server:start
```
2- tapez dans la barre d'url de votre navigateur

http://127.0.0.1:8000 ou localhost:8000

3- Pour arrêter le serveur

```bash
symfony server:stop
```




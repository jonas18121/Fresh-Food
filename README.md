# FRESH FOOD #

## Présentation ##

`Fresh Food` est une application web qui permet la `gestion de votre stock alimentaire`.

## Installation  ##

** En ligne de commande **

Récupérer le projet sur `Github` :
> git clone `depuis la branche master`

Installer les composants de `Symfony` :
> composer install

Créer un fichier à la racine du projet et nommez le `.env.local` et notez cette ligne :
`DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7`
pour se connecter a votre base de données remplacez : 

    - db_user par votre nom d'utilisateur
    - db_password par votre mot de passe
    - db_name par le nom de votre base de données
    - si vous avez MySQL 5.7 laissez le 3306 qui est par défaut
    - si vous avez MySQL 8 écrivez 3308

Lancer le serveur `Symfony` :
> symfony serve

Installer et lancer `yarn` pour le css et le js :
Remarque : Si vous avez un fichier nommez `node_modules` à la racine du projet, supprimez-le avant de faire la commande ci-dessous:

> yarn install

> yarn run dev-server

si vous avez des problèmes avec yarn run dev-server faite :

> yarn remove node-sass

puis 

> yarn add node-sass

et relancer yarn run dev-server

Installer et lancer `maildev` pour l'envoi de mail :

> npm install -g maildev

> maildev

Ce rendre dans l'url `localhost:1080` dans une nouvelle fenêtre de votre navigateur, 
puis écrire `MAILER_URL=smtp://localhost:1025` dans le fichier `.env.local`

## Scénario  ##
    
    - Création d'un compte et connexion au site
    - Création, affichage, edition, suppression d'un produit
    - Visite d'un profil utilisateur
    - Visite du back-office en admin
    - Envoyer un mail depuis son profil dans l'onglet Contact
# SnowTricks

PHP / Symfony - Développement du site communautaire SnowTricks 100% snowboard !

[![Maintainability](https://api.codeclimate.com/v1/badges/0748f3f0091e2f944154/maintainability)](https://codeclimate.com/github/mdoutreluingne/snowtricks/maintainability)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/9c2b5c04e4f84ffe88c762165070a2d5)](https://www.codacy.com/gh/mdoutreluingne/snowtricks/dashboard?utm_source=github.com&utm_medium=referral&utm_content=mdoutreluingne/snowtricks&utm_campaign=Badge_Grade)

## Configuration du serveur requise

*   MySQL ou MariaDB
*   Apache2 (avec le mod_rewrite activé)
*   Php 7.4
*   Composer
*   git
*   YARN 1.22

## Installation du projet

Cloner le projet sur votre disque dur avec la commande :
```text
https://github.com/mdoutreluingne/snowtricks.git
```

Ensuite, effectuez la commande "composer install" depuis le répertoire du projet cloné, afin d'installer les dépendances back nécessaires :
```text
composer install
```

Puis, "yarn install" pour les dépendances front du projet :
```text
yarn install
```

Si vous utilez NPM au lieu de YARN, voici un [lien vers la documentation](https://symfony.com/doc/current/frontend/encore/installation.html#installing-encore-in-symfony-applications).

### Paramétrage et accès à la base de données

Editez le fichier situé à la racine intitulé ".env" afin de remplacer les valeurs de paramétrage de la base de données :

````text
//Exemple : mysql://root:@127.0.0.1:3306/snowtricks
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
````

Ensuite à la racine du projet, effectuez la commande `php bin/console doctrine:database:create` pour créer la base de données :

````text
php bin/console doctrine:database:create
````

Pour obtenir une structure similaire à mon projet au niveau de la base de données, je vous joins aussi dans le dossier `~src/migrations/` les versions de migrations que j'ai utilisées. Vous pouvez donc recréer la base de données en effectuant la commande suivante, à la racine du projet :

```text
php bin/console doctrine:migrations:migrate
```

Après avoir créer votre base de données, vous pouvez également injecter un jeu de données en effectuant la commande suivante :

```text
php bin/console doctrine:fixtures:load
```

### Envoi des mails

Si vous souhaitez utiliser un serveur de mail afin d'envoyer des mails, vous pouvez le configurer dans le fichier `.env` à la racine du projet, dans la partie `MAILER_URL`

Sachez que vous pouvez aussi utiliser [maildev](https://www.npmjs.com/package/maildev) pour simuler l'envoi des mails.

### Identifiant de connexion

*   Nom d'utilisateur : admin
*   Mot de passe : adminadmin

### Lancer le projet

A la racine du projet :

*   Pour lancer le serveur de développement, effectuez un `yarn encore dev`.
*   Pour lancer le serveur de symfony, effectuez un `php bin/console server:start`.

### Bravo, le projet SnowTricks est désormais accessible à l'adresse : localhost:8000

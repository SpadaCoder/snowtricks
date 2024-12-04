# OpenClassrooms - Projet 6 - SnowTricks

# SnowTricks

**SnowTricks** est une plateforme communautaire pour partager et découvrir des figures de snowboard. Les utilisateurs peuvent ajouter, éditer, commenter et visualiser des figures avec des images et vidéos associées.

## Prérequis

- PHP >= 8.1
- Composer
- Symfony CLI
- MySQL ou tout autre SGBD compatible
- Serveur web (Apache, Nginx ou intégré)

## Installation

Tout d'abord, vous devez cloner le dépôt GitHub sur votre machine locale.
Ouvrez votre terminal ou invite de commande et utilisez la commande git clone suivie de l'URL du dépôt GitHub :
```bash
git clone https://github.com/spadacoder/snowtricks.git
cd snowtricks
```

Une fois dans le répertoire du projet, installez les dépendances avec Composer :

```bash
composer install
```

## Configuration de l'environnement Symfony
Configurez votre environnement Symfony, copiez le fichier '.env' pour utiliser les paramètres correctement configuré avec votre base de données.
 ```bash
   cp .env.example .env
```
Ouvrez .env et assurez-vous que les variables de connexion à la base de données sont correctes :

```bash
DATABASE_URL="mysql://username:password@127.0.0.1:3306/snowtricks?serverVersion=5.7"
```
Remplacez username, password, et le nom de la base de données (snowtricks) par les valeurs correspondant à votre configuration.

## Création de la base de données 

Si vous n'avez pas encore créé la base de données, exécutez la commande suivante :

```bash
php bin/console doctrine:database:create
```

Appliquer les migrations :

Si des migrations sont nécessaires, appliquez-les pour mettre à jour votre base de données :

```bash
php bin/console doctrine:migrations:migrate
```

## Installation des fixtures
Une fois la base de données prête, vous pouvez charger les fixtures pour peupler la base de données avec des données de test.

Pour charger les fixtures par défaut (utilisateur, catégories, tricks), exécutez la commande suivante :

```bash
php bin/console doctrine:fixtures:load
```
Cette commande va supprimer toutes les données existantes dans la base de données et charger les fixtures définies dans src/DataFixtures.

Si vous souhaitez éviter la suppression des données existantes, ajoutez l'option --append :

```bash
php bin/console doctrine:fixtures:load --append
```
Vous devriez maintenant avoir un utilisateur par défaut, ainsi que des catégories et tricks préchargés dans votre base de données.

## Configuration
Une fois installé, vous pouvez accéder à l'application via votre navigateur web.
Vous pouvez vous connecter avec les identifiants suivants :
```bash
Utilisateur :  default_user
Mot de passe : password123
```

## Notation Codacy
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/eaa5205525b7415b90d70f6ac1de788b)](https://app.codacy.com/gh/SpadaCoder/snowtricks/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

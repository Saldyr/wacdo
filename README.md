# Back-office Wacdo – Gestion du catalogue et des commandes

Ce projet permet de gérer les produits, menus et commandes d’une borne Wacdo via une interface back-office développée en PHP (architecture MVC) et MySQL.

## Prérequis

- **PHP** : 8.4.0  
- **Composer** : 2.8.6  
- **MySQL** : 9.1.0 (MySQL Community Server)  
- **Extensions PHP requises** :  
  - pdo_mysql  
  - mbstring  
  - openssl  
  - json  
- **Outils** :  
  - Git  
  - Un IDE ou éditeur de code (VS Code, PhpStorm, Sublime…)

## Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/Saldyr/wacdo.git
cd wacdo

# 2. Installer les dépendances PHP
composer install


# 3. Éditez config/db.php pour renseigner :
#   host, port, database, username, password

# 4. Exécuter les migrations pour créer les tables
mysql -u <votre_user> -p wacdo < migrations/*.sql
```
## Configuration

Les paramètres de connexion à la base de données sont stockés dans le fichier `config/db.php`.  
Pour les adapter à votre environnement, ouvrez ce fichier et modifiez simplement le tableau renvoyé :

```php
<?php
return [
  'host'     => '127.0.0.1',  // Adresse du serveur MySQL
  'port'     => 3306,         // Port MySQL (par défaut 3306)
  'database' => 'wacdo',      // Nom de la base de données
  'username' => 'root',       // Nom d’utilisateur MySQL
  'password' => '',           // Mot de passe MySQL (vide s’il n’y en a pas)
];
```

La classe Database (dans lib/Database.php) lit automatiquement ces valeurs :
```php 
// Extrait de lib/Database.php
$cfg = require __DIR__ . '/../config/db.php';
$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8',
    $cfg['host'],
    $cfg['port'],
    $cfg['database']
);
$db = new PDO($dsn, $cfg['username'], $cfg['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```
Aucune autre modification n’est nécessaire : enregistrez simplement vos valeurs dans config/db.php, puis relancez l’application pour que la connexion soit établie avec ces nouveaux paramètres.

## Lancement 

# Se placer dans le dossier 'public'
cd public

# Démarrer le serveur PHP intégré
php -S 127.0.0.1:8000

## Structure du projet

- `public/` : point d’entrée (front-controller)  
- `config/` : configuration de la base  
- `lib/` : classes utilitaires  
- `model/`, `controller/`, `view/` : MVC  
- `migrations/` : scripts SQL  
- `docs/` : ERD & spécifications  


## Base de données

Le schéma de la base est disponible dans docs/ERD_Wacdo.png

## Documentation fonctionnelle

Les workflows (login, CRUD utilisateurs, gestion du catalogue, process de commande) sont décrits dans docs/functional_spec.md.

## Conformité RGPD

Détails du stockage du consentement et de la procédure de droit à l’oubli dans docs/RGPD.md.

## Tests

Pour lancer la suite de tests unitaires :

```bash
composer require --dev phpunit/phpunit
./vendor/bin/phpunit
```


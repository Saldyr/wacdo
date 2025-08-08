# Wacdo – Back-office & Client Order Management

Ce projet **Wacdo** fournit une interface **back-office** pour gérer le catalogue (produits, menus, boissons) et les commandes, ainsi qu’une interface **client** simple permettant de passer et consulter ses commandes. Développé en **PHP 8** (architecture MVC) avec une base **MySQL**, il intègre également les exigences **RGPD** (consentement, anonymisation).

---

## Table des matières
1. [Prérequis](#prérequis)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Base de données](#base-de-données)
5. [Exemples de requêtes SQL](#exemples-de-requêtes-sql)
6. [Démarrage de l’application](#démarrage-de-lapplication)
7. [Structure du projet](#structure-du-projet)
8. [Tests](#tests)
9. [Couverture des tests](#couverture-des-tests)
10. [RGPD](#rgpd)
11. [Spécifications fonctionnelles](#spécifications-fonctionnelles)
12. [Schéma fonctionnel](#schéma-fonctionnel)
13. [Sécurité et rôles](#sécurité-et-rôles)
14. [Déploiement](#déploiement)

---

## Prérequis
- **PHP** ≥ 8.1 (testé sous 8.4)
- **Composer** ≥ 2.0
- **MySQL** ≥ 5.7 ou 8.x
- Extensions PHP : `pdo_mysql`, `mbstring`, `openssl`, `json`
- **Git**, un éditeur de code ou IDE (VS Code, PhpStorm, Sublime…)

---

## Installation
1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/Saldyr/wacdo.git
   cd wacdo
   ```
2. **Installer les dépendances**
   ```bash
   composer install
   ```
3. **Configurer la base de données**
   - Ouvrez `config/db.php` et renseignez vos paramètres MySQL : hôte, port, nom BD, utilisateur, mot de passe.
4. **Créer la base et exécuter les migrations**
   ```bash
   # Import global
   mysql -u <votre_user> -p <nom_bd> < wacdo.sql

   # Ou lancer chaque script :
   mysql -u <user> -p <nom_bd> < migrations/001_create_role.sql
   …
   mysql -u <user> -p <nom_bd> < migrations/013_add_is_active_to_utilisateur.sql
   ```

---

## Configuration
Toutes les options de connexion sont centralisées dans `config/db.php` :
```php
<?php
return [
   'host'     => '127.0.0.1',
   'port'     => 3306,
   'database' => 'wacdo',
   'username' => 'root',
   'password' => '',
];
```
La classe `lib/Database.php` lit ces valeurs pour établir la connexion PDO.

---

## Base de données
- Les scripts SQL sont dans le dossier `migrations/` numérotés de **001** à **013**.
- Un dump global est disponible sous `wacdo.sql`.
- Le schéma conceptuel (ERD) se trouve dans `docs/ERD_Wacdo.png`.

---

## Exemples de requêtes SQL

1. **Nombre de commandes par utilisateur**
   ```sql
   SELECT 
      u.user_prenom || ' ' || u.user_nom AS utilisateur,
      COUNT(c.order_id) AS nb_commandes
   FROM utilisateur u
   JOIN commande c ON c.user_id = u.user_id
   GROUP BY u.user_id;
   ```

2. **Total vendu par menu**
   ```sql
   SELECT
      m.menu_nom,
      SUM(cm.order_menu_quantite * m.menu_prix) AS total_vendu
   FROM menu m
   JOIN commande_menu cm ON cm.menu_id = m.menu_id
   GROUP BY m.menu_id;
   ```

3. **Liste des boissons non encore livrées**
   ```sql
   SELECT
     c.order_id,
     b.boisson_nom,
     b.boisson_prix
   FROM commande c
   JOIN boisson b ON b.boisson_id = c.boisson_id
   WHERE c.order_statut_commande <> 'livrée';
   ```

4. **Détail d’une commande (produits + quantités)**
   ```sql
   SELECT
     p.product_nom,
     cp.order_product_quantite AS quantite
   FROM commande_produit cp
   JOIN produit p ON p.product_id = cp.product_id
   WHERE cp.order_id = 123;
   ```

---

## Démarrage de l’application
1. Placez-vous dans le dossier `public/` :
   ```bash
   cd public
   ```
2. Lancez le serveur PHP intégré :
   ```bash
   php -S 127.0.0.1:8000
   ```
3. Ouvrez votre navigateur à l’adresse :  `http://127.0.0.1:8000`

---

## Structure du projet
```
/                     # racine
├─ public/            # front controller (index.php, api.php)
├─ config/            # config DB, constantes métier
├─ lib/               # classes utilitaires (DB, Auth...)
├─ model/             # classes modèles (Utilisateur, Produit...)
├─ controller/        # controllers MVC
├─ view/              # vues HTML/PHP
├─ migrations/        # scripts SQL d’évolution de la BDD
├─ docs/              # spécifications, RGPD, ERD, functional spec
├─ README.md
└─ wacdo.sql          # dump de la base complète
```

---

## Tests
- Configuration PHPUnit fournie (`phpunit.xml`).
- Pour lancer la suite de tests :
   ```bash
   vendor/bin/phpunit --configuration phpunit.xml
   ```

---

## Couverture des tests
Pour générer et visualiser la couverture :
```bash
vendor/bin/phpunit --configuration phpunit.xml --coverage-text
```
> Objectif : **≥ 70 %** de couverture sur `model/` et `controller/`.

---

## RGPD
La gestion du consentement et l’anonymisation sont implémentées dans :
- **`view/login.php`** : checkbox `register_consentement` et son enregistrement (modèle `Utilisateur::add`).
- **`controller/UtilisateurController.php`** : anonymisation lors de la suppression via `setConsentement(false)` et `date_consentement = NULL`.

Voir le détail dans [docs/RGPD.md](docs/RGPD.md).

---

## Spécifications fonctionnelles
Toutes les spécifications fonctionnelles sont décrites dans :
[docs/functional_spec.md](docs/functional_spec.md)

---

## Schéma fonctionnel
```mermaid
flowchart TD
A[Utilisateur non connecté] -->|Connexion| B{Choix de rôle}

B -->|Admin – Role 1| C[Back-Office]
B -->|Préparateur – Role 2| D[Commandes back-office]
B -->|Accueil – Role 3| D
B -->|Livreur – Role 4| E[Mes livraisons]
B -->|Client – Role 5| F[Passer commande]

C --> G[CRUD Produits]
C --> H[CRUD Catégories]
C --> I[CRUD Menus]
C --> J[CRUD Boissons]
C --> K[Gestion Utilisateurs]

D --> L[Traiter commandes]
E --> M[Voir mes livraisons]

F --> N[Formulaire de commande]
F --> O[Historique de mes commandes]
F --> P[Détail d’une commande]
```

---

## Sécurité et rôles
L’accès aux différentes parties de l’application est contrôlé selon le rôle de l’utilisateur, via `lib/Auth.php` et `Auth::check([...])`.

| Role ID | Rôle                  | Accès principal                                   |
|:-------:|:----------------------|:--------------------------------------------------|
| 1       | Administrateur        | CRUD complet (Produits, Catégories, Menus, Boissons, Utilisateurs) |
| 2       | Manager               | Gestion des commandes back-office                |
| 3       | Préparateur/Accueil   | Gestion des commandes back-office                |
| 4       | Livreur               | Accès et assignation des livraisons               |
| 5       | Client                | Passage et consultation de commandes, profil      |

**Exemples :**
```php
Auth::check([1]);     // Admin uniquement
Auth::check([1,2,3]); // Back-office commandes
Auth::check([4]);     // Livreur
Auth::check([5]);     // Client
```

---

## Déploiement
Sur un serveur de production (Apache/Nginx + PHP-FPM) :
```bash
git pull origin main
composer install --no-dev
# Importer ou migrer la base : mysql -u prod_user -p prod_bd < wacdo.sql
service php7.4-fpm reload  # adapter à votre version
```
Veillez à protéger les dossiers `migrations/` et `docs/` si nécessaire.

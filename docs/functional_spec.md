# Spécification fonctionnelle

## 1. Authentification
- **URL** : `GET /index.php?section=auth`
- **Description** : affiche le formulaire de connexion.
- **Paramètres (POST)** :  
  - `email` (string, requis)  
  - `password` (string, requis)  
- **Rôles** : tous  
- **Comportement** :  
  - Si succès → redirige vers `/index.php` (section par défaut selon rôle).  
  - Si échec → message « Identifiants invalides ».

## 2. Gestion des utilisateurs (Admin)
### 2.1 Liste des utilisateurs
- **URL** : `GET /index.php?section=utilisateur`
- **Description** : affiche la liste paginée.
- **Rôles** : Administrateur
- **Comportement** :  
  - Affiche tableau (nom, mail, rôle, date de création).  
  - Actions : « Modifier », « Supprimer ».

### 2.2 Création d’un utilisateur
- **URL** :  
  - Formulaire : `GET /index.php?section=utilisateur&action=create`  
  - Soumission : `POST /index.php?section=utilisateur&action=store`
- **Paramètres (POST)** :  
  - `user_nom`, `user_prenom`, `user_mail`, `user_password`, `role_id`  
- **Rôles** : Administrateur
- **Comportement** :  
  - Validation des champs → création en base → redirige vers la liste.

*(Répéter pour modification et suppression)*

## 3. Gestion du catalogue
### 3.1 Catégories
- **URL** : `GET /index.php?section=categorie`
- **Actions** : CRUD (similaire aux utilisateurs)

### 3.2 Produits
- **URL** : `GET /index.php?section=produit`
- **Filtres** : `?category_id=<id>`
- **Actions** : CRUD, disponibilité (toggle)

### 3.3 Boissons
- **URL** : `GET /index.php?section=boisson`
- **Actions** : CRUD

### 3.4 Menus
- **URL** : `GET /index.php?section=menu`
- **Actions** : CRUD + gestion des liaisons `menu_produit`

## 4. Processus de commande
### 4.1 Saisie de commande
- **URL** :  
  - Formulaire : `GET /index.php?section=commande&action=create`  
  - Soumission : `POST /index.php?section=commande&action=store`
- **Paramètres (POST)** :  
  - Sélection de produits, boissons, menus, quantités, `order_type`
- **Rôles** : Accueil, Administrateur
- **Comportement** :  
  - Numérotation de ticket, état initial `saisie`

### 4.2 Préparation
- **URL** : `GET /index.php?section=commande&status=saisie`
- **Rôles** : Préparateur, Administrateur
- **Actions** :  
  - Bouton « Préparée » → met à jour `order_statut_commande`

### 4.3 Livraison
- **URL** : `POST /index.php?section=commande&action=deliver`
- **Rôles** : Accueil, Administrateur
- **Comportement** :  
  - Change statut en `livree`

## 5. Statuts et erreurs
- Liste des statuts autorisés (`saisie`, `preparée`, `livree`).  
- Gestion des erreurs (produit indisponible, session expirée).


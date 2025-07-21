<?php
session_start();

// 1) Si on veut se connecter / déconnecter
$section = $_GET['section'] ?? null;
if ($section === 'auth') {
    require __DIR__ . '/../controller/AuthController.php';
    exit;
}

// 2) Si pas d’utilisateur en session → on va au login
if (!isset($_SESSION['user'])) {
    header('Location: index.php?section=auth');
    exit;
}

// 3) Choix de la section par défaut selon le rôle
$role = $_SESSION['user']['role_id'];
if ($section === null) {
    // Admin → vue des produits, Prépa/Accueil → vue des commandes
    $section = $role === 1 ? 'produit' : 'commande';
}

// 4) Dispatch vers le controller adéquat
switch ($section) {
    case 'categorie':
        require __DIR__ . '/../controller/CategorieController.php';
        break;

    case 'menu':
        require __DIR__ . '/../controller/MenuController.php';
        break;

    case 'commande':
        require __DIR__ . '/../controller/CommandeController.php';
        break;

    case 'boisson':
        require __DIR__ . '/../controller/BoissonController.php';
        break;

    case 'utilisateur':
        require __DIR__ . '/../controller/UtilisateurController.php';
        break;

    case 'produit':
    default:
        require __DIR__ . '/../controller/ProduitController.php';
        break;
}

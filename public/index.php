<?php
//0) Charger la classe Database
require_once __DIR__ . '/../lib/Database.php';

session_start();

$section = $_GET['section'] ?? null;
$action  = $_GET['action']  ?? null;

// 1) Pages publiques : login/auth et inscription
$section = $_GET['section'] ?? null;

if ($section === 'auth') {
    require __DIR__ . '/../controller/AuthController.php';
    exit;
}

if ($section === 'register') {
    require __DIR__ . '/../controller/RegisterController.php';
    exit;
}

// 2) Si pas d’utilisateur en session → on redirige vers le login
if (!isset($_SESSION['user'])) {
    header('Location: index.php?section=auth');
    exit;
}

// 3) Section par défaut selon le rôle
$role = $_SESSION['user']['role_id'];
if ($section === null) {
    $section = ($role === 1) ? 'produit' : 'commande';
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

    case 'profile':
        require __DIR__ . '/../controller/UtilisateurController.php';
        break;
}

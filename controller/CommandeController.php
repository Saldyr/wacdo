<?php
// controller/CommandeController.php

require_once __DIR__ . '/../lib/Auth.php';
// Autorise admin (1), préparation (2) et accueil (3)
Auth::check([1, 2, 3]);

require_once __DIR__ . '/../model/Commande.php';
require_once __DIR__ . '/../model/CommandeMenu.php';
require_once __DIR__ . '/../model/CommandeProduit.php';
require_once __DIR__ . '/../model/Menu.php';
require_once __DIR__ . '/../model/Produit.php';
require_once __DIR__ . '/../model/Boisson.php';

$commandeModel        = new Commande();
$commandeMenuModel    = new CommandeMenu();
$commandeProduitModel = new CommandeProduit();
$menuModel            = new Menu();
$produitModel         = new Produit();
$boissonModel         = new Boisson();

// Récupère le rôle courant
$role = $_SESSION['user']['role_id'];

// ───────────────────────────────────────────────────────
// A) CRÉATION D’UNE COMMANDE (POST)
// ───────────────────────────────────────────────────────
if (
    ($_GET['action'] ?? null) === 'add'
    && $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    // CSRF
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    // Seuls admin (1) et accueil (3) peuvent créer
    if (!in_array($role, [1, 3], true)) {
        http_response_code(403);
        exit('Pas autorisé à créer une commande');
    }

    $date    = trim($_POST['order_date_commande']   ?? '');
    $heure   = trim($_POST['order_heure_livraison'] ?? '') ?: null;
    $statut  = trim($_POST['order_statut_commande'] ?? '');
    $ticket  = trim($_POST['order_numero_ticket']   ?? '');
    $userId  = (int) ($_POST['user_id'] ?? 0);
    $boisson = trim($_POST['boisson_id'] ?? '') ?: null;

    // Insère la commande
    $commandeModel->add($date, $heure, $statut, $ticket, $userId, $boisson);
    $orderId = $commandeModel->getLastInsertId();

    // Liaisons menus
    foreach ($_POST['menus'] ?? [] as $menuId => $qty) {
        if (($q = (int)$qty) > 0) {
            $commandeMenuModel->add($orderId, (int)$menuId, $q);
        }
    }
    // Liaisons produits
    foreach ($_POST['produits'] ?? [] as $prodId => $qty) {
        if (($q = (int)$qty) > 0) {
            $commandeProduitModel->add($orderId, (int)$prodId, $q);
        }
    }

    header('Location: index.php?section=commande');
    exit;
}

// ───────────────────────────────────────────────────────
// B) FORMULAIRE DE CRÉATION (GET)
// ───────────────────────────────────────────────────────
if (
    ($_GET['action'] ?? null) === 'add'
    && $_SERVER['REQUEST_METHOD'] === 'GET'
) {
    $menus    = $menuModel->getAll();
    $produits = $produitModel->getAll();
    $boissons = $boissonModel->getAll();
    require __DIR__ . '/../view/commande_add.php';
    exit;
}

// ───────────────────────────────────────────────────────
// C) SUPPRESSION D’UNE COMMANDE (POST)
// ───────────────────────────────────────────────────────
if (
    ($_GET['action'] ?? null) === 'delete'
    && $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    // CSRF
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    // Seul admin (1) peut supprimer
    if ($role !== 1) {
        http_response_code(403);
        exit('Pas autorisé à supprimer');
    }
    $orderId = (int) ($_POST['id'] ?? 0);
    // Supprime d’abord les liaisons
    $commandeMenuModel->deleteAllByCommande($orderId);
    $commandeProduitModel->deleteAllByCommande($orderId);
    // Puis la commande
    $commandeModel->delete($orderId);

    header('Location: index.php?section=commande');
    exit;
}

// ───────────────────────────────────────────────────────
// D) MODIFICATION D’UNE COMMANDE (GET + POST)
// ───────────────────────────────────────────────────────
if (
    ($_GET['action'] ?? null) === 'edit'
    && isset($_GET['id'])
) {
    $orderId = (int) $_GET['id'];

    // Traitement du POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // CSRF
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
            exit('CSRF détecté');
        }
        // Seuls admin (1) et préparation (2) peuvent mettre à jour
        if (!in_array($role, [1, 2], true)) {
            http_response_code(403);
            exit('Pas autorisé à modifier');
        }

        $date    = trim($_POST['order_date_commande']   ?? '');
        $heure   = trim($_POST['order_heure_livraison'] ?? '') ?: null;
        $statut  = trim($_POST['order_statut_commande'] ?? '');
        $ticket  = trim($_POST['order_numero_ticket']   ?? '');
        $userId  = (int) ($_POST['user_id'] ?? 0);
        $boisson = trim($_POST['boisson_id'] ?? '') ?: null;

        // Mise à jour de la commande
        $commandeModel->update($orderId, $date, $heure, $statut, $ticket, $userId, $boisson);

        // Réinitialise puis recrée les liaisons menus
        $commandeMenuModel->deleteAllByCommande($orderId);
        foreach ($_POST['menus'] ?? [] as $menuId => $qty) {
            if (($q = (int)$qty) > 0) {
                $commandeMenuModel->add($orderId, (int)$menuId, $q);
            }
        }
        // Réinitialise puis recrée les liaisons produits
        $commandeProduitModel->deleteAllByCommande($orderId);
        foreach ($_POST['produits'] ?? [] as $prodId => $qty) {
            if (($q = (int)$qty) > 0) {
                $commandeProduitModel->add($orderId, (int)$prodId, $q);
            }
        }

        header('Location: index.php?section=commande');
        exit;
    }

    // Prépare l’affichage du formulaire d’édition
    $commande            = $commandeModel->get($orderId);
    $menus               = $menuModel->getAll();
    $produits            = $produitModel->getAll();
    $boissons            = $boissonModel->getAll();
    $menusParCommande    = $commandeMenuModel->getMenusByCommande($orderId);
    $produitsParCommande = $commandeProduitModel->getProduitsByCommande($orderId);

    require __DIR__ . '/../view/commande_edit.php';
    exit;
}

// ───────────────────────────────────────────────────────
// E) LISTE DES COMMANDES (par défaut)
// ───────────────────────────────────────────────────────
$commandes           = $commandeModel->getAll();
$menusParCommande    = [];
$produitsParCommande = [];
$boissonsParCommande = [];

foreach ($commandes as $cmd) {
    $oid = $cmd['order_id'];

    // menus
    $mrows = $commandeMenuModel->getMenusByCommande($oid);
    foreach ($mrows as &$m) {
        $m['menu_nom'] = $menuModel->get($m['menu_id'])['menu_nom'];
    }
    $menusParCommande[$oid] = $mrows;

    // produits
    $prows = $commandeProduitModel->getProduitsByCommande($oid);
    foreach ($prows as &$p) {
        $p['product_nom'] = $produitModel->get($p['product_id'])['product_nom'];
    }
    $produitsParCommande[$oid] = $prows;

    // boisson
    $boissonsParCommande[$oid] = !empty($cmd['boisson_id'])
        ? $boissonModel->get($cmd['boisson_id'])
        : null;
}

require __DIR__ . '/../view/commande_list.php';

<?php
// controller/CommandeController.php

require_once __DIR__ . '/../lib/Auth.php';
// Autorise admin (1), préparateur (2) et accueil (3)
Auth::check([1, 2, 3]);

require_once __DIR__ . '/../model/Commande.php';
require_once __DIR__ . '/../model/CommandeMenu.php';
require_once __DIR__ . '/../model/CommandeProduit.php';
require_once __DIR__ . '/../model/Menu.php';
require_once __DIR__ . '/../model/Produit.php';
require_once __DIR__ . '/../model/Boisson.php';

$cmdM = new Commande();
$cmM  = new CommandeMenu();
$cpM  = new CommandeProduit();
$mM   = new Menu();
$pM   = new Produit();
$bM   = new Boisson();

$role   = $_SESSION['user']['role_id'];
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// … A) et B) add (existant) …
if ($action === 'add') {
    // Seuls admin(1) et accueil(3)
    if ($role !== 1 && $role !== 3) {
        http_response_code(403);
        exit('Pas autorisé');
    }
    // … reste inchangé …
}

// … C) edit …
if ($action === 'edit') {
    // Seuls admin(1) et accueil(3) (prépa n’édite pas les champs)
    if ($role !== 1 && $role !== 3) {
        http_response_code(403);
        exit('Pas autorisé');
    }
    // … reste inchangé …
}

// … D) delete …
if ($action === 'delete') {
    // Seul admin(1)
    if ($role !== 1) {
        http_response_code(403);
        exit('Pas autorisé');
    }
    // … reste inchangé …
}

// ───────────────────────────────────────────────────────
// E) MARQUER LA COMMANDE COMME PRÊTE (POST uniquement pour préparateur)
// ───────────────────────────────────────────────────────
if ($action === 'markReady' && $method === 'POST') {
    // CSRF
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    // Seul préparateur (2)
    if ($role !== 2) {
        http_response_code(403);
        exit('Pas autorisé');
    }
    $orderId = (int)($_POST['id'] ?? 0);
    // On décide du nouveau statut selon le type de commande
    $cmd = $cmdM->get($orderId);
    if (!$cmd) {
        http_response_code(404);
        exit('Commande introuvable');
    }
    if (($cmd['order_type'] ?? '') === 'a_emporter') {
        $newStatus = 'En livraison';
    } else {
        $newStatus = 'Prête';
    }
    $cmdM->updateStatus($orderId, $newStatus);
    header('Location: index.php?section=commande');
    exit;
}

// ───────────────────────────────────────────────────────
// F) LISTE DES COMMANDES (par défaut)
// ───────────────────────────────────────────────────────
$commandes           = $cmdM->getAll();
$menusParCommande    = [];
$produitsParCommande = [];
$boissonsParCommande = [];
foreach ($commandes as $c) {
    $oid = $c['order_id'];
    // menus …
    $mrows = $cmM->getMenusByCommande($oid);
    foreach ($mrows as &$r) {
        $r['menu_nom'] = $mM->get($r['menu_id'])['menu_nom'];
    }
    $menusParCommande[$oid] = $mrows;
    // produits …
    $prows = $cpM->getProduitsByCommande($oid);
    foreach ($prows as &$r) {
        $r['product_nom'] = $pM->get($r['product_id'])['product_nom'];
    }
    $produitsParCommande[$oid] = $prows;
    // boisson …
    $boissonsParCommande[$oid] = $c['boisson_id']
        ? $bM->get($c['boisson_id'])
        : null;
}

require __DIR__ . '/../view/commande_list.php';

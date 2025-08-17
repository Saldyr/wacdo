<?php

require_once __DIR__ . '/../lib/Auth.php';
// Seul l’admin  (1) peut gérer les menus
Auth::check([1]);

require_once __DIR__ . '/../model/Menu.php';
require_once __DIR__ . '/../model/MenuProduit.php';
require_once __DIR__ . '/../model/Produit.php';

$menuModel        = new Menu();
$menuProduitModel = new MenuProduit();
$produitModel     = new Produit();

// ───────────────────────────────────────────────────────
// 1) AJOUT D’UN MENU (GET + POST)
// ───────────────────────────────────────────────────────
if (($_GET['action'] ?? null) === 'add') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // CSRF
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
            exit('CSRF détecté');
        }

        // Récupère et nettoie les champs
        $nom         = trim($_POST['menu_nom']         ?? '');
        $description = trim($_POST['menu_description'] ?? '');
        $prix        = (float) ($_POST['menu_prix']     ?? 0);
        $imageUrl    = trim($_POST['menu_image_url']   ?? '');
        $dispo       = isset($_POST['menu_disponibilite']) ? 1 : 0;
        $produitsSel = $_POST['produits'] ?? [];

        // Insère le menu et récupère son ID
        $menuModel->add($nom, $description, $prix, $imageUrl, $dispo);
        $newId = $menuModel->getLastInsertId();

        // Associe les produits cochés
        $menuProduitModel->updateProduitsForMenu($newId, $produitsSel);

        header('Location: index.php?section=menu');
        exit;
    }

    // GET → affiche le formulaire d’ajout
    $produits         = $produitModel->getAll();
    $produits_du_menu = []; 
    require __DIR__ . '/../view/menu_add.php';
    exit;
}

// ───────────────────────────────────────────────────────
// 2) SUPPRESSION D’UN MENU (POST)
// ───────────────────────────────────────────────────────
if (
    ($_GET['action'] ?? null) === 'delete' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    // CSRF
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }

    $id = (int) ($_POST['id'] ?? 0);
    // Supprime d'abord les liaisons dans menu_produit
    $menuProduitModel->deleteByMenu($id);
    // Puis le menu
    $menuModel->delete($id);

    header('Location: index.php?section=menu');
    exit;
}

// ───────────────────────────────────────────────────────
// 3) ÉDITION D’UN MENU (GET + POST)
// ───────────────────────────────────────────────────────
if (
    ($_GET['action'] ?? null) === 'edit' &&
    isset($_GET['id'])
) {
    $id = (int) $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // CSRF
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
            exit('CSRF détecté');
        }

        $nom         = trim($_POST['menu_nom']         ?? '');
        $description = trim($_POST['menu_description'] ?? '');
        $prix        = (float) ($_POST['menu_prix']     ?? 0);
        $imageUrl    = trim($_POST['menu_image_url']   ?? '');
        $dispo       = isset($_POST['menu_disponibilite']) ? 1 : 0;
        $produitsSel = $_POST['produits'] ?? [];

        $menuModel->update($id, $nom, $description, $prix, $imageUrl, $dispo);
        $menuProduitModel->updateProduitsForMenu($id, $produitsSel);

        header('Location: index.php?section=menu');
        exit;
    }

    // GET → affiche le formulaire d’édition
    $menu             = $menuModel->get($id);
    $produits         = $produitModel->getAll();
    $produits_du_menu = $menuProduitModel->getProduitsByMenu($id);

    require __DIR__ . '/../view/menu_edit.php';
    exit;
}

// ───────────────────────────────────────────────────────
// 4) LISTE DES MENUS (par défaut)
// ───────────────────────────────────────────────────────
$menus           = $menuModel->getAll();
$produitsParMenu = [];

foreach ($menus as $m) {
    $productIds = $menuProduitModel->getProduitsByMenu($m['menu_id']);
    $list       = [];
    foreach ($productIds as $pid) {
        $p = $produitModel->get((int)$pid);
        if ($p) {
            $list[] = $p['product_nom'];
        }
    }
    $produitsParMenu[$m['menu_id']] = $list;
}

require __DIR__ . '/../view/menu_list.php';

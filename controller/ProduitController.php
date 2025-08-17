<?php

require_once __DIR__ . '/../lib/Auth.php';
// Seul l’admin (1) peut gérer les produits
Auth::check([1]);

require_once __DIR__ . '/../model/Produit.php';
require_once __DIR__ . '/../model/Categorie.php';

$produitModel   = new Produit();
$categorieModel = new Categorie();

// ───────────────────────────────────────────────────────
// 1) AJOUT D’UN PRODUIT (POST)
// ───────────────────────────────────────────────────────
if (
    ($_GET['action'] ?? null) === 'add' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    // CSRF
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }

    // Récupère et nettoie les champs
    $nom         = trim($_POST['product_nom']         ?? '');
    $description = trim($_POST['product_description'] ?? '');
    $prix        = (float) ($_POST['product_prix']     ?? 0);
    $imageUrl    = trim($_POST['product_image_url']   ?? '');
    $dispo       = isset($_POST['product_disponibilite']) ? 1 : 0;
    $categorieId = (int) ($_POST['category_id']        ?? 0);

    // Insertion
    $produitModel->add($nom, $description, $prix, $imageUrl, $dispo, $categorieId);

    header('Location: index.php?section=produit');
    exit;
}

// ───────────────────────────────────────────────────────
// 2) FORMULAIRE D’AJOUT (GET)
// ───────────────────────────────────────────────────────
if (($_GET['action'] ?? null) === 'add') {
    $categories = $categorieModel->getAll();
    require __DIR__ . '/../view/produit_add.php';
    exit;
}

// ───────────────────────────────────────────────────────
// 3) SUPPRESSION D’UN PRODUIT (POST)
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
    $produitModel->delete($id);

    header('Location: index.php?section=produit');
    exit;
}

// ───────────────────────────────────────────────────────
// 4) MODIFICATION D’UN PRODUIT (GET + POST)
// ───────────────────────────────────────────────────────
if (
    ($_GET['action'] ?? null) === 'edit' &&
    isset($_GET['id'])
) {
    $id = (int) $_GET['id'];

    // Traitement du POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // CSRF
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
            exit('CSRF détecté');
        }

        $nom         = trim($_POST['product_nom']         ?? '');
        $description = trim($_POST['product_description'] ?? '');
        $prix        = (float) ($_POST['product_prix']     ?? 0);
        $imageUrl    = trim($_POST['product_image_url']   ?? '');
        $dispo       = isset($_POST['product_disponibilite']) ? 1 : 0;
        $categorieId = (int) ($_POST['category_id']        ?? 0);

        $produitModel->update($id, $nom, $description, $prix, $imageUrl, $dispo, $categorieId);

        header('Location: index.php?section=produit');
        exit;
    }

    // Affichage du formulaire pré‑rempli
    $produit    = $produitModel->get($id);
    $categories = $categorieModel->getAll();
    require __DIR__ . '/../view/produit_edit.php';
    exit;
}

// ───────────────────────────────────────────────────────
// 5) LISTE DES PRODUITS (par défaut)
// ───────────────────────────────────────────────────────
$produits = $produitModel->getAll();
require __DIR__ . '/../view/produit_list.php';

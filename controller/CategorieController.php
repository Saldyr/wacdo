<?php

require_once __DIR__ . '/../lib/Auth.php';
// Seul le rôle admin (1) peut gérer les catégories
Auth::check([1]);

require_once __DIR__ . '/../model/Categorie.php';
$categorieModel = new Categorie();

// 1) Ajout d'une catégorie (POST)
if (
    ($_GET['action'] ?? null) === 'add' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    // CSRF
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    $nom         = trim($_POST['category_nom']         ?? '');
    $description = trim($_POST['category_description'] ?? '');

    $categorieModel->add($nom, $description);
    header('Location: index.php?section=categorie');
    exit;
}

// 2) Formulaire d'ajout (GET)
if (($_GET['action'] ?? null) === 'add') {
    require __DIR__ . '/../view/categorie_add.php';
    exit;
}

// 3) Suppression d'une catégorie
if (
    ($_GET['action'] ?? null) === 'delete' &&
    isset($_GET['id'])
) {
    $categorieModel->delete((int)$_GET['id']);
    header('Location: index.php?section=categorie');
    exit;
}

// 4) Modification d'une catégorie (GET + POST)
if (
    ($_GET['action'] ?? null) === 'edit' &&
    isset($_GET['id'])
) {
    $id = (int)$_GET['id'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // CSRF
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
            exit('CSRF détecté');
        }
        $nom         = trim($_POST['category_nom']         ?? '');
        $description = trim($_POST['category_description'] ?? '');
        $categorieModel->update($id, $nom, $description);
        header('Location: index.php?section=categorie');
        exit;
    }
    // GET → affiche le formulaire d’édition
    $categorie = $categorieModel->get($id);
    require __DIR__ . '/../view/categorie_edit.php';
    exit;
}

// 5) Liste par défaut
$categories = $categorieModel->getAll();
require __DIR__ . '/../view/categorie_list.php';

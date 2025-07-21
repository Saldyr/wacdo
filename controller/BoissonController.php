<?php
// controller/BoissonController.php

require_once __DIR__ . '/../lib/Auth.php';
Auth::check([1]);   // seul l’admin

require_once __DIR__ . '/../model/Boisson.php';
$boissonModel = new Boisson();

// AJOUT (POST)
if (($_GET['action'] ?? null) === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    $nom   = trim($_POST['boisson_nom'] ?? '');
    $prix  = (float)($_POST['boisson_prix'] ?? 0);
    $dispo = isset($_POST['boisson_disponibilite']) ? 1 : 0;

    $boissonModel->add($nom, $prix, $dispo);
    header('Location: index.php?section=boisson');
    exit;
}

// FORMULAIRE D’AJOUT (GET)
if (($_GET['action'] ?? null) === 'add') {
    require __DIR__ . '/../view/boisson_add.php';
    exit;
}

// SUPPRESSION
if (($_GET['action'] ?? null) === 'delete' && isset($_GET['id'])) {
    $boissonModel->delete((int)$_GET['id']);
    header('Location: index.php?section=boisson');
    exit;
}

// ÉDITION (GET + POST)
if (($_GET['action'] ?? null) === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Traitement du POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
            exit('CSRF détecté');
        }
        $nom   = trim($_POST['boisson_nom'] ?? '');
        $prix  = (float)($_POST['boisson_prix'] ?? 0);
        $dispo = isset($_POST['boisson_disponibilite']) ? 1 : 0;

        $boissonModel->update($id, $nom, $prix, $dispo);
        header('Location: index.php?section=boisson');
        exit;
    }

    // Affichage du form edit
    $boisson = $boissonModel->get($id);
    require __DIR__ . '/../view/boisson_edit.php';
    exit;
}

// LISTE par défaut
$boissons = $boissonModel->getAll();
require __DIR__ . '/../view/boisson_list.php';

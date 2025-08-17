<?php

require_once __DIR__ . '/../lib/Auth.php';
Auth::check([1]); // admin uniquement

require_once __DIR__ . '/../model/Boisson.php';
$boissonModel = new Boisson();

$action = $_GET['action'] ?? null;

/* ===== AJOUT (POST) ===== */
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }

    $nom   = trim($_POST['boisson_nom'] ?? '');
    $prix  = (float)($_POST['boisson_prix'] ?? 0);
    $dispo = isset($_POST['boisson_disponibilite']) ? 1 : 0;
    $desc  = trim($_POST['boisson_description'] ?? '');
    $img   = trim($_POST['boisson_image_url'] ?? '');
    if ($img === '') { $img = null; }

    $boissonModel->add($nom, $prix, $dispo, $desc, $img);

    header('Location: index.php?section=boisson');
    exit;
}

/* ===== FORMULAIRE AJOUT (GET) ===== */
if ($action === 'add') {
    require __DIR__ . '/../view/boisson_add.php';
    exit;
}

/* ===== SUPPRESSION (POST) ===== */
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $boissonModel->delete($id);
    }
    header('Location: index.php?section=boisson');
    exit;
}

/* ===== ÉDITION (GET + POST) ===== */
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
            exit('CSRF détecté');
        }
        $nom   = trim($_POST['boisson_nom'] ?? '');
        $prix  = (float)($_POST['boisson_prix'] ?? 0);
        $dispo = isset($_POST['boisson_disponibilite']) ? 1 : 0;
        $desc  = trim($_POST['boisson_description'] ?? '');
        $img   = trim($_POST['boisson_image_url'] ?? '');
        if ($img === '') { $img = null; }

        $boissonModel->update($id, $nom, $prix, $dispo, $desc, $img);

        header('Location: index.php?section=boisson');
        exit;
    }

    $boisson = $boissonModel->get($id);
    if (!$boisson) { http_response_code(404); exit('Boisson introuvable'); }

    require __DIR__ . '/../view/boisson_edit.php';
    exit;
}

/* ===== LISTE ===== */
$boissons = $boissonModel->getAll();
require __DIR__ . '/../view/boisson_list.php';

<?php
// controller/UtilisateurController.php

require_once __DIR__ . '/../lib/Auth.php';
// Seul le rôle admin (1) peut gérer les utilisateurs
Auth::check([1]);

require_once __DIR__ . '/../model/Utilisateur.php';
$uModel = new Utilisateur();

// ───────────────────────────────────────────────────────
// 1a) LISTE (par défaut)
// ───────────────────────────────────────────────────────
if (!isset($_GET['action'])) {
    $users = $uModel->getAll();
    require __DIR__ . '/../view/utilisateur_list.php';
    exit;
}

// ───────────────────────────────────────────────────────
// 1b) AJOUT (GET + POST)
// ───────────────────────────────────────────────────────
if (($_GET['action'] ?? null) === 'add') {
    // Traitement du POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // CSRF
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
            exit('CSRF détecté');
        }
        // Récupère et nettoie les champs
        $prenom   = trim($_POST['prenom']   ?? '');
        $nom      = trim($_POST['nom']      ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
        $role     = (int) ($_POST['role']   ?? 0);

        // Appel au modèle pour insérer
        $uModel->add($prenom, $nom, $email, $password, $role);

        // Redirection vers la liste mise à jour
        header('Location: index.php?section=utilisateur');
        exit;
    }

    // Affichage du formulaire d’ajout
    require __DIR__ . '/../view/utilisateur_add.php';
    exit;
}

// ───────────────────────────────────────────────────────
// 1c) ÉDITION (GET + POST)
// ───────────────────────────────────────────────────────
if (($_GET['action'] ?? null) === 'edit' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Traitement du POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // CSRF
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
            exit('CSRF détecté');
        }
        $prenom = trim($_POST['prenom']   ?? '');
        $nom    = trim($_POST['nom']      ?? '');
        $email  = trim($_POST['email']    ?? '');
        $role   = (int) ($_POST['role']   ?? 0);

        $uModel->update($id, $prenom, $nom, $email, $role);
        header('Location: index.php?section=utilisateur');
        exit;
    }

    // Affichage du formulaire pré-rempli
    $user = $uModel->get($id);
    require __DIR__ . '/../view/utilisateur_edit.php';
    exit;
}

// ───────────────────────────────────────────────────────
// 1d) SUPPRESSION (POST)
// ───────────────────────────────────────────────────────
if (($_GET['action'] ?? null) === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    $id = (int) ($_POST['id'] ?? 0);
    $uModel->delete($id);
    header('Location: index.php?section=utilisateur');
    exit;
}

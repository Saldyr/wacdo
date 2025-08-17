<?php

require_once __DIR__ . '/../model/Utilisateur.php';
$uModel = new Utilisateur();

//-- Déconnexion
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php?section=auth');
    exit;
}

$error = '';
//-- Traitement du formulaire de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']    ?? '');
    $pass  =        $_POST['password'] ?? '';


    $user = $uModel->findByEmail($email);

    if ($user && password_verify($pass, $user['user_password'])) {
        $_SESSION['user'] = [
            'user_id' => $user['user_id'],
            'role_id' => $user['role_id'],
            'name'    => $user['user_prenom'] . ' ' . $user['user_nom'],
        ];
        // Génère un token CSRF pour les formulaires (CRUD, etc.)
        $_SESSION['csrf'] = bin2hex(random_bytes(32));

        header('Location: index.php');
        exit;
    } else {
        $error = 'Email ou mot de passe invalide';
    }
}

require __DIR__ . '/../view/login.php';

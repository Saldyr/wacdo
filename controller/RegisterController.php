<?php

require_once __DIR__ . '/../model/Utilisateur.php';
$uModel = new Utilisateur();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom          = trim($_POST['register_prenom'] ?? '');
    $nom             = trim($_POST['register_nom'] ?? '');
    $email           = filter_var(trim($_POST['register_email']), FILTER_VALIDATE_EMAIL);
    $password        = $_POST['register_password'] ?? '';
    $confirmPassword = $_POST['register_confirm_password'] ?? '';

    if (!$prenom || !$nom || !$email || !$password || !$confirmPassword) {
        $error = 'Tous les champs doivent être renseignés.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif ($uModel->findByEmail($email)) {
        $error = 'Cet email est déjà utilisé.';
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $hasConsent   = isset($_POST['register_consentement']) && $_POST['register_consentement'] === '1';
        $dateConsent  = $hasConsent ? new \DateTime() : null;
        $success = $uModel->add(
            $prenom,
            $nom,
            $email,
            $hashedPassword,
            5,
            $hasConsent,
            $dateConsent
        );
        if ($success) {
            header('Location: index.php?section=auth&registered=1');
            exit;
        } else {
            $error = 'Erreur lors de l\'inscription, réessayez.';
        }
    }
}

// si on arrive en GET ou en erreur, on retombe sur la vue login/inscription
require __DIR__ . '/../view/login.php';

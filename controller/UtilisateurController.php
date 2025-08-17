<?php

$section = $_GET['section'] ?? '';
$action  = $_GET['action']  ?? '';

if ($section === 'profile') {
    require_once __DIR__ . '/../model/Utilisateur.php';
    $uModel = new Utilisateur();

    // Récupère l’ID et le rôle dans la session
    $userId = $_SESSION['user']['user_id']     ?? null;
    $role   = $_SESSION['user']['role_id']     ?? null;
    if ($userId === null || $role !== 5) {
        header('HTTP/1.1 403 Forbidden');
        exit('Accès interdit pour votre rôle.');
    }

    // Dispatcher selon l’action
    switch ($action) {
        case 'export':
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="mes_donnees.json"');
            echo json_encode($uModel->get($userId), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;

        case 'update':
            $prenom = trim($_POST['prenom'] ?? '');
            $nom    = trim($_POST['nom']    ?? '');
            $email  = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
            $uModel->update($userId, $prenom, $nom, $email, $role);
            header('Location: index.php?section=profile&updated=1');
            exit;

        case 'delete':
            $pwdConfirm = $_POST['pwd_confirm'] ?? '';
            $record     = $uModel->get($userId);
            if (password_verify($pwdConfirm, $record['user_password'])) {
                // anonymisation
                $anonEmail = 'user+' . $userId . '@anonymise.local';
                $uModel->update($userId, 'Anonyme', 'Anonyme', $anonEmail, $role);
                // reset consentement
                $uModel
                    ->setConsentement(false)
                    ->setDateConsentement(null)
                    ->setIsActive(false)
                    ->saveStatus($userId);
                session_destroy();
                header('Location: index.php?section=auth&deleted=1');
                exit;
            } else {
                $_SESSION['error'] = 'Mot de passe incorrect.';
                header('Location: index.php?section=profile');
                exit;
            }

        case '':
        default:
            // Affichage du profil
            $user = $uModel->get($userId);
            require __DIR__ . '/../view/utilisateur_profile.php';
            exit;
    }
}

require_once __DIR__ . '/../lib/Auth.php';
// Seul le rôle Admin (1) peut gérer les utilisateurs
Auth::check([1]);

require_once __DIR__ . '/../model/Utilisateur.php';
$uModel = new Utilisateur();

// ───────────────────────────────────────────────────────
// 1a) LISTE (par défaut)
// ───────────────────────────────────────────────────────
if (!isset($_GET['action'])) {
    $users = $uModel->getAllActive();
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
        $password = $_POST['password']      ?? '';
        $role     = (int) ($_POST['role']   ?? 0);

        // 1b‑1) Vérifier unicité de l’email
        if ($uModel->findByEmail($email)) {
            $error = 'Cet email est déjà utilisé.';
            require __DIR__ . '/../view/utilisateur_add.php';
            exit;
        }

        // 1b‑2) Hasher le mot de passe et insérer
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $uModel->add($prenom, $nom, $email, $passwordHash, $role, false, null);

        // Redirection vers la liste
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

        // Récupère et nettoie
        $prenom = trim($_POST['prenom']   ?? '');
        $nom    = trim($_POST['nom']      ?? '');
        $email  = trim($_POST['email']    ?? '');
        $role   = (int) ($_POST['role']   ?? 0);

        // 1c‑1) Vérifier si email changé et déjà utilisé
        $existing = $uModel->findByEmail($email);
        if ($existing && (int)$existing['user_id'] !== $id) {
            $error = 'Cet email est déjà utilisé par un autre compte.';
            $user = ['user_id' => $id, 'user_prenom' => $prenom, 'user_nom' => $nom, 'user_mail' => $email, 'role_id' => $role];
            require __DIR__ . '/../view/utilisateur_edit.php';
            exit;
        }

        // 1c‑2) Mettre à jour
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
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    $id = (int) ($_POST['id'] ?? 0);

    $user = $uModel->get($id);
    if (!$user) {
        header('Location: index.php?section=utilisateur&error=notfound');
        exit;
    }
    if ((int)$user['role_id'] === 1) {
        header('Location: index.php?section=utilisateur&error=admin_forbidden');
        exit;
    }

    // Anonymisation + désactivation
    $uModel->anonymize($id);
    header('Location: index.php?section=utilisateur&info=anonymized');
    exit;
}

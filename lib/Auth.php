<?php

class Auth
{
    public static function check(array $allowedRoles): void
    {
        // Démarre la session
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // 1) L’utilisateur doit être connecté
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?section=auth');
            exit;
        }

        // 2) Le rôle doit être dans la liste des rôles autorisés
        $roleId = $_SESSION['user']['role_id'];
        if (!in_array($roleId, $allowedRoles, true)) {
            http_response_code(403);
            exit('Accès interdit pour votre rôle');
        }
    }
}

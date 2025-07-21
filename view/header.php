<?php
// view/header.php

// Récupère le rôle courant (null si non connecté)
$role = $_SESSION['user']['role_id'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mon Back‑Office Wacdo</title>
    <style>
        /* Un tout petit peu de style inline juste pour lisser */
        nav {
            background: #eee;
            padding: 8px;
            margin-bottom: 16px;
        }
        nav a {
            margin-right: 12px;
            text-decoration: none;
            color: #333;
        }
        nav span {
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <nav>
        <?php if ($role === 1): // Admin ?>
            <a href="index.php?section=produit">Produits</a>
            <a href="index.php?section=categorie">Catégories</a>
            <a href="index.php?section=menu">Menus</a>
            <a href="index.php?section=boisson">Boissons</a>
            <a href="index.php?section=utilisateur">Utilisateurs</a>
        <?php endif; ?>

        <?php if (in_array($role, [1, 2, 3], true)): // Tous les rôles connectés ?>
            <a href="index.php?section=commande">Commandes</a>
        <?php endif; ?>

        <?php if ($role !== null): // Si connecté ?>
            <span>
                Bonjour <?= htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES) ?> |
                <a href="index.php?section=auth&action=logout">Déconnexion</a>
            </span>
        <?php endif; ?>
    </nav>

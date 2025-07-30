<?php

// Charge le mapping des statuts
$STATUT_LABELS = require __DIR__ . '/../config/statuses.php';

// Récupère le rôle courant (null si non connecté)
$role    = $_SESSION['user']['role_id'] ?? null;
$action  = $_GET['action']          ?? '';
$section = $_GET['section']         ?? '';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Back-Office Wacdo</title>
    <style>
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

        /* Onglet actif */
        nav a.active {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <nav>
        <?php if ($role === 1): // Admin 
        ?>
            <a href="index.php?section=produit">Produits</a>
            <a href="index.php?section=categorie">Catégories</a>
            <a href="index.php?section=menu">Menus</a>
            <a href="index.php?section=boisson">Boissons</a>
            <a href="index.php?section=utilisateur">Utilisateurs</a>
        <?php endif; ?>

        <?php if (in_array($role, [1, 2, 3], true)): // Admin, Manager, Prépa/Accueil 
        ?>
            <a href="index.php?section=commande">Commandes back-office</a>
        <?php endif; ?>

        <?php if ($role === 5): // Client 
        ?>
            <a
                href="index.php?section=commande"
                class="<?= $section === 'commande' && $action === ''      ? 'active' : '' ?>">
                Passer commande
            </a>
            <a
                href="index.php?section=commande&action=listClient"
                class="<?= $section === 'commande' && $action === 'listClient' ? 'active' : '' ?>">
                Mes commandes
            </a>
            <?php if ($section === 'commande' && $action === 'view'): ?>
                <a
                    href="index.php?section=commande&action=view&id=<?= (int)$_GET['id'] ?>"
                    class="active">
                    Détail commande
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($role !== null): // Tout utilisateur connecté 
        ?>
            <span>
                Bonjour <?= htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES) ?> |
                <a href="index.php?section=auth&action=logout">Déconnexion</a>
            </span>
        <?php endif; ?>
    </nav>
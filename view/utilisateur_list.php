<?php include __DIR__ . '/header.php'; ?>

<h1>Gestion des utilisateurs</h1>
<p><a href="index.php?section=utilisateur&action=add">Ajouter un utilisateur</a></p>

<?php if (isset($_GET['info']) && $_GET['info'] === 'anonymized'): ?>
    <p style="background:#e6ffea;color:#065f46;padding:8px;border:1px solid #065f46;">
        Compte anonymisé et désactivé.
    </p>
<?php endif; ?>
<?php if (isset($_GET['error']) && $_GET['error'] === 'admin_forbidden'): ?>
    <p style="background:#ffe5e5;color:#a00;padding:8px;border:1px solid #a00;">
        Impossible d’anonymiser un administrateur.
    </p>
<?php endif; ?>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['user_id'],   ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($u['user_prenom'], ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($u['user_nom'],   ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($u['user_mail'],  ENT_QUOTES) ?></td>
                <td>
                    <?php
                    switch ((int)$u['role_id']) {
                        case 1:
                            echo 'Administrateur';
                            break;
                        case 2:
                            echo 'Préparateur';
                            break;
                        case 3:
                            echo 'Caissier(e)';
                            break;
                        case 4:
                            echo 'Livreur(se)';
                            break;
                        case 5:
                            echo 'Client(e)';
                            break;
                        default:
                            echo 'Inconnu';
                    }
                    ?>
                </td>
                <td>
                    <!-- Édition -->
                    <a href="index.php?section=utilisateur&action=edit&id=<?= $u['user_id'] ?>">
                        ✏️
                    </a>
                    <!-- Suppression -->
                    <form method="post"
                        action="index.php?section=utilisateur&action=delete"
                        style="display:inline">
                        <input type="hidden" name="id" value="<?= $u['user_id'] ?>">
                        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                        <button type="submit" title="Anonymiser et désactiver ce compte"
                            onclick="return confirm('Anonymiser ce compte ? Les données perso seront remplacées et le compte sera désactivé (is_active=0).')">
                            🗑️
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/footer.php'; ?>
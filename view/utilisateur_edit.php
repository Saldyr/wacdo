<?php include __DIR__ . '/header.php'; ?>

<h1>Modifier l'utilisateur</h1>
<form method="post" action="index.php?section=utilisateur&action=edit&id=<?= $user['user_id'] ?>">
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

    <label>Prénom:<br>
        <input type="text" name="prenom" required
            value="<?= htmlspecialchars($user['user_prenom'], ENT_QUOTES) ?>">
    </label><br><br>

    <label>Nom:<br>
        <input type="text" name="nom" required
            value="<?= htmlspecialchars($user['user_nom'], ENT_QUOTES) ?>">
    </label><br><br>

    <label>Email:<br>
        <input type="email" name="email" required
            value="<?= htmlspecialchars($user['user_mail'], ENT_QUOTES) ?>">
    </label><br><br>

    <label>Rôle:<br>
        <select name="role">
            <option value="1" <?= $user['role_id'] == 1 ? 'selected' : '' ?>>Administrateur</option>
            <option value="2" <?= $user['role_id'] == 2 ? 'selected' : '' ?>>Préparateur</option>
            <option value="3" <?= $user['role_id'] == 3 ? 'selected' : '' ?>>Accueil</option>
            <option value="4" <?= $user['role_id'] == 4 ? 'selected' : '' ?>>Livreur</option>
            <option value="5" <?= $user['role_id'] == 5 ? 'selected' : '' ?>>Client</option>
        </select>
    </label><br><br>

    <button type="submit">Mettre à jour</button>
    <a href="index.php?section=utilisateur">Annuler</a>
</form>

<?php include __DIR__ . '/footer.php'; ?>

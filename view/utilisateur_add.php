<?php include __DIR__ . '/header.php'; ?>

<h1>Ajouter un utilisateur</h1>
<form method="post" action="index.php?section=utilisateur&action=add">
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

    <label>Prénom:<br>
        <input type="text" name="prenom" required>
    </label><br><br>

    <label>Nom:<br>
        <input type="text" name="nom" required>
    </label><br><br>

    <label>Email:<br>
        <input type="email" name="email" required>
    </label><br><br>

    <label>Mot de passe:<br>
        <input type="password" name="password" required>
    </label><br><br>

    <label>Rôle:<br>
        <select name="role">
            <option value="1">Admin</option>
            <option value="2">Préparation</option>
            <option value="3">Accueil</option>
        </select>
    </label><br><br>

    <button type="submit">Ajouter</button>
    <a href="index.php?section=utilisateur">Annuler</a>
</form>

<?php include __DIR__ . '/footer.php'; ?>
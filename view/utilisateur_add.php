<?php include __DIR__ . '/header.php'; ?>

<h1>Ajouter un utilisateur</h1>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES) ?></p>
<?php endif; ?>

<form method="post" action="index.php?section=utilisateur&action=add">
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

    <label>Prénom:<br>
        <input 
            type="text" 
            name="prenom" 
            required
            value="<?= isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom'], ENT_QUOTES) : '' ?>"
        >
    </label><br><br>

    <label>Nom:<br>
        <input 
            type="text" 
            name="nom" 
            required
            value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom'], ENT_QUOTES) : '' ?>"
        >
    </label><br><br>

    <label>Email:<br>
        <input 
            type="email" 
            name="email" 
            required
            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES) : '' ?>"
        >
    </label><br><br>

    <label>Mot de passe:<br>
        <input type="password" name="password" required>
    </label><br><br>

    <label>Rôle:<br>
        <select name="role">
            <?php 
            $roles = [
                1 => 'Administrateur',
                2 => 'Préparateur',
                3 => 'Accueil',
                4 => 'Livreur',
                5 => 'Client',
            ];
            $selRole = $_POST['role'] ?? null;
            foreach ($roles as $id => $label): ?>
                <option 
                    value="<?= $id ?>" 
                    <?= $selRole == $id ? 'selected' : '' ?>
                >
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <button type="submit">Ajouter</button>
    <a href="index.php?section=utilisateur">Annuler</a>
</form>

<?php include __DIR__ . '/footer.php'; ?>

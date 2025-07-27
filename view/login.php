<?php include __DIR__ . '/header.php'; ?>

<h1>Se connecter</h1>

<?php if (!empty($error)): ?>
    <p style="color:red"><?= htmlspecialchars($error, ENT_QUOTES) ?></p>
<?php endif; ?>

<form method="post" action="index.php?section=auth" autocomplete="off">
    <div>
        <label for="email">Email :</label><br>
        <input
            type="email"
            id="email"
            name="email"
            required
            autocomplete="username">
    </div>
    <div style="margin-top:1em;">
        <label for="password">Mot de passe :</label><br>
        <input
            type="password"
            id="password"
            name="password"
            required
            autocomplete="current-password">
    </div>
    <p style="margin-top:1em;">
        <button type="submit">Connexion</button>
    </p>
</form>

<hr>

<h2>Créer un compte</h2>

<form method="post" action="index.php?section=register" autocomplete="off">
    <div>
        <label for="register_prenom">Prénom :</label><br>
        <input type="text" id="register_prenom" name="register_prenom" required>
    </div>
    <div>
        <label for="register_nom">Nom :</label><br>
        <input type="text" id="register_nom" name="register_nom" required>
    </div>
    <div>
        <label for="register_email">Email :</label><br>
        <input type="email" id="register_email" name="register_email" required autocomplete="email">
    </div>
    <div>
        <label for="register_password">Mot de passe :</label><br>
        <input type="password" id="register_password" name="register_password" required autocomplete="new-password">
    </div>
    <div>
        <label for="register_confirm_password">Confirmer le mot de passe :</label><br>
        <input type="password" id="register_confirm_password" name="register_confirm_password" required autocomplete="new-password">
    </div>
    <p>
        <button type="submit">Inscription</button>
    </p>
</form>


<?php include __DIR__ . '/footer.php'; ?>

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

<?php include __DIR__ . '/footer.php'; ?>
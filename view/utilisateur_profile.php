<?php include __DIR__ . '/header.php'; ?>

<h1>Mon compte</h1>

<?php if (!empty($_GET['updated'])): ?>
    <p style="color:green;">Informations mises à jour avec succès.</p>
<?php endif; ?>
<?php if (!empty($_GET['deleted'])): ?>
    <p style="color:green;">Votre compte a été supprimé/anonymisé.</p>
<?php endif; ?>

<!-- Affichage des informations utilisateur -->
<p><strong>Prénom :</strong> <?= htmlspecialchars($user['user_prenom'], ENT_QUOTES) ?></p>
<p><strong>Nom :</strong> <?= htmlspecialchars($user['user_nom'], ENT_QUOTES) ?></p>
<p><strong>Email :</strong> <?= htmlspecialchars($user['user_mail'], ENT_QUOTES) ?></p>
<p><strong>Date de création :</strong> <?= htmlspecialchars($user['user_date_creation'], ENT_QUOTES) ?></p>
<p><strong>Date de consentement :</strong> <?= htmlspecialchars($user['date_consentement'] ?? 'Non renseignée', ENT_QUOTES) ?></p>

<hr>

<h2>Un petit creux !</h2>
<ul>
    <li><a href="index.php?section=commande">Passer une nouvelle commande</a></li>
    <li><a href="index.php?section=commande&action=listClient">Voir mes commandes</a></li>
</ul>

<hr>

<h2>Modifier mes informations</h2>
<form method="post" action="index.php?section=profile&action=update" autocomplete="off">
    <div>
        <label for="prenom">Prénom :</label><br>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['user_prenom'], ENT_QUOTES) ?>" required>
    </div>
    <div>
        <label for="nom">Nom :</label><br>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['user_nom'], ENT_QUOTES) ?>" required>
    </div>
    <div>
        <label for="email">Email :</label><br>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['user_mail'], ENT_QUOTES) ?>" required>
    </div>
    <p><button type="submit">Mettre à jour</button></p>
</form>

<hr>

<h2>Supprimer mon compte</h2>
<form method="post" action="index.php?section=profile&action=delete" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">
    <div>
        <label for="pwd_confirm">Confirmez votre mot de passe :</label><br>
        <input type="password" id="pwd_confirm" name="pwd_confirm" required>
    </div>
    <p><button type="submit">Supprimer mon compte</button></p>
</form>

<?php include __DIR__ . '/footer.php'; ?>
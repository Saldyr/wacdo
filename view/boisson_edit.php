<?php require __DIR__ . '/header.php'; ?>
<?php $old = $_POST ?: $boisson; ?>

<h1>Modifier une boisson</h1>
<p><a href="index.php?section=boisson">← Retour à la liste</a></p>

<form
    method="post"
    action="index.php?section=boisson&action=edit&id=<?= (int)$boisson['boisson_id'] ?>">
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

    <div>
        <label for="boisson_nom">Nom :</label><br>
        <input
            type="text"
            id="boisson_nom"
            name="boisson_nom"
            value="<?= htmlspecialchars($old['boisson_nom'] ?? '', ENT_QUOTES) ?>"
            required>
    </div>

    <div style="margin-top:1em;">
        <label for="boisson_prix">Prix (€) :</label><br>
        <input
            type="number"
            step="0.01"
            id="boisson_prix"
            name="boisson_prix"
            value="<?= htmlspecialchars($old['boisson_prix'] ?? '', ENT_QUOTES) ?>"
            required>
    </div>

    <div style="margin-top:1em;">
        <label>
            <input
                type="checkbox"
                name="boisson_disponibilite"
                value="1"
                <?= !empty($old['boisson_disponibilite']) ? 'checked' : '' ?>>
            Disponible
        </label>
    </div>

    <div style="margin-top:1em;">
        <label for="boisson_description">Description :</label><br>
        <textarea
            id="boisson_description"
            name="boisson_description"
            rows="4"
            cols="50"
            required><?= htmlspecialchars($old['boisson_description'] ?? '', ENT_QUOTES) ?></textarea>
    </div>

    <div style="margin-top:1em;">
        <label for="boisson_image_url">URL de l'image :</label><br>
        <input
            type="text"
            id="boisson_image_url"
            name="boisson_image_url"
            value="<?= htmlspecialchars($old['boisson_image_url'] ?? '', ENT_QUOTES) ?>">
    </div>

    <p style="margin-top:1em;">
        <button type="submit">Enregistrer</button>
        <a href="index.php?section=boisson">Annuler</a>
    </p>
</form>

<?php require __DIR__ . '/footer.php'; ?>
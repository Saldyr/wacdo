<?php include __DIR__ . '/header.php'; ?>

<h1>Ajouter un produit</h1>
<p><a href="index.php?section=produit">← Retour à la liste</a></p>

<?php
$old = $_POST ?? [];
$csrf = htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES);
?>

<form method="post" action="index.php?section=produit&action=add">
    <!-- CSRF token -->
    <input type="hidden" name="csrf" value="<?= $csrf ?>">

    <div>
        <label for="product_nom">Nom :</label><br>
        <input
            type="text"
            id="product_nom"
            name="product_nom"
            required
            value="<?= htmlspecialchars($old['product_nom'] ?? '', ENT_QUOTES) ?>">
    </div>

    <div style="margin-top:1em;">
        <label for="product_description">Description :</label><br>
        <textarea
            id="product_description"
            name="product_description"
            rows="2"><?= htmlspecialchars($old['product_description'] ?? '', ENT_QUOTES) ?></textarea>
    </div>

    <div style="margin-top:1em;">
        <label for="product_prix">Prix (€) :</label><br>
        <input
            type="number"
            id="product_prix"
            name="product_prix"
            step="0.01"
            required
            value="<?= htmlspecialchars($old['product_prix'] ?? '', ENT_QUOTES) ?>">
    </div>

    <div style="margin-top:1em;">
        <label for="product_image_url">Image URL :</label><br>
        <input
            type="url"
            id="product_image_url"
            name="product_image_url"
            value="<?= htmlspecialchars($old['product_image_url'] ?? '', ENT_QUOTES) ?>">
    </div>

    <div style="margin-top:0.5em;">
        <label>
            <input
                type="checkbox"
                name="product_disponibilite"
                value="1"
                <?= isset($old['product_disponibilite']) ? 'checked' : 'checked' // coché par défaut ?>>
            Disponible
        </label>
    </div>

    <div style="margin-top:1em;">
        <label for="category_id">Catégorie :</label><br>
        <select id="category_id" name="category_id" required>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <option
                        value="<?= (int)$cat['category_id'] ?>"
                        <?= ((int)($old['category_id'] ?? 0) === (int)$cat['category_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category_nom'], ENT_QUOTES) ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="">-- Aucune catégorie --</option>
            <?php endif; ?>
        </select>
    </div>

    <p style="margin-top:1em;">
        <button type="submit">Enregistrer</button>
        <a href="index.php?section=produit">Annuler</a>
    </p>
</form>

<?php include __DIR__ . '/footer.php'; ?>

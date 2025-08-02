<?php include __DIR__ . '/header.php'; ?>

<h1>Modifier le menu</h1>
<form
    method="post"
    action="index.php?section=menu&action=edit&id=<?= (int)$menu['menu_id'] ?>">
    <!-- 1) Token CSRF obligatoire -->
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

    <label>Nom du menu:<br>
        <input
            type="text"
            name="menu_nom"
            required
            value="<?= htmlspecialchars($menu['menu_nom'], ENT_QUOTES) ?>">
    </label><br><br>

    <label>Description:<br>
        <textarea name="menu_description" rows="3">
            <?=htmlspecialchars($menu['menu_description'], ENT_QUOTES)?>
        </textarea>
    </label><br><br>

    <label>Prix:<br>
        <input
            type="number"
            step="0.01"
            name="menu_prix"
            required
            value="<?= htmlspecialchars($menu['menu_prix'], ENT_QUOTES) ?>">
    </label><br><br>

    <label>URL de l'image:<br>
        <input
            type="url"
            name="menu_image_url"
            value="<?= htmlspecialchars($menu['menu_image_url'], ENT_QUOTES) ?>">
    </label><br><br>

    <label>
        <input
            type="checkbox"
            name="menu_disponibilite"
            <?= $menu['menu_disponibilite'] ? 'checked' : '' ?>>
        Disponible
    </label><br><br>

    <fieldset>
        <legend>Produits associés</legend>
        <?php foreach ($produits as $p): ?>
            <label>
                <input
                    type="checkbox"
                    name="produits[<?= (int)$p['product_id'] ?>]"
                    value="1"
                    <?= in_array($p['product_id'], $produits_du_menu) ? 'checked' : '' ?>>
                <?= htmlspecialchars($p['product_nom'], ENT_QUOTES) ?>
            </label><br>
        <?php endforeach; ?>
    </fieldset><br>

    <button type="submit">Mettre à jour</button>
    <a href="index.php?section=menu">Annuler</a>
</form>

<?php include __DIR__ . '/footer.php'; ?>
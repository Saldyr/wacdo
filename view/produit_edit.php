<?php include __DIR__ . '/header.php'; ?>

<h1>Modifier un produit</h1>
<p><a href="index.php?section=produit">← Retour à la liste</a></p>

<form 
    method="post" 
    action="index.php?section=produit&action=edit&id=<?= (int)$produit['product_id'] ?>"
>
    <!-- Token CSRF -->
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

    <div>
        <label for="product_nom">Nom :</label><br>
        <input
            type="text"
            id="product_nom"
            name="product_nom"
            value="<?= htmlspecialchars($produit['product_nom'], ENT_QUOTES) ?>"
            required
        >
    </div>

    <div>
        <label for="product_description">Description :</label><br>
        <textarea
            id="product_description"
            name="product_description"
            rows="2"
        ><?= htmlspecialchars($produit['product_description'], ENT_QUOTES) ?></textarea>
    </div>

    <div>
        <label for="product_prix">Prix (€) :</label><br>
        <input
            type="number"
            id="product_prix"
            name="product_prix"
            step="0.01"
            value="<?= htmlspecialchars($produit['product_prix'], ENT_QUOTES) ?>"
            required
        >
    </div>

    <div>
        <label for="product_image_url">Image URL :</label><br>
        <input
            type="url"
            id="product_image_url"
            name="product_image_url"
            value="<?= htmlspecialchars($produit['product_image_url'], ENT_QUOTES) ?>"
        >
    </div>

    <div>
        <label>
            <input
                type="checkbox"
                name="product_disponibilite"
                value="1"
                <?= $produit['product_disponibilite'] ? 'checked' : '' ?>
            >
            Disponible
        </label>
    </div>

    <div>
        <label for="category_id">Catégorie :</label><br>
        <select id="category_id" name="category_id" required>
            <?php foreach ($categories as $cat): ?>
                <option
                    value="<?= (int)$cat['category_id'] ?>"
                    <?= $produit['category_id'] == $cat['category_id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($cat['category_nom'], ENT_QUOTES) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <p>
        <button type="submit">Enregistrer les modifications</button>
    </p>
</form>

<?php include __DIR__ . '/footer.php'; ?>

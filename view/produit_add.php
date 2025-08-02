<?php include __DIR__ . '/header.php'; ?>

<h1>Ajouter un produit</h1>
<p><a href="index.php?section=produit">← Retour à la liste</a></p>

<form method="post" action="index.php?section=produit&action=add">
    <!-- CSRF token -->
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

    <div>
        <label for="product_nom">Nom :</label><br>
        <input
            type="text"
            id="product_nom"
            name="product_nom"
            required
        >
    </div>

    <div>
        <label for="product_description">Description :</label><br>
        <textarea
            id="product_description"
            name="product_description"
            rows="2"
        ></textarea>
    </div>

    <div>
        <label for="product_prix">Prix (€) :</label><br>
        <input
            type="number"
            id="product_prix"
            name="product_prix"
            step="0.01"
            required
        >
    </div>

    <div>
        <label for="product_image_url">Image URL :</label><br>
        <input
            type="url"
            id="product_image_url"
            name="product_image_url"
        >
    </div>

    <div>
        <label>
            <input
                type="checkbox"
                name="product_disponibilite"
                value="1"
                checked
            >
            Disponible
        </label>
    </div>

    <div>
        <label for="category_id">Catégorie :</label><br>
        <select id="category_id" name="category_id" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['category_id'] ?>">
                    <?= htmlspecialchars($cat['category_nom'], ENT_QUOTES) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <p>
        <button type="submit">Ajouter</button>
    </p>
</form>

<?php include __DIR__ . '/footer.php'; ?>

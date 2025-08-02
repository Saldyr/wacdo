<?php include __DIR__ . '/header.php'; ?>

<h1>Ajouter un menu</h1>
<p><a href="index.php?section=menu">← Retour à la liste</a></p>

<form 
    method="post" 
    action="index.php?section=menu&action=add"
>
    <!-- CSRF token -->
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

    <div>
        <label for="menu_nom">Nom :</label><br>
        <input
            type="text"
            id="menu_nom"
            name="menu_nom"
            required
        >
    </div>

    <div>
        <label for="menu_description">Description :</label><br>
        <textarea
            id="menu_description"
            name="menu_description"
            rows="3"
        ></textarea>
    </div>

    <div>
        <label for="menu_prix">Prix (€) :</label><br>
        <input
            type="number"
            id="menu_prix"
            name="menu_prix"
            step="0.01"
            required
        >
    </div>

    <div>
        <label for="menu_image_url">Image URL :</label><br>
        <input
            type="url"
            id="menu_image_url"
            name="menu_image_url"
        >
    </div>

    <div>
        <label>
            <input
                type="checkbox"
                name="menu_disponibilite"
                value="1"
                checked
            >
            Disponible
        </label>
    </div>

    <fieldset>
        <legend>Produits associés :</legend>
        <?php foreach ($produits as $produit): ?>
            <label>
                <input
                    type="checkbox"
                    name="produits[<?= (int)$produit['product_id'] ?>]"
                    value="1"
                >
                <?= htmlspecialchars($produit['product_nom'], ENT_QUOTES) ?>
            </label><br>
        <?php endforeach; ?>
    </fieldset>

    <p>
        <button type="submit">Ajouter le menu</button>
    </p>
</form>

<?php include __DIR__ . '/footer.php'; ?>

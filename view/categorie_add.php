<?php include __DIR__ . '/header.php'; ?>

<h1>Ajouter une catégorie</h1>
<p><a href="index.php?section=categorie">← Retour à la liste</a></p>

<form 
    method="post" 
    action="index.php?section=categorie&action=add"
>
    <!-- Token CSRF -->
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

    <div>
        <label for="category_nom">Nom de la catégorie :</label><br>
        <input
            type="text"
            id="category_nom"
            name="category_nom"
            required
        >
    </div>

    <div style="margin-top: 1em;">
        <label for="category_description">Description :</label><br>
        <textarea
            id="category_description"
            name="category_description"
            rows="3"
        ></textarea>
    </div>

    <p style="margin-top:1em;">
        <button type="submit">Ajouter</button>
        <a href="index.php?section=categorie">Annuler</a>
    </p>
</form>

<?php include __DIR__ . '/footer.php'; ?>

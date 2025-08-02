<?php include __DIR__ . '/header.php'; ?>

<h1>Modifier une catégorie</h1>
<p><a href="index.php?section=categorie">← Retour à la liste</a></p>

<form 
    method="post" 
    action="index.php?section=categorie&action=edit&id=<?= (int)$categorie['category_id'] ?>"
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
            value="<?= htmlspecialchars($categorie['category_nom'], ENT_QUOTES) ?>"
        >
    </div>

    <div style="margin-top:1em;">
        <label for="category_description">Description :</label><br>
        <textarea
            id="category_description"
            name="category_description"
            rows="3"
        ><?= htmlspecialchars($categorie['category_description'] ?? '', ENT_QUOTES) ?></textarea>
    </div>

    <p style="margin-top:1em;">
        <button type="submit">Enregistrer les modifications</button>
        <a href="index.php?section=categorie">Annuler</a>
    </p>
</form>

<?php include __DIR__ . '/footer.php'; ?>

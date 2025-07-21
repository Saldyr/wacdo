<?php include __DIR__ . '/header.php'; ?>

<h1>Catégories</h1>
<p><a href="index.php?section=categorie&action=add">Ajouter une catégorie</a></p>

<ul>
    <?php if (empty($categories)): ?>
        <li>Aucune catégorie enregistrée.</li>
    <?php else: ?>
        <?php foreach ($categories as $cat): ?>
            <li>
                <?= htmlspecialchars($cat['category_nom'], ENT_QUOTES) ?>
                &nbsp;
                <a
                    href="index.php?section=categorie&action=edit&id=<?= (int)$cat['category_id'] ?>">
                    Modifier
                </a>
                |
                <form
                    method="post"
                    action="index.php?section=categorie&action=delete"
                    style="display:inline">
                    <input type="hidden" name="id" value="<?= (int)$cat['category_id'] ?>">
                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                    <button
                        type="submit"
                        onclick="return confirm('Confirmer la suppression ?')">Supprimer</button>
                </form>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>

<p><a href="index.php?section=produit">Retour à l'accueil</a></p>

<?php include __DIR__ . '/footer.php'; ?>
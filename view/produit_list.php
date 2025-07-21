<?php include __DIR__ . '/header.php'; ?>

<h1>Produits</h1>

<p><a href="index.php?section=produit&action=add">Ajouter un produit</a></p>

<ul>
    <?php if (empty($produits)): ?>
        <li>Aucun produit enregistré.</li>
    <?php else: ?>
        <?php foreach ($produits as $p): ?>
            <li>
                <?= htmlspecialchars($p['product_nom'], ENT_QUOTES) ?> :
                <?= number_format($p['product_prix'], 2) ?> €
                <!-- Lien de modification -->
                <a
                    href="index.php?section=produit&action=edit&id=<?= (int)$p['product_id'] ?>">Modifier</a>
                |
                <!-- Formulaire POST pour la suppression -->
                <form
                    method="post"
                    action="index.php?section=produit&action=delete"
                    style="display:inline">
                    <input type="hidden" name="id" value="<?= (int)$p['product_id'] ?>">
                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                    <button
                        type="submit"
                        onclick="return confirm('Confirmer la suppression ?')">Supprimer</button>
                </form>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>

<?php include __DIR__ . '/footer.php'; ?>
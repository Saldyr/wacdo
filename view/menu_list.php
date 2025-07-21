<?php include __DIR__ . '/header.php'; ?>

<h1>Menus</h1>
<p><a href="index.php?section=menu&action=add">Ajouter un menu</a></p>

<ul>
    <?php foreach ($menus as $menu): ?>
        <li>
            <strong><?= htmlspecialchars($menu['menu_nom'], ENT_QUOTES) ?></strong>
            — <?= number_format($menu['menu_prix'], 2) ?> €
            — <?= $menu['menu_disponibilite'] ? 'Disponible' : 'Indisponible' ?>
            <br>
            <em>Produits associés :</em>
            <?php if (!empty($produitsParMenu[$menu['menu_id']])): ?>
                <ul>
                    <?php foreach ($produitsParMenu[$menu['menu_id']] as $prodNom): ?>
                        <li><?= htmlspecialchars($prodNom, ENT_QUOTES) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                Aucun produit lié.
            <?php endif; ?>

            <p>
                <a href="index.php?section=menu&action=edit&id=<?= (int)$menu['menu_id'] ?>">Modifier</a>
                |
            <form method="post" action="index.php?section=menu&action=delete" style="display:inline">
                <input type="hidden" name="id" value="<?= (int)$menu['menu_id'] ?>">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                <button type="submit" onclick="return confirm('Supprimer ce menu ?')">Supprimer</button>
            </form>
            </p>
        </li>
    <?php endforeach; ?>
</ul>

<?php include __DIR__ . '/footer.php'; ?>
<?php include __DIR__ . '/header.php'; ?>

<h1>Détail de la commande <?= htmlspecialchars($cmd['order_numero_ticket'], ENT_QUOTES) ?></h1>

<ul>
    <li><strong>Date :</strong> <?= htmlspecialchars($cmd['order_date_commande'], ENT_QUOTES) ?></li>
    <li><strong>Type :</strong> <?= htmlspecialchars($cmd['order_type'], ENT_QUOTES) ?></li>
    <li><strong>Statut :</strong> <?= htmlspecialchars($cmd['order_statut_commande'], ENT_QUOTES) ?></li>
</ul>

<h2>Menus</h2>
<?php if (empty($menusCommandes)): ?>
    <p>Aucun menu dans cette commande.</p>
<?php else: ?>
    <ul>
    <?php foreach ($menusCommandes as $m): ?>
        <li>
            <?= htmlspecialchars($m['menu_nom'], ENT_QUOTES) ?>
            <?php if (!empty($m['menu_boisson_id'])): ?>
                – Boisson offerte : <?= htmlspecialchars($m['boisson_nom'], ENT_QUOTES) ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h2>Produits</h2>
<?php if (empty($produitsCommandes)): ?>
    <p>Aucun produit supplémentaire.</p>
<?php else: ?>
    <ul>
    <?php foreach ($produitsCommandes as $p): ?>
        <li>
            <?= htmlspecialchars($p['product_nom'], ENT_QUOTES) ?>
            × <?= (int)$p['order_product_quantite'] ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h2>Boissons à l’unité</h2>
<?php if (empty($boissonsCommandes)): ?>
    <p>Aucune boisson.</p>
<?php else: ?>
    <ul>
    <?php foreach ($boissonsCommandes as $b): ?>
        <li>
            <?= htmlspecialchars($b['boisson_nom'], ENT_QUOTES) ?>
            × <?= (int)$b['order_boisson_quantite'] ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p style="margin-top:2em;">
    <a href="index.php?section=commande&action=listClient">« Retour à mes commandes</a>
</p>

<?php include __DIR__ . '/footer.php'; ?>

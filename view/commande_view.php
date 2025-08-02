<?php include __DIR__ . '/header.php'; ?>
<?php $STATUT_LABELS = require __DIR__ . '/../config/statuses.php'; ?>

<h1>Détail commande #<?= (int)$cmd['order_id'] ?> (Back-Office)</h1>

<ul>
    <li><strong>Date :</strong> <?= htmlspecialchars($cmd['order_date_commande'], ENT_QUOTES) ?> à <?= htmlspecialchars($cmd['order_heure_livraison'] ?: '--', ENT_QUOTES) ?></li>
    <li><strong>Type :</strong>
        <?php
        switch ($cmd['order_type']) {
            case 'a_emporter':
                echo 'À emporter';
                break;
            case 'livraison':
                echo 'Livraison';
                break;
            default:
                echo 'Sur place';
                break;
        }
        ?>
    </li>
    <li><strong>Statut :</strong> <?= htmlspecialchars($STATUT_LABELS[$cmd['order_statut_commande']] ?? $cmd['order_statut_commande'], ENT_QUOTES) ?></li>
    <li><strong>Ticket :</strong> <?= htmlspecialchars($cmd['order_numero_ticket'], ENT_QUOTES) ?></li>
    <li><strong>Client :</strong> #<?= (int)$cmd['user_id'] ?></li>
    <?php if (!empty($cmd['order_created_at'])): ?>
        <li><strong>Passée le :</strong> <?= htmlspecialchars($cmd['order_created_at'], ENT_QUOTES) ?></li>
    <?php endif; ?>
</ul>

<h2>Menus & boissons offertes</h2>
<?php if (empty($menusParCommande)): ?>
    <p>Aucun menu.</p>
<?php else: ?>
    <ul>
        <?php foreach ($menusParCommande as $m): ?>
            <li>
                <?= htmlspecialchars((new Menu())->get($m['menu_id'])['menu_nom'] ?? '—', ENT_QUOTES) ?>
                (x<?= (int)$m['order_menu_quantite'] ?>)
                <?php if (!empty($m['menu_boisson_id'])): ?>
                    — Boisson offerte :
                    <?= htmlspecialchars((new Boisson())->get($m['menu_boisson_id'])['boisson_nom'] ?? '—', ENT_QUOTES) ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h2>Produits supplémentaires</h2>
<?php if (empty($produitsParCommande)): ?>
    <p>Aucun produit.</p>
<?php else: ?>
    <ul>
        <?php foreach ($produitsParCommande as $p): ?>
            <li>
                <?= htmlspecialchars((new Produit())->get($p['product_id'])['product_nom'] ?? '—', ENT_QUOTES) ?>
                (x<?= (int)$p['order_product_quantite'] ?>)
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h2>Boissons à l'unité</h2>
<?php if (empty($boissonsUniteParCommande)): ?>
    <p>Aucune boisson à l'unité.</p>
<?php else: ?>
    <ul>
        <?php foreach ($boissonsUniteParCommande as $b): ?>
            <li>
                <?= htmlspecialchars((new Boisson())->get($b['boisson_id'])['boisson_nom'] ?? '—', ENT_QUOTES) ?>
                (x<?= (int)$b['order_boisson_quantite'] ?>)
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p style="margin-top:2em;">
    <a href="index.php?section=commande">← Retour au back-office</a>
</p>

<?php include __DIR__ . '/footer.php'; ?>
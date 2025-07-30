<?php

require_once __DIR__ . '/../model/Menu.php';
require_once __DIR__ . '/../model/Produit.php';
require_once __DIR__ . '/../model/Boisson.php';

$mM = new Menu();
$pM = new Produit();
$bM = new Boisson();
?>

<?php include __DIR__ . '/header.php'; ?>
<?php $STATUT_LABELS = require __DIR__ . '/../config/statuses.php'; ?>


<h1>Détail de la commande « <?= htmlspecialchars($cmd['order_numero_ticket'], ENT_QUOTES) ?> »</h1>

<ul>
    <li><strong>Date :</strong> <?= htmlspecialchars($cmd['order_date_commande'], ENT_QUOTES) ?></li>

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
        }
        ?>
    </li>

    <li><strong>Statut :</strong> <?= htmlspecialchars($STATUT_LABELS[$cmd['order_statut_commande']] ?? $cmd['order_statut_commande'], ENT_QUOTES) ?></li>
</ul>

<h2>Menus</h2>
<?php if (empty($menusCommandes)): ?>
    <p>Aucun menu dans cette commande.</p>
<?php else: ?>
    <ul>
        <?php foreach ($menusCommandes as $mc):
            $menu = $mM->get($mc['menu_id']);
            $nom  = $menu ? htmlspecialchars($menu['menu_nom'], ENT_QUOTES) : 'Menu introuvable';
        ?>
            <li>
                <?= $nom ?>
                <?php if (!empty($mc['menu_boisson_id'])):
                    $off = $bM->get($mc['menu_boisson_id']);
                ?>
                    – Boisson offerte : <?= htmlspecialchars($off['boisson_nom'], ENT_QUOTES) ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>


<h2>Produits supplémentaires</h2>
<?php if (empty($produitsCommandes)): ?>
    <p>Aucun produit supplémentaire.</p>
<?php else: ?>
    <ul>
        <?php foreach ($produitsCommandes as $pc):
            $prod = $pM->get($pc['product_id']);
            if (!$prod) continue;
            $prodNom = htmlspecialchars($prod['product_nom'], ENT_QUOTES);
            $qte     = (int)$pc['order_product_quantite'];
        ?>
            <li><?= $prodNom ?> × <?= $qte ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h2>Boissons à l’unité</h2>
<?php if (empty($boissonsCommandes)): ?>
    <p>Aucune boisson à l’unité.</p>
<?php else: ?>
    <ul>
        <?php foreach ($boissonsCommandes as $bc):
            $bev = $bM->get($bc['boisson_id']);
            if (!$bev) continue;
            $bevNom = htmlspecialchars($bev['boisson_nom'], ENT_QUOTES);
            $qte    = (int)$bc['order_boisson_quantite'];
        ?>
            <li><?= $bevNom ?> × <?= $qte ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p style="margin-top:2em;">
    <a href="index.php?section=commande&action=listClient">« Retour à mes commandes</a>
</p>

<?php include __DIR__ . '/footer.php'; ?>
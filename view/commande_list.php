<?php include __DIR__ . '/header.php'; ?>

<h1>Liste des commandes</h1>

<p>
    <?php if ($_SESSION['user']['role_id'] !== 2): ?>
        <a href="index.php?section=commande&action=add">Passer une nouvelle commande</a>
    <?php endif; ?>
</p>

<?php if (empty($commandes)): ?>
    <p>Aucune commande enregistrée.</p>
<?php else: ?>
    <ul>
        <?php foreach ($commandes as $cmd): ?>
            <li>
                <strong>Commande #<?= (int)$cmd['order_id'] ?></strong><br>
                Date : <?= htmlspecialchars($cmd['order_date_commande'], ENT_QUOTES) ?> —
                Heure : <?= htmlspecialchars($cmd['order_heure_livraison'] ?: '--', ENT_QUOTES) ?><br>

                Type :
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
                ?><br>

                Statut : <?= htmlspecialchars($cmd['order_statut_commande'], ENT_QUOTES) ?><br>
                Ticket : <?= htmlspecialchars($cmd['order_numero_ticket'], ENT_QUOTES) ?><br>
                Utilisateur : #<?= (int)$cmd['user_id'] ?><br><br>

                <em>Menus & boissons incluses :</em>
                <?php if (!empty($menusParCommande[$cmd['order_id']])): ?>
                    <ul>
                        <?php foreach ($menusParCommande[$cmd['order_id']] as $m): ?>
                            <li>
                                <?= htmlspecialchars($m['menu_nom'], ENT_QUOTES) ?>
                                (x<?= (int)$m['order_menu_quantite'] ?>)
                                <?php if (!empty($m['menu_boisson_id'])): ?>
                                    — Boisson gratuite :
                                    <?= htmlspecialchars(
                                        $boissonMap[$m['menu_boisson_id']]['boisson_nom'] ?? '—',
                                        ENT_QUOTES
                                    ) ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><em>Aucun menu.</em></p>
                <?php endif; ?>

                <em>Produits supplémentaires :</em>
                <?php if (!empty($produitsParCommande[$cmd['order_id']])): ?>
                    <ul>
                        <?php foreach ($produitsParCommande[$cmd['order_id']] as $p): ?>
                            <li>
                                <?= htmlspecialchars($p['product_nom'], ENT_QUOTES) ?>
                                (x<?= (int)$p['order_product_quantite'] ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><em>Aucun produit supplémentaire.</em></p>
                <?php endif; ?>

                <em>Boissons à l’unité :</em>
                <?php if (!empty($boissonsUniteParCommande[$cmd['order_id']])): ?>
                    <ul>
                        <?php foreach ($boissonsUniteParCommande[$cmd['order_id']] as $brow):
                            // On suppose que getByCommande() renvoie ['boisson_id','order_boisson_quantite']
                            $bo = $boissonMap[$brow['boisson_id']] ?? null;
                        ?>
                            <li>
                                <?= htmlspecialchars($bo['boisson_nom'] ?? '—', ENT_QUOTES) ?>
                                (x<?= (int)$brow['order_boisson_quantite'] ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><em>Aucune boisson à l’unité.</em></p>
                <?php endif; ?>

                <?php $total = (new Commande())->getTotal((int)$cmd['order_id']); ?>
                <p><strong>Total :</strong> <?= number_format($total, 2) ?> €</p>

                <div style="margin-top:1em;">
                    <?php if ($_SESSION['user']['role_id'] === 2): ?>
                        <form method="post"
                            action="index.php?section=commande&action=markReady"
                            style="display:inline">
                            <input type="hidden" name="id" value="<?= (int)$cmd['order_id'] ?>">
                            <input type="hidden" name="csrf"
                                value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">
                            <button type="submit"
                                <?= in_array(
                                    strtolower($cmd['order_statut_commande']),
                                    ['pret', 'en_livraison', 'livree'],
                                    true
                                ) ? 'disabled' : '' ?>>
                                Marquer <?= $cmd['order_type'] === 'a_emporter' ? 'en livraison' : 'prête' ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="index.php?section=commande&action=edit&id=<?= (int)$cmd['order_id'] ?>">
                            Modifier
                        </a>
                        &nbsp;|&nbsp;
                        <form method="post"
                            action="index.php?section=commande&action=delete"
                            style="display:inline"
                            onsubmit="return confirm('Supprimer cette commande ?')">
                            <input type="hidden" name="id" value="<?= (int)$cmd['order_id'] ?>">
                            <input type="hidden" name="csrf"
                                value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">
                            <button type="submit">Supprimer</button>
                        </form>
                    <?php endif; ?>
                </div>

                <hr>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><a href="index.php?section=commande">← Retour à l’accueil</a></p>

<?php include __DIR__ . '/footer.php'; ?>
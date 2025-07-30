<?php include __DIR__ . '/header.php'; ?>

<h1>Liste des commandes</h1>

<p>
    <?php if (in_array($_SESSION['user']['role_id'], [1, 3], true)): ?>
        <a href="index.php?section=commande&action=add">Passer une nouvelle commande</a>
    <?php endif; ?>
</p>

<?php if (empty($commandes)): ?>
    <p>Aucune commande enregistrée.</p>
<?php else: ?>
    <ul>
        <?php foreach ($commandes as $cmd):
            $oid    = (int) $cmd['order_id'];
            $status = $cmd['order_statut_commande'];
            $type   = $cmd['order_type'];
            $role   = $_SESSION['user']['role_id'];
        ?>
            <li>
                <strong>Commande #<?= $oid ?></strong><br>
                Date : <?= htmlspecialchars($cmd['order_date_commande'], ENT_QUOTES) ?> —
                Heure : <?= htmlspecialchars($cmd['order_heure_livraison'] ?: '--', ENT_QUOTES) ?><br>
                Type : <?php
                        switch ($type) {
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
                        ?><br>
                Statut : <?= htmlspecialchars($STATUT_LABELS[$cmd['order_statut_commande']] ?? $cmd['order_statut_commande'], ENT_QUOTES) ?><br>
                Ticket : <?= htmlspecialchars($cmd['order_numero_ticket'], ENT_QUOTES) ?><br>
                Client : #<?= (int) $cmd['user_id'] ?><br><br>

                <em>Menus & boissons :</em>
                <?php if (!empty($menusParCommande[$oid])): ?>
                    <ul>
                        <?php foreach ($menusParCommande[$oid] as $m): ?>
                            <li>
                                <?= htmlspecialchars($m['menu_nom'], ENT_QUOTES) ?> (x<?= (int) $m['order_menu_quantite'] ?>)
                                <?php if (!empty($m['menu_boisson_id'])): ?>
                                    — Boisson offerte : <?= htmlspecialchars($boissonMap[$m['menu_boisson_id']]['boisson_nom'] ?? '—', ENT_QUOTES) ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><em>Aucun menu.</em></p>
                <?php endif; ?>

                <em>Produits supplémentaires :</em>
                <?php if (!empty($produitsParCommande[$oid])): ?>
                    <ul>
                        <?php foreach ($produitsParCommande[$oid] as $p): ?>
                            <li>
                                <?= htmlspecialchars($p['product_nom'], ENT_QUOTES) ?> (x<?= (int) $p['order_product_quantite'] ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><em>Aucun produit.</em></p>
                <?php endif; ?>

                <em>Boissons à l’unité :</em>
                <?php if (!empty($boissonsUniteParCommande[$oid])): ?>
                    <ul>
                        <?php foreach ($boissonsUniteParCommande[$oid] as $brow):
                            $bo = $boissonMap[$brow['boisson_id']] ?? null;
                        ?>
                            <li>
                                <?= htmlspecialchars($bo['boisson_nom'] ?? '—', ENT_QUOTES) ?> (x<?= (int) $brow['order_boisson_quantite'] ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><em>Aucune boisson.</em></p>
                <?php endif; ?>

                <?php $total = (new Commande())->getTotal($oid); ?>
                <p><strong>Total :</strong> <?= number_format($total, 2, ',', ' ') ?> €</p>

                <div style="margin-top:1em;">
                    <?php
                    // Rôle 2 : préparation → prêt
                    if ($role === 2 && $status === 'en_preparation'): ?>
                        <form method="post" action="index.php?section=commande&action=markReady" style="display:inline">
                            <input type="hidden" name="id" value="<?= $oid ?>">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">
                            <button type="submit">Marquer prête</button>
                        </form>

                        <?php
                    // Rôle 3 : prêt → servie / en_livraison
                    elseif ($role === 3 && $status === 'pret'):
                        if (in_array($type, ['sur_place', 'a_emporter'], true)): ?>
                            <form method="post" action="index.php?section=commande&action=markReady" style="display:inline">
                                <input type="hidden" name="id" value="<?= $oid ?>">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">
                                <button type="submit">Remettre au client</button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="index.php?section=commande&action=markReady" style="display:inline">
                                <input type="hidden" name="id" value="<?= $oid ?>">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">
                                <button type="submit">Remettre au livreur</button>
                            </form>
                        <?php endif; ?>

                        <?php
                    // Rôle 4 : en_livraison → prise / livrée
                    elseif ($role === 4 && $status === 'en_livraison'):
                        if ($cmd['livreur_id'] === null): ?>
                            <form method="post" action="index.php?section=commande&action=prendre" style="display:inline">
                                <input type="hidden" name="id" value="<?= $oid ?>">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">
                                <button type="submit">Prendre en charge</button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="index.php?section=commande&action=markReady" style="display:inline">
                                <input type="hidden" name="id" value="<?= $oid ?>">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">
                                <button type="submit">Marquer livrée</button>
                            </form>
                    <?php endif;
                    endif;
                    ?>

                    <?php if (in_array($role, [1, 3], true)): ?>
                        &nbsp;
                        <a href="index.php?section=commande&action=edit&id=<?= $oid ?>">Modifier</a>
                        &nbsp;|&nbsp;
                        <form method="post" action="index.php?section=commande&action=delete" style="display:inline" onsubmit="return confirm('Supprimer la commande #<?= $oid ?> ?')">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">
                            <input type="hidden" name="id" value="<?= $oid ?>">
                            <button type="submit">Supprimer</button>
                        </form>
                    <?php endif; ?>
                </div>
                <hr>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><a href="index.php?section=commande">← Retour à l'accueil</a></p>

<?php include __DIR__ . '/footer.php'; ?>
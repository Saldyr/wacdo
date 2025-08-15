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

                <em>Boissons à l'unité :</em>
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
                    <?php if ($role === 2 && $status === 'en_preparation'): ?>
                        <!-- Rôle 2 : préparation → prêt -->
                        <form method="post" action="index.php?section=commande&action=markReady" style="display:inline">
                            <input type="hidden" name="id" value="<?= $oid ?>">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">
                            <button type="submit">Marquer prête</button>
                        </form>

                    <?php elseif ($role === 3 && $status === 'pret' && $cmd['order_type'] === 'livraison'): ?>
                        <!-- Rôle 3 : prêt (livraison) → en_livraison + assignation -->
                        <form method="post" action="index.php?section=commande&action=markReady" style="display:inline">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">
                            <input type="hidden" name="id" value="<?= (int)$cmd['order_id'] ?>">
                            <select name="livreur_id" required>
                                <option value="">-- Choisir livreur --</option>
                                <?php foreach ($livreurs as $l): ?>
                                    <option value="<?= (int)$l['user_id'] ?>">
                                        <?= htmlspecialchars($l['user_nom'] . ' ' . $l['user_prenom'], ENT_QUOTES) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Remettre au livreur</button>
                        </form>

                    <?php elseif ($role === 3 && $status === 'pret' && in_array($cmd['order_type'], ['sur_place', 'a_emporter'], true)): ?>
                        <!-- Rôle 3 : prêt (sur place / à emporter) → servie -->
                        <form method="post" action="index.php?section=commande&action=markReady" style="display:inline">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">
                            <input type="hidden" name="id" value="<?= (int)$cmd['order_id'] ?>">
                            <button type="submit">Marquer servie</button>
                        </form>

                    <?php elseif ($role === 4 && $status === 'en_livraison'): ?>
                        <!-- Rôle 4 : uniquement si la commande est assignée à CE livreur -->
                        <?php $uid = $_SESSION['user']['user_id'] ?? null; ?>
                        <?php if ($uid !== null && (int)$cmd['livreur_id'] === (int)$uid): ?>
                            <form method="post" action="index.php?section=commande&action=markReady" style="display:inline">
                                <input type="hidden" name="id" value="<?= $oid ?>">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">
                                <button type="submit">Remis au client</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (in_array($role, [1, 3], true)): ?>
                        &nbsp;
                        <a href="index.php?section=commande&action=edit&id=<?= $oid ?>">Modifier</a>
                        &nbsp;|&nbsp;
                        <form method="post" action="index.php?section=commande&action=delete" style="display:inline"
                            onsubmit="return confirm('Supprimer la commande #<?= $oid ?> ?')">
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
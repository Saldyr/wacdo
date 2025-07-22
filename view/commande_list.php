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

            <!-- Date et heure -->
            Date : <?= htmlspecialchars($cmd['order_date_commande'] ?? '', ENT_QUOTES) ?> —
            Heure : <?= htmlspecialchars($cmd['order_heure_livraison'] ?? '', ENT_QUOTES) ?: '<span style="color:gray">--</span>' ?><br>

            <!-- Type de commande -->
            Type : 
            <?php if (($cmd['order_type'] ?? '') === 'a_emporter'): ?>
                Livraison
            <?php else: ?>
                Sur place
            <?php endif; ?><br>

            <!-- Statut -->
            Statut : <?= htmlspecialchars($cmd['order_statut_commande'] ?? '', ENT_QUOTES) ?><br>

            <!-- Ticket & Utilisateur -->
            Ticket : <?= htmlspecialchars($cmd['order_numero_ticket'] ?? '', ENT_QUOTES) ?><br>
            Utilisateur (ID) : <?= (int)$cmd['user_id'] ?><br><br>

            <!-- Menus -->
            <em>Menus :</em>
            <?php if (!empty($menusParCommande[$cmd['order_id']])): ?>
                <ul>
                    <?php foreach ($menusParCommande[$cmd['order_id']] as $m): ?>
                        <li>
                            <?= htmlspecialchars($m['menu_nom'], ENT_QUOTES) ?>
                            (x<?= (int)$m['order_menu_quantite'] ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><em>Aucun menu lié.</em></p>
            <?php endif; ?>

            <!-- Produits -->
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

            <!-- Boisson -->
            <em>Boisson au choix :</em>
            <?php if (!empty($boissonsParCommande[$cmd['order_id']])): ?>
                <?= htmlspecialchars($boissonsParCommande[$cmd['order_id']]['boisson_nom'] ?? '', ENT_QUOTES) ?>
            <?php else: ?>
                <span style="color:gray">Aucune</span>
            <?php endif; ?>

            <!-- Total -->
            <?php
                $total = (new Commande())->getTotal((int)$cmd['order_id']);
            ?>
            <p><strong>Total :</strong> <?= number_format($total, 2) ?> €</p>

            <!-- Actions selon rôle -->
            <div style="margin-top:1em;">
            <?php if ($_SESSION['user']['role_id'] === 2): ?>
                <!-- Préparateur : bouton marquer prête/livraison -->
                <form method="post" action="index.php?section=commande&action=markReady" style="display:inline">
                    <input type="hidden" name="id"   value="<?= (int)$cmd['order_id'] ?>">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES) ?>">
                    <button type="submit"
                        <?php if (in_array($cmd['order_statut_commande'], ['Prête','En livraison','Livré'], true)): ?>
                            disabled
                        <?php endif; ?>
                    >
                        Marquer 
                        <?= ($cmd['order_type'] ?? '') === 'a_emporter' ? 'en livraison' : 'prête' ?>
                    </button>
                </form>
            <?php else: ?>
                <!-- Admin & accueil : Modifier / Supprimer -->
                <a href="index.php?section=commande&action=edit&id=<?= (int)$cmd['order_id'] ?>">Modifier</a>
                &nbsp;|&nbsp;
                <form method="post" action="index.php?section=commande&action=delete" style="display:inline">
                    <input type="hidden" name="id"   value="<?= (int)$cmd['order_id'] ?>">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES) ?>">
                    <button type="submit" onclick="return confirm('Confirmer la suppression ?')">
                        Supprimer
                    </button>
                </form>
            <?php endif; ?>
            </div>
        </li>
        <hr>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><a href="index.php?section=produit">← Retour à l'accueil</a></p>

<?php include __DIR__ . '/footer.php'; ?>

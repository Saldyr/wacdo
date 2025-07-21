<?php include __DIR__ . '/header.php'; ?>

<h1>Modifier la commande #<?= (int)$commande['order_id'] ?></h1>
<p><a href="index.php?section=commande">Retour à la liste</a></p>

<form 
    method="post" 
    action="index.php?section=commande&action=edit&id=<?= (int)$commande['order_id'] ?>"
>
    <!-- CSRF -->
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES) ?>">

    <div>
        <label for="order_date_commande">Date de commande :</label><br>
        <input
            type="date"
            id="order_date_commande"
            name="order_date_commande"
            required
            value="<?= htmlspecialchars($commande['order_date_commande'] ?? '', ENT_QUOTES) ?>"
        >
    </div>

    <div style="margin-top:1em;">
        <label for="order_heure_livraison">Heure de livraison :</label><br>
        <input
            type="time"
            id="order_heure_livraison"
            name="order_heure_livraison"
            value="<?= htmlspecialchars($commande['order_heure_livraison'] ?? '', ENT_QUOTES) ?>"
        >
    </div>

    <div style="margin-top:1em;">
        <label for="order_statut_commande">Statut :</label><br>
        <input
            type="text"
            id="order_statut_commande"
            name="order_statut_commande"
            required
            value="<?= htmlspecialchars($commande['order_statut_commande'] ?? '', ENT_QUOTES) ?>"
        >
    </div>

    <div style="margin-top:1em;">
        <label for="order_numero_ticket">Numéro de ticket :</label><br>
        <input
            type="text"
            id="order_numero_ticket"
            name="order_numero_ticket"
            required
            value="<?= htmlspecialchars($commande['order_numero_ticket'] ?? '', ENT_QUOTES) ?>"
        >
    </div>

    <div style="margin-top:1em;">
        <label for="user_id">Utilisateur (ID) :</label><br>
        <input
            type="number"
            id="user_id"
            name="user_id"
            required
            value="<?= (int)($commande['user_id'] ?? 0) ?>"
        >
    </div>

    <fieldset style="margin-top:1em;">
        <legend>Menus :</legend>
        <?php foreach ($menus as $menu): 
            // quantité initiale ou 0
            $qty = 0;
            foreach ($menusParCommande[$commande['order_id']] ?? [] as $m) {
                if ($m['menu_id'] == $menu['menu_id']) {
                    $qty = (int)$m['order_menu_quantite'];
                    break;
                }
            }
        ?>
        <div>
            <label>
                <?= htmlspecialchars($menu['menu_nom'], ENT_QUOTES) ?> —
                <?= number_format($menu['menu_prix'], 2) ?> € :
                <input
                    type="number"
                    name="menus[<?= (int)$menu['menu_id'] ?>]"
                    value="<?= $qty ?>"
                    min="0"
                    style="width:60px;"
                >
            </label>
        </div>
        <?php endforeach; ?>
    </fieldset>

    <fieldset style="margin-top:1em;">
        <legend>Produits supplémentaires :</legend>
        <?php foreach ($produits as $prod): 
            $pq = 0;
            foreach ($produitsParCommande[$commande['order_id']] ?? [] as $p) {
                if ($p['product_id'] == $prod['product_id']) {
                    $pq = (int)$p['order_product_quantite'];
                    break;
                }
            }
        ?>
        <div>
            <label>
                <?= htmlspecialchars($prod['product_nom'], ENT_QUOTES) ?> —
                <?= number_format($prod['product_prix'], 2) ?> € :
                <input
                    type="number"
                    name="produits[<?= (int)$prod['product_id'] ?>]"
                    value="<?= $pq ?>"
                    min="0"
                    style="width:60px;"
                >
            </label>
        </div>
        <?php endforeach; ?>
    </fieldset>

    <div style="margin-top:1em;">
        <label for="boisson_id">Boisson au choix :</label><br>
        <select id="boisson_id" name="boisson_id">
            <option value="">-- Aucune --</option>
            <?php foreach ($boissons as $b): ?>
                <option
                    value="<?= (int)$b['boisson_id'] ?>"
                    <?= (isset($commande['boisson_id']) && $commande['boisson_id'] == $b['boisson_id']) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($b['boisson_nom'], ENT_QUOTES) ?> —
                    <?= number_format($b['boisson_prix'], 2) ?> €
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <p style="margin-top:1em;">
        <button type="submit">Enregistrer les modifications</button>
        <a href="index.php?section=commande">Annuler</a>
    </p>
</form>

<?php include __DIR__ . '/footer.php'; ?>

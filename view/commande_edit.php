<?php include __DIR__ . '/header.php'; ?>

<h1>Modifier la commande #<?= (int)($commande['order_id'] ?? 0) ?></h1>
<p><a href="index.php?section=commande">← Retour à la liste</a></p>

<form
    method="post"
    action="index.php?section=commande&action=edit&id=<?= (int)($commande['order_id'] ?? 0) ?>"
>
    <!-- CSRF -->
    <input
        type="hidden"
        name="csrf"
        value="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES) ?>"
    >

    <!-- Date de commande -->
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

    <!-- Heure de livraison -->
    <div style="margin-top:1em;">
        <label for="order_heure_livraison">Heure de livraison :</label><br>
        <input
            type="time"
            id="order_heure_livraison"
            name="order_heure_livraison"
            value="<?= htmlspecialchars($commande['order_heure_livraison'] ?? '', ENT_QUOTES) ?>"
        >
    </div>

    <!-- Type de commande -->
    <div style="margin-top:1em;">
        <label>Type de commande :</label><br>
        <label>
            <input
                type="radio"
                name="order_type"
                value="sur_place"
                <?= (($commande['order_type'] ?? '') === 'sur_place') ? 'checked' : '' ?>
            > Sur place
        </label>
        <label style="margin-left:1em;">
            <input
                type="radio"
                name="order_type"
                value="a_emporter"
                <?= (($commande['order_type'] ?? '') === 'a_emporter') ? 'checked' : '' ?>
            > Livraison
        </label>
    </div>

    <!-- Statut -->
    <div style="margin-top:1em;">
        <label for="order_statut_commande">Statut :</label><br>
        <select id="order_statut_commande" name="order_statut_commande" required>
            <?php if (($commande['order_type'] ?? '') === 'sur_place'): ?>
                <option value="en_preparation"   <?= ($commande['order_statut_commande']==='en_preparation')   ? 'selected' : '' ?>>En cours de préparation</option>
                <option value="pret"             <?= ($commande['order_statut_commande']==='pret')             ? 'selected' : '' ?>>Commande prête</option>
            <?php else: /* a_emporter */ ?>
                <option value="en_livraison"     <?= ($commande['order_statut_commande']==='en_livraison')     ? 'selected' : '' ?>>En livraison</option>
                <option value="livre"            <?= ($commande['order_statut_commande']==='livre')            ? 'selected' : '' ?>>Livrée</option>
            <?php endif; ?>
        </select>
    </div>

    <!-- Numéro de ticket -->
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

    <!-- Utilisateur -->
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

    <!-- Pré‑remplissage des quantités de menus -->
    <?php
    $menuQty = [];
    foreach ($menusParCommande as $m) {
        $menuQty[(int)$m['menu_id']] = (int)$m['order_menu_quantite'];
    }
    ?>
    <fieldset style="margin-top:1em;">
        <legend>Menus (quantité)</legend>
        <?php foreach ($menus as $menu): 
            $qty = $menuQty[(int)$menu['menu_id']] ?? 0;
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

    <!-- Pré‑remplissage des quantités de produits -->
    <?php
    $prodQty = [];
    foreach ($produitsParCommande as $p) {
        $prodQty[(int)$p['product_id']] = (int)$p['order_product_quantite'];
    }
    ?>
    <fieldset style="margin-top:1em;">
        <legend>Produits supplémentaires (quantité)</legend>
        <?php foreach ($produits as $prod):
            $pq = $prodQty[(int)$prod['product_id']] ?? 0;
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

    <!-- Boisson -->
    <div style="margin-top:1em;">
        <label for="boisson_id">Boisson au choix :</label><br>
        <select id="boisson_id" name="boisson_id">
            <option value="">-- Aucune --</option>
            <?php foreach ($boissons as $b): ?>
                <option
                    value="<?= (int)$b['boisson_id'] ?>"
                    <?= (($commande['boisson_id'] ?? '') == $b['boisson_id']) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($b['boisson_nom'], ENT_QUOTES) ?> —
                    <?= number_format($b['boisson_prix'], 2) ?> €
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Actions -->
    <p style="margin-top:1em;">
        <button type="submit">Enregistrer les modifications</button>
        <a href="index.php?section=commande">Annuler</a>
    </p>
</form>

<?php include __DIR__ . '/footer.php'; ?>

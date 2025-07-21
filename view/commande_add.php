<?php include __DIR__ . '/header.php'; ?>

<h1>Passer une commande</h1>
<p><a href="index.php?section=commande">Retour à la liste</a></p>

<form method="post" action="index.php?section=commande&action=add">
    <!-- Token CSRF -->
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">

    <div>
        <label for="order_date_commande">Date de commande :</label><br>
        <input
            type="date"
            id="order_date_commande"
            name="order_date_commande"
            required
            value="<?= date('Y-m-d') ?>"
        >
    </div>

    <div style="margin-top:1em;">
        <label for="order_heure_livraison">Heure de livraison :</label><br>
        <input
            type="time"
            id="order_heure_livraison"
            name="order_heure_livraison"
        >
    </div>

    <div style="margin-top:1em;">
        <label for="order_statut_commande">Statut :</label><br>
        <input
            type="text"
            id="order_statut_commande"
            name="order_statut_commande"
            required
            value="En cours"
        >
    </div>

    <div style="margin-top:1em;">
        <label for="order_numero_ticket">Numéro de ticket :</label><br>
        <input
            type="text"
            id="order_numero_ticket"
            name="order_numero_ticket"
            required
        >
    </div>

    <div style="margin-top:1em;">
        <label>Utilisateur :</label><br>
        <!-- On récupère l'ID de l'utilisateur connecté -->
        <input
            type="hidden"
            name="user_id"
            value="<?= (int)$_SESSION['user']['user_id'] ?>"
        >
        <span>
            Utilisateur #<?= (int)$_SESSION['user']['user_id'] ?>
            — <?= htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES) ?>
        </span>
    </div>

    <fieldset style="margin-top:1em;">
        <legend>Menus (quantité)</legend>
        <?php foreach ($menus as $menu): ?>
            <div>
                <label>
                    <?= htmlspecialchars($menu['menu_nom'], ENT_QUOTES) ?> —
                    <?= number_format($menu['menu_prix'], 2) ?> €
                    :
                    <input
                        type="number"
                        name="menus[<?= (int)$menu['menu_id'] ?>]"
                        value="0"
                        min="0"
                        style="width:60px;"
                    >
                </label>
            </div>
        <?php endforeach; ?>
    </fieldset>

    <fieldset style="margin-top:1em;">
        <legend>Produits supplémentaires (quantité)</legend>
        <?php foreach ($produits as $prod): ?>
            <div>
                <label>
                    <?= htmlspecialchars($prod['product_nom'], ENT_QUOTES) ?> —
                    <?= number_format($prod['product_prix'], 2) ?> €
                    :
                    <input
                        type="number"
                        name="produits[<?= (int)$prod['product_id'] ?>]"
                        value="0"
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
                <option value="<?= (int)$b['boisson_id'] ?>">
                    <?= htmlspecialchars($b['boisson_nom'], ENT_QUOTES) ?> —
                    <?= number_format($b['boisson_prix'], 2) ?> €
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <p style="margin-top:1em;">
        <button type="submit">Passer la commande</button>
        <a href="index.php?section=commande">Annuler</a>
    </p>
</form>

<?php include __DIR__ . '/footer.php'; ?>

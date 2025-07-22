<?php include __DIR__ . '/header.php'; ?>

<h1>Passer une commande</h1>
<p><a href="index.php?section=commande">← Retour à la liste</a></p>

<form method="post" action="index.php?section=commande&action=add">
    <!-- CSRF -->
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES) ?>">

    <!-- Date de commande -->
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

    <!-- Heure de livraison (optionnelle) -->
    <div style="margin-top:1em;">
        <label for="order_heure_livraison">Heure de livraison :</label><br>
        <input
            type="time"
            id="order_heure_livraison"
            name="order_heure_livraison"
        >
    </div>

    <!-- Type de commande -->
    <div style="margin-top:1em;">
        <label>Type de commande :</label><br>
        <label>
            <input
                type="radio"
                name="order_type"
                value="sur_place"
                checked
            > Sur place
        </label>
        <label style="margin-left:1em;">
            <input
                type="radio"
                name="order_type"
                value="a_emporter"
            > Livraison
        </label>
    </div>

    <!-- Statut (lié au type ci‑dessus) -->
    <div style="margin-top:1em;">
        <label for="order_statut_commande">Statut :</label><br>
        <select id="order_statut_commande" name="order_statut_commande" required>
            <optgroup label="Sur place" data-type="sur_place">
                <option value="en_preparation">En cours de préparation</option>
                <option value="pret">Commande prête</option>
            </optgroup>
            <optgroup label="Livraison" data-type="a_emporter" style="display:none">
                <option value="en_livraison">En livraison</option>
                <option value="livre">Livrée</option>
            </optgroup>
        </select>
    </div>

    <!-- Utilisateur courant -->
    <div style="margin-top:1em;">
        <label>Utilisateur :</label><br>
        <input
            type="hidden"
            name="user_id"
            value="<?= (int)($_SESSION['user']['user_id'] ?? 0) ?>"
        >
        <span>
            #<?= (int)($_SESSION['user']['user_id'] ?? 0) ?>
            — <?= htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES) ?>
        </span>
    </div>

    <!-- Sélection des menus -->
    <fieldset style="margin-top:1em;">
        <legend>Menus (quantité)</legend>
        <?php foreach ($menus as $menu): ?>
            <div>
                <label>
                    <?= htmlspecialchars($menu['menu_nom'], ENT_QUOTES) ?> —
                    <?= number_format($menu['menu_prix'], 2) ?> € :
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

    <!-- Sélection des produits supplémentaires -->
    <fieldset style="margin-top:1em;">
        <legend>Produits supplémentaires (quantité)</legend>
        <?php foreach ($produits as $prod): ?>
            <div>
                <label>
                    <?= htmlspecialchars($prod['product_nom'], ENT_QUOTES) ?> —
                    <?= number_format($prod['product_prix'], 2) ?> € :
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

    <!-- Sélection de la boisson -->
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

    <!-- Actions -->
    <p style="margin-top:1em;">
        <button type="submit">Passer la commande</button>
        <a href="index.php?section=commande">Annuler</a>
    </p>
</form>

<script>
// When the order_type changes, show the matching optgroup and hide the other
document.querySelectorAll('input[name="order_type"]').forEach(radio => {
    radio.addEventListener('change', () => {
        const sel = document.getElementById('order_statut_commande');
        const type = radio.value;
        sel.querySelectorAll('optgroup').forEach(og => {
            og.style.display = og.getAttribute('data-type') === type ? '' : 'none';
        });
        // pick the first option in the visible group
        const first = sel.querySelector(`optgroup[data-type="${type}"] option`);
        if (first) first.selected = true;
    });
});
// fire on load to set initial state
window.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('input[name="order_type"]:checked');
    if (checked) checked.dispatchEvent(new Event('change'));
});
</script>

<?php include __DIR__ . '/footer.php'; ?>

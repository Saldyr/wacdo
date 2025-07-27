<?php include __DIR__ . '/header.php'; ?>

<h1>Passer une commande</h1>
<p><a href="index.php?section=commande">← Retour à la liste</a></p>

<form method="post" action="index.php?section=commande&action=add">
    <!-- CSRF -->
    <input type="hidden" name="csrf"
        value="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES) ?>">

    <!-- Date de commande -->
    <div>
        <label for="order_date_commande">Date de commande :</label><br>
        <input
            type="date"
            id="order_date_commande"
            name="order_date_commande"
            required
            value="<?= htmlspecialchars(date('Y-m-d'), ENT_QUOTES) ?>">
    </div>

    <!-- Heure de livraison (optionnelle) -->
    <div style="margin-top:1em;">
        <label for="order_heure_livraison">Heure de livraison :</label><br>
        <input
            type="time"
            id="order_heure_livraison"
            name="order_heure_livraison">
    </div>

    <!-- Type de commande -->
    <div style="margin-top:1em;">
        <label>Type de commande :</label><br>
        <?php foreach (['sur_place' => 'Sur place', 'a_emporter' => 'À emporter', 'livraison' => 'Livraison'] as $val => $label): ?>
            <label style="margin-right:1em;">
                <input
                    type="radio"
                    name="order_type"
                    value="<?= $val ?>"
                    <?= $val === 'sur_place' ? 'checked' : '' ?>>
                <?= $label ?>
            </label>
        <?php endforeach; ?>
    </div>

    <!-- Statut lié au type -->
    <div style="margin-top:1em;">
        <label for="order_statut_commande">Statut :</label><br>
        <select id="order_statut_commande" name="order_statut_commande" required>
            <optgroup label="Sur place" data-type="sur_place">
                <option value="en_preparation">En cours de préparation</option>
                <option value="pret">Commande prête</option>
            </optgroup>
            <optgroup label="À emporter" data-type="a_emporter" style="display:none">
                <option value="en_preparation">En cours de préparation</option>
                <option value="pret">Commande prête</option>
            </optgroup>
            <optgroup label="Livraison" data-type="livraison" style="display:none">
                <option value="en_preparation">En cours de préparation</option>
                <option value="en_livraison">En livraison</option>
                <option value="livree">Livrée</option>
            </optgroup>
        </select>
    </div>

    <!-- Utilisateur courant -->
    <div style="margin-top:1em;">
        <label>Utilisateur :</label><br>
        <input type="hidden"
            name="user_id"
            value="<?= (int)($_SESSION['user']['user_id'] ?? 0) ?>">
        <span>#<?= (int)($_SESSION['user']['user_id'] ?? 0) ?> —
            <?= htmlspecialchars($_SESSION['user']['name'] ?? '', ENT_QUOTES) ?></span>
    </div>

    <!-- 1) Menus + boissons incluses (gratuites) -->
    <fieldset style="margin-top:1em;">
        <legend>Menus &amp; boissons incluses (gratuites)</legend>
        <?php foreach ($menus as $menu):
            $mid     = (int)$menu['menu_id'];
            $menuQty = (int)($_POST['menus'][$mid] ?? 0);
        ?>
            <div style="margin-bottom:1em;">
                <label style="display:inline-block; width:300px;">
                    <?= htmlspecialchars($menu['menu_nom'], ENT_QUOTES) ?> —
                    <?= number_format($menu['menu_prix'], 2) ?> €
                    <input
                        type="number"
                        name="menus[<?= $mid ?>]"
                        value="<?= $menuQty ?>"
                        min="0"
                        style="width:60px; margin-left:10px;">
                </label>
                <div style="margin-left:320px; margin-top:0.5em;">
                    <strong>Boissons incluses :</strong><br>
                    <?php foreach ($boissons as $b):
                        $bid     = (int)$b['boisson_id'];
                        $freeQty = (int)($_POST['menu_boissons'][$mid][$bid] ?? 0);
                    ?>
                        <label style="display:inline-block; width:200px; margin-right:1em;">
                            <?= htmlspecialchars($b['boisson_nom'], ENT_QUOTES) ?>
                            <input
                                type="number"
                                name="menu_boissons[<?= $mid ?>][<?= $bid ?>]"
                                value="<?= $freeQty ?>"
                                min="0"
                                style="width:50px; margin-left:5px;">
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </fieldset>

    <!-- 2) Produits supplémentaires (quantité) -->
    <?php foreach ($produitsParCategorie as $categorie => $liste): ?>
        <h3><?= htmlspecialchars($categorie, ENT_QUOTES) ?></h3>
        <?php foreach ($liste as $prod):
            $pid = (int)$prod['product_id'];
            $qty = (int)($_POST['produits'][$pid] ?? 0);
        ?>
            <div style="margin-bottom:0.5em;">
                <label>
                    <?= htmlspecialchars($prod['product_nom'], ENT_QUOTES) ?> —
                    <?= number_format($prod['product_prix'], 2) ?> €
                    <input
                        type="number"
                        name="produits[<?= $pid ?>]"
                        min="0"
                        value="<?= $qty ?>"
                        style="width:60px; margin-left:10px;">
                </label>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>


    <!-- 3) Boissons à l'unité (quantité) -->
    <fieldset style="margin-top:1em;">
        <legend>Boissons à l'unité (quantité)</legend>
        <?php foreach ($boissons as $b):
            $bid    = (int)$b['boisson_id'];
            $bQty   = (int)($_POST['boissons_unite'][$bid] ?? 0);
        ?>
            <div style="margin-bottom:0.5em;">
                <label>
                    <?= htmlspecialchars($b['boisson_nom'], ENT_QUOTES) ?> —
                    <?= number_format($b['boisson_prix'], 2) ?> €
                    <input
                        type="number"
                        name="boissons_unite[<?= $bid ?>]"
                        value="<?= $bQty ?>"
                        min="0"
                        style="width:60px; margin-left:10px;">
                </label>
            </div>
        <?php endforeach; ?>
    </fieldset>

    <!-- Actions -->
    <p style="margin-top:1em;">
        <button type="submit">Passer la commande</button>
        <a href="index.php?section=commande">Annuler</a>
    </p>
</form>

<script>
    (function() {
        const statusSelect = document.getElementById('order_statut_commande');
        document.querySelectorAll('input[name="order_type"]').forEach(radio => {
            radio.addEventListener('change', () => {
                const t = radio.value;
                statusSelect.querySelectorAll('optgroup').forEach(og => {
                    og.style.display = og.dataset.type === t ? '' : 'none';
                });
                statusSelect.querySelector(`optgroup[data-type="${t}"] option`).selected = true;
            });
        });
        window.addEventListener('DOMContentLoaded', () => {
            document.querySelector('input[name="order_type"]:checked')
                .dispatchEvent(new Event('change'));
        });
    })();
</script>

<?php include __DIR__ . '/footer.php'; ?>
<?php include __DIR__ . '/header.php'; ?>
<?php $STATUT_LABELS = require __DIR__ . '/../config/statuses.php'; ?>

<h1>Passer une commande</h1>
<p><a href="index.php?section=commande">← Retour à la liste</a></p>

<form method="post" action="index.php?section=commande&action=add">
    <!-- CSRF -->
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES) ?>">

    <!-- Date de commande -->
    <div>
        <label for="order_date_commande">Date de commande :</label><br>
        <input type="date"
            id="order_date_commande"
            name="order_date_commande"
            required
            value="<?= htmlspecialchars(date('Y-m-d'), ENT_QUOTES) ?>">
    </div>

    <!-- Heure de livraison (optionnelle) -->
    <div style="margin-top:1em;">
        <label for="order_heure_livraison">Heure de livraison :</label><br>
        <input type="time" id="order_heure_livraison" name="order_heure_livraison">
    </div>

    <!-- Type de commande -->
    <div style="margin-top:1em;">
        <label>Type de commande :</label><br>
        <?php foreach (['sur_place' => 'Sur place', 'a_emporter' => 'À emporter', 'livraison' => 'Livraison'] as $val => $label): ?>
            <label style="margin-right:1em;">
                <input type="radio"
                    name="order_type"
                    value="<?= $val ?>"
                    <?= $val === 'sur_place' ? 'checked' : '' ?>>
                <?= $label ?>
            </label>
        <?php endforeach; ?>
    </div>

    <!-- Statut lié au type -->
    <div style="margin-top:1em;">
        <label for="order_statut_commande">Statut :</label><br>
        <select id="order_statut_commande" name="order_statut_commande" required>
            <?php foreach ($STATUT_LABELS as $code => $libelle):
                // quels statuts proposer en création ?
                $allowed = match ($code) {
                    'en_preparation', 'pret' => true,
                    'en_livraison'           => ($_POST['order_type'] ?? 'sur_place') === 'livraison',
                    default                  => false,
                };
                if (!$allowed) continue;
                // data-type pour JS
                $dt = in_array($code, ['en_preparation', 'pret'], true) ? 'all'
                    : 'livraison';
            ?>
                <option data-type="<?= $dt ?>"
                    value="<?= $code ?>"
                    <?= (($_POST['order_statut_commande'] ?? '') === $code) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($libelle, ENT_QUOTES) ?>
                </option>
            <?php endforeach; ?>
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
        const radios = document.querySelectorAll('input[name="order_type"]');

        function filterOptions() {
            const type = document.querySelector('input[name="order_type"]:checked').value;
            const wanted = (type === 'livraison') ? 'livraison' : 'all';
            [...statusSelect.options].forEach(opt => {
                // on affiche toujours les data-type="all"
                opt.hidden = (opt.dataset.type !== 'all' && opt.dataset.type !== wanted);
            });
            // coche la première visible
            for (const o of statusSelect.options) {
                if (!o.hidden) {
                    o.selected = true;
                    break;
                }
            }
        }

        radios.forEach(r => r.addEventListener('change', filterOptions));
        window.addEventListener('DOMContentLoaded', filterOptions);
    })();
</script>

<?php include __DIR__ . '/footer.php'; ?>
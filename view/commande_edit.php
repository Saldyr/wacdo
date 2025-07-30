<?php include __DIR__ . '/header.php'; ?>
<?php $STATUT_LABELS = require __DIR__ . '/../config/statuses.php'; ?>

<h1>Modifier la commande #<?= (int)$commande['order_id'] ?></h1>
<p><a href="index.php?section=commande">← Retour à la liste</a></p>

<form method="post"
    action="index.php?section=commande&action=edit&id=<?= (int)$commande['order_id'] ?>">
    <!-- CSRF + ticket caché -->
    <input type="hidden" name="csrf"
        value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">
    <input type="hidden" name="order_numero_ticket"
        value="<?= htmlspecialchars($commande['order_numero_ticket'], ENT_QUOTES) ?>">

    <!-- Date de commande -->
    <div>
        <label for="order_date_commande">Date de commande :</label><br>
        <input type="date"
            id="order_date_commande"
            name="order_date_commande"
            required
            value="<?= htmlspecialchars($commande['order_date_commande'], ENT_QUOTES) ?>">
    </div>

    <!-- Heure de livraison -->
    <div style="margin-top:1em;">
        <label for="order_heure_livraison">Heure de livraison :</label><br>
        <input type="time"
            id="order_heure_livraison"
            name="order_heure_livraison"
            value="<?= htmlspecialchars($commande['order_heure_livraison'] ?? '', ENT_QUOTES) ?>">
    </div>

    <!-- Type de commande -->
    <div style="margin-top:1em;">
        <label>Type de commande :</label><br>
        <?php foreach (['sur_place' => 'Sur place', 'a_emporter' => 'À emporter', 'livraison' => 'Livraison'] as $val => $label): ?>
            <label style="margin-right:1em;">
                <input type="radio"
                    name="order_type"
                    value="<?= $val ?>"
                    <?= $commande['order_type'] === $val ? 'checked' : '' ?>>
                <?= $label ?>
            </label>
        <?php endforeach; ?>
    </div>

    <!-- Statut -->
    <div style="margin-top:1em;">
        <label for="order_statut_commande">Statut :</label><br>
        <select id="order_statut_commande" name="order_statut_commande" required>
            <?php foreach ($STATUT_LABELS as $code => $libelle):
                // on veut toujours pouvoir revenir en préparation
                $allowed = match ($code) {
                    'en_preparation'          => true,                             // TOUJOURS proposé
                    'pret'                    => in_array($commande['order_type'], ['sur_place', 'a_emporter', 'livraison'], true),
                    'servie'                  => in_array($commande['order_type'], ['sur_place', 'a_emporter'], true),
                    'en_livraison', 'livree'  => $commande['order_type'] === 'livraison',
                    default                   => false,
                };
                if (!$allowed) continue;
                // on définit data-type pour le JS de filtrage
                $dataType = in_array($code, ['en_preparation', 'pret', 'servie'], true)
                    ? 'sur_place_a_emporter'
                    : 'livraison';
            ?>
                <option
                    data-type="<?= $dataType ?>"
                    value="<?= $code ?>"
                    <?= $commande['order_statut_commande'] === $code ? 'selected' : '' ?>>
                    <?= htmlspecialchars($libelle, ENT_QUOTES) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>


    <!-- Utilisateur (non modifiable) -->
    <div style="margin-top:1em;">
        <label>Utilisateur :</label><br>
        <input type="hidden" name="user_id"
            value="<?= (int)$commande['user_id'] ?>">
        <span>#<?= (int)$commande['user_id'] ?> —
            <?= htmlspecialchars($_SESSION['user']['name'] ?? '', ENT_QUOTES) ?></span>
    </div>

    <!-- Menus & boissons gratuites -->
    <fieldset style="margin-top:1em;">
        <legend>Menus &amp; boissons incluses (gratuites)</legend>
        <?php foreach ($menus as $menu):
            $mid  = (int)$menu['menu_id'];
            $mQty = $menuQty[$mid] ?? 0;
            $free = $menuFreeChoice[$mid] ?? [];
        ?>
            <div style="margin-bottom:1em;">
                <label style="display:inline-block; width:300px;">
                    <?= htmlspecialchars($menu['menu_nom'], ENT_QUOTES) ?> —
                    <?= number_format($menu['menu_prix'], 2) ?> €
                    <input type="number"
                        name="menus[<?= $mid ?>]"
                        min="0"
                        value="<?= $mQty ?>"
                        style="width:60px; margin-left:10px;">
                </label>
                <div style="margin-left:320px; margin-top:0.5em;">
                    <strong>Boissons gratuites :</strong><br>
                    <?php foreach ($boissons as $b):
                        $bid  = (int)$b['boisson_id'];
                        $fQty = $free[$bid] ?? 0;
                    ?>
                        <label style="display:inline-block; width:200px; margin-right:1em;">
                            <?= htmlspecialchars($b['boisson_nom'], ENT_QUOTES) ?>
                            <input type="number"
                                name="menu_boissons[<?= $mid ?>][<?= $bid ?>]"
                                min="0"
                                value="<?= $fQty ?>"
                                style="width:50px; margin-left:5px;">
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </fieldset>

    <!-- Produits supplémentaires -->
    <fieldset style="margin-top:1em;">
        <legend>Produits supplémentaires (quantité)</legend>

        <?php foreach ($produitsParCategorie as $categorie => $liste): ?>
            <section style="margin-bottom:1.5em;">
                <h4 style="margin-bottom:0.5em;"><?= htmlspecialchars($categorie, ENT_QUOTES) ?></h4>
                <?php foreach ($liste as $prod):
                    $pid  = (int)$prod['product_id'];
                    $pQty = $prodQty[$pid] ?? 0;
                ?>
                    <div style="margin-bottom:0.5em;">
                        <label>
                            <?= htmlspecialchars($prod['product_nom'], ENT_QUOTES) ?> —
                            <?= number_format($prod['product_prix'], 2) ?> €
                            <input
                                type="number"
                                name="produits[<?= $pid ?>]"
                                min="0"
                                value="<?= $pQty ?>"
                                style="width:60px; margin-left:10px;">
                        </label>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php endforeach; ?>
    </fieldset>


    <!-- Boissons à l'unité -->
    <fieldset style="margin-top:1em;">
        <legend>Boissons à l'unité (quantité)</legend>
        <?php foreach ($boissons as $b):
            $bid  = (int)$b['boisson_id'];
            $bQty = $boissonsUniteQty[$bid] ?? 0;
        ?>
            <div style="margin-bottom:0.5em;">
                <label>
                    <?= htmlspecialchars($b['boisson_nom'], ENT_QUOTES) ?> —
                    <?= number_format($b['boisson_prix'], 2) ?> €
                    <input type="number"
                        name="boissons_unite[<?= $bid ?>]"
                        min="0"
                        value="<?= $bQty ?>"
                        style="width:60px; margin-left:10px;">
                </label>
            </div>
        <?php endforeach; ?>
    </fieldset>

    <!-- Actions -->
    <p style="margin-top:1em;">
        <button type="submit">Enregistrer les modifications</button>
        <a href="index.php?section=commande">Annuler</a>
    </p>
</form>

<script>
    (function() {
        const statusSelect = document.getElementById('order_statut_commande');
        const radios = document.querySelectorAll('input[name="order_type"]');

        function filterOptions() {
            const t = document.querySelector('input[name="order_type"]:checked').value;
            const wantedGroup = (t === 'livraison') ? 'livraison' : 'sur_place_a_emporter';

            [...statusSelect.options].forEach(opt => {
                // on affiche toujours "en_preparation", et sinon on vérifie le groupe
                if (opt.value === 'en_preparation') {
                    opt.hidden = false;
                } else {
                    opt.hidden = opt.dataset.type !== wantedGroup;
                }
            });
            // coche la première visible
            for (const opt of statusSelect.options) {
                if (!opt.hidden) {
                    opt.selected = true;
                    break;
                }
            }
        }

        radios.forEach(r => r.addEventListener('change', filterOptions));
        window.addEventListener('DOMContentLoaded', filterOptions);
    })();
</script>


<?php include __DIR__ . '/footer.php'; ?>
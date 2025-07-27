<?php include __DIR__ . '/header.php'; ?>

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
        <label for="order_statut_commande">Statut :</label><br>
        <select id="order_statut_commande"
            name="order_statut_commande"
            required>
            <optgroup label="Sur place" data-type="sur_place">
                <option value="en_preparation"
                    <?= $commande['order_statut_commande'] === 'en_preparation' ? 'selected' : '' ?>>
                    En cours de préparation
                </option>
                <option value="pret"
                    <?= $commande['order_statut_commande'] === 'pret' ? 'selected' : '' ?>>
                    Commande prête
                </option>
            </optgroup>
            <optgroup label="À emporter" data-type="a_emporter" style="display:none">
                <option value="en_preparation"
                    <?= $commande['order_statut_commande'] === 'en_preparation' ? 'selected' : '' ?>>
                    En cours de préparation
                </option>
                <option value="pret"
                    <?= $commande['order_statut_commande'] === 'pret' ? 'selected' : '' ?>>
                    Commande prête
                </option>
            </optgroup>
            <optgroup label="Livraison" data-type="livraison" style="display:none">
                <option value="en_preparation"
                    <?= $commande['order_statut_commande'] === 'en_preparation' ? 'selected' : '' ?>>
                    En cours de préparation
                </option>
                <option value="en_livraison"
                    <?= $commande['order_statut_commande'] === 'en_livraison' ? 'selected' : '' ?>>
                    En livraison
                </option>
                <option value="livree"
                    <?= $commande['order_statut_commande'] === 'livree' ? 'selected' : '' ?>>
                    Livrée
                </option>
            </optgroup>
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
                            style="width:60px; margin-left:10px;"
                        >
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
        const status = document.getElementById('order_statut_commande');
        document.querySelectorAll('input[name="order_type"]').forEach(radio => {
            radio.addEventListener('change', () => {
                const t = radio.value;
                status.querySelectorAll('optgroup').forEach(og => {
                    og.style.display = og.dataset.type === t ? '' : 'none';
                });
                status.querySelector(`optgroup[data-type="${t}"] option`).selected = true;
            });
        });
        window.addEventListener('DOMContentLoaded', () => {
            document.querySelector('input[name="order_type"]:checked')
                .dispatchEvent(new Event('change'));
        });
    })();
</script>

<?php include __DIR__ . '/footer.php'; ?>
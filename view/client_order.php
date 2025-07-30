<?php include __DIR__ . '/header.php'; ?>

<?php if (!empty($_GET['registered'])): ?>
    <p id="flash-message" style="color:green; margin:1em 0;">
        Bonjour <?= htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES) ?>,
        votre compte a bien été créé !
    </p>
<?php endif; ?>

<h1>
    Bonjour <?= htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES) ?>,<br>
    que souhaitez-vous commander aujourd'hui&nbsp;?
</h1>

<section id="panier">
    <h2>Votre panier</h2>

    <form id="order-type-form"
        method="post"
        action="index.php?section=commande&action=checkout"
        style="margin-bottom:1em;">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES) ?>">

        <fieldset>
            <legend>Type de commande</legend>
            <label>
                <input type="radio"
                    name="order_type"
                    value="sur_place"
                    <?= ($_POST['order_type'] ?? 'sur_place') === 'sur_place' ? 'checked' : '' ?>>
                Sur place
            </label>
            &nbsp;
            <label>
                <input type="radio"
                    name="order_type"
                    value="a_emporter"
                    <?= ($_POST['order_type'] ?? '') === 'a_emporter' ? 'checked' : '' ?>>
                À emporter
            </label>
            &nbsp;
            <label>
                <input type="radio"
                    name="order_type"
                    value="livraison"
                    <?= ($_POST['order_type'] ?? '') === 'livraison' ? 'checked' : '' ?>>
                Livraison
            </label>
        </fieldset>

        <div id="cart-content">
            <?php if (empty($cartDetail['items'])): ?>
                <p>Votre panier est vide.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($cartDetail['items'] as $i): ?>
                        <li>
                            <?= htmlspecialchars($i['name'], ENT_QUOTES) ?>
                            <?php if (!empty($i['boisson_name'])): ?>
                                <br>
                                <small>Boisson offerte : <?= htmlspecialchars($i['boisson_name'], ENT_QUOTES) ?></small>
                            <?php endif; ?>
                            <br>
                            — Qté : <?= $i['qty'] ?> × <?= number_format($i['price'], 2, ',', ' ') ?> €
                            = <?= number_format($i['subtotal'], 2, ',', ' ') ?> €
                            <button
                                data-action="removeCart"
                                data-type="<?= htmlspecialchars($i['type'], ENT_QUOTES) ?>"
                                data-id="<?= (int)$i['id'] ?>">
                                [–]
                            </button>
                            <button
                                data-action="addCart"
                                data-type="<?= htmlspecialchars($i['type'], ENT_QUOTES) ?>"
                                data-id="<?= (int)$i['id'] ?>">
                                [+]
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <p><strong>Total : <?= number_format($cartDetail['total'], 2, ',', ' ') ?> €</strong></p>

                <button type="submit">Valider la commande</button>
            <?php endif; ?>
        </div>
    </form>
</section>

<div class="catalogue">
    <?php if (!empty($menus)): ?>
        <h2>Nos menus</h2>
        <ul>
            <?php foreach ($menus as $menu):
                $mid = (int)$menu['menu_id'];
            ?>
                <li>
                    <h3><?= htmlspecialchars($menu['menu_nom'], ENT_QUOTES) ?></h3>
                    <p><?= htmlspecialchars($menu['menu_description'], ENT_QUOTES) ?></p>
                    <p>Prix : <?= number_format($menu['menu_prix'], 2, ',', ' ') ?> €</p>

                    <form method="post"
                        action="index.php?section=commande&action=addCart&ajax=0"
                        style="margin-top:0.5em;">
                        <input type="hidden" name="type" value="menus">
                        <input type="hidden" name="id" value="<?= $mid ?>">
                        <label>
                            Boisson offerte :
                            <select name="boisson_id" required>
                                <option value="">— Choisir —</option>
                                <?php foreach ($boissons as $b): ?>
                                    <option value="<?= (int)$b['boisson_id'] ?>">
                                        <?= htmlspecialchars($b['boisson_nom'], ENT_QUOTES) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <button type="submit">Ajouter au panier</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($produitsParCategorie)): ?>
        <h2>Nos produits à la carte</h2>
        <?php foreach ($produitsParCategorie as $catNom => $liste): ?>
            <h3><?= htmlspecialchars($catNom, ENT_QUOTES) ?></h3>
            <ul>
                <?php foreach ($liste as $produit): ?>
                    <li>
                        <h4><?= htmlspecialchars($produit['product_nom'], ENT_QUOTES) ?></h4>
                        <p>Prix : <?= number_format($produit['product_prix'], 2, ',', ' ') ?> €</p>
                        <button data-action="addCart"
                            data-type="produits"
                            data-id="<?= (int)$produit['product_id'] ?>">
                            Ajouter au panier
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($boissons)): ?>
        <h2>Nos boissons</h2>
        <ul>
            <?php foreach ($boissons as $boisson): ?>
                <li>
                    <h3><?= htmlspecialchars($boisson['boisson_nom'], ENT_QUOTES) ?></h3>
                    <p>Prix : <?= number_format($boisson['boisson_prix'], 2, ',', ' ') ?> €</p>
                    <button data-action="addCart"
                        data-type="boissons"
                        data-id="<?= (int)$boisson['boisson_id'] ?>">
                        Ajouter au panier
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cartContent = document.getElementById('cart-content');

        async function refreshCart(type, id, boissonId = null, action = 'addCart') {
            // 1) Construction de l’URL AJAX
            const params = new URLSearchParams({
                section: 'commande',
                action,
                type,
                id,
                ajax: 1
            });
            if (boissonId !== null) {
                params.set('boisson_id', boissonId);
            }

            // 2) Requête GET JSON
            const res = await fetch(`index.php?${params}`);
            if (!res.ok) return console.error('Fetch failed', res.status);
            const data = await res.json();

            // 3) Reconstruction du HTML du panier
            let html = '<ul>';
            data.items.forEach(i => {
                html += `<li>
                    ${i.name}`;
                if (i.boisson_name) {
                    html += `<br><small>Boisson offerte : ${i.boisson_name}</small>`;
                }
                html += `<br>— Qté : ${i.qty} × ${parseFloat(i.price).toFixed(2)} € 
                        = ${parseFloat(i.subtotal).toFixed(2)} €
                        <button data-action="removeCart" data-type="${i.type}" data-id="${i.id}">[–]</button>
                        <button data-action="addCart"    data-type="${i.type}" data-id="${i.id}">[+]</button>
                    </li>`;
            });
            html += `</ul>
                <p><strong>Total : ${parseFloat(data.total).toFixed(2)} €</strong></p>
                <button type="submit">Valider la commande</button>`;

            cartContent.innerHTML = html;
        }

        // 4) Interception du submit des FORMULAIRES (menus avec boisson)
        document.body.addEventListener('submit', e => {
            const form = e.target.closest('form[action*="addCart"]');
            if (!form) return;
            e.preventDefault();

            const type = form.querySelector('input[name="type"]').value;
            const id = form.querySelector('input[name="id"]').value;
            const boissonEl = form.querySelector('select[name="boisson_id"]');
            const boissonId = boissonEl ? boissonEl.value : null;

            refreshCart(type, id, boissonId, 'addCart');
        });

        // 5) Interception des CLICS sur tous les boutons [+] et [–]
        document.body.addEventListener('click', e => {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;
            e.preventDefault();

            const action = btn.dataset.action;
            const type = btn.dataset.type;
            const id = btn.dataset.id;

            refreshCart(type, id, null, action);
        });
    });
</script>

<?php include __DIR__ . '/footer.php'; ?>
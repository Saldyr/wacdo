<?php include __DIR__ . '/header.php'; ?>

<?php if (!empty($_GET['registered'])): ?>
    <p id="flash-message" style="color:green; margin:1em 0;">
        Bonjour <?= htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES) ?>, votre compte a bien été créé !
    </p>
<?php endif; ?>

<h1>
    Bonjour <?= htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES) ?>,<br>
    que souhaitez-vous commander aujourd'hui&nbsp;?
</h1>

<!-- Section Panier -->
<section id="panier">
    <h2>Votre panier</h2>
    <?php if (empty($cartDetail['items'])): ?>
        <p>Votre panier est vide.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($cartDetail['items'] as $i): ?>
                <li>
                    <?= htmlspecialchars($i['name'], ENT_QUOTES) ?>
                    — Qté : <?= $i['qty'] ?> × <?= number_format($i['price'], 2, ',', ' ') ?> €
                    = <?= number_format($i['subtotal'], 2, ',', ' ') ?> €
                    <button data-action="removeCart" data-type="<?= $i['type'] ?>" data-id="<?= $i['id'] ?>">[–]</button>
                    <button data-action="addCart"    data-type="<?= $i['type'] ?>" data-id="<?= $i['id'] ?>">[+]</button>
                </li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Total : <?= number_format($cartDetail['total'], 2, ',', ' ') ?> €</strong></p>
        <form method="post" action="index.php?section=commande&action=checkout" style="margin-top:1em;">
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
            <button type="submit">Valider la commande</button>
        </form>
    <?php endif; ?>
</section>

<!-- Section Catalogue -->
<div class="catalogue">
    <?php if (!empty($menus)): ?>
        <h2>Nos menus</h2>
        <ul>
            <?php foreach ($menus as $menu): ?>
                <li>
                    <h3><?= htmlspecialchars($menu['menu_nom'], ENT_QUOTES) ?></h3>
                    <p><?= htmlspecialchars($menu['menu_description'], ENT_QUOTES) ?></p>
                    <p>Prix : <?= number_format($menu['menu_prix'], 2, ',', ' ') ?> €</p>
                    <button data-action="addCart" data-type="menus" data-id="<?= $menu['menu_id'] ?>">
                        Ajouter au panier
                    </button>
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
                        <p>Prix : <?= number_format($produit['product_prix'], 2, ',', ' ') ?> €</p>
                        <button data-action="addCart" data-type="produits" data-id="<?= $produit['product_id'] ?>">
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
                    <p>Prix : <?= number_format($boisson['boisson_prix'], 2, ',', ' ') ?> €</p>
                    <button data-action="addCart" data-type="boissons" data-id="<?= $boisson['boisson_id'] ?>">
                        Ajouter au panier
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<!-- Script AJAX pour mise à jour live du panier -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const panier = document.getElementById('panier');

    async function refreshCart(action, type, id) {
        const res  = await fetch(
            `index.php?section=commande&action=${action}&type=${type}&id=${id}&ajax=1`
        );
        if (!res.ok) return;
        const data = await res.json();

        let html = '<h2>Votre panier</h2>';
        if (data.items.length === 0) {
            html += '<p>Votre panier est vide.</p>';
        } else {
            html += '<ul>';
            data.items.forEach(i => {
                const price    = parseFloat(i.price);
                const subtotal = parseFloat(i.subtotal);
                html += `<li>
                    ${i.name} — Qté : ${i.qty} × ${price.toFixed(2)} € = ${subtotal.toFixed(2)} €
                    <button data-action="removeCart" data-type="${i.type}" data-id="${i.id}">[–]</button>
                    <button data-action="addCart"    data-type="${i.type}" data-id="${i.id}">[+]</button>
                </li>`;
            });
            html += `</ul><p><strong>Total : ${parseFloat(data.total).toFixed(2)} €</strong></p>
            <form method="post" action="index.php?section=commande&action=checkout" style="margin-top:1em;">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                <button type="submit">Valider la commande</button>
            </form>`;
        }
        panier.innerHTML = html;
    }

    // Délégation globale pour capter add/remove
    document.body.addEventListener('click', e => {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;
        const act = btn.dataset.action;
        if (act === 'addCart' || act === 'removeCart') {
            e.preventDefault();
            refreshCart(act, btn.dataset.type, btn.dataset.id);
        }
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
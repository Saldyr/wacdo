<?php include __DIR__ . '/header.php'; ?>

<h1>Mes commandes</h1>

<?php if (empty($all)): ?>
    <p>Vous n’avez encore passé aucune commande.</p>
<?php else: ?>
    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border:1px solid #ccc; padding:8px;"># Commande</th>
                <th style="border:1px solid #ccc; padding:8px;">Date</th>
                <th style="border:1px solid #ccc; padding:8px;">Type</th>
                <th style="border:1px solid #ccc; padding:8px;">Statut</th>
                <th style="border:1px solid #ccc; padding:8px;">Détails</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($all as $cmd): ?>
            <tr>
                <td style="border:1px solid #ccc; padding:8px;">
                    <?= htmlspecialchars($cmd['order_numero_ticket'], ENT_QUOTES) ?>
                </td>
                <td style="border:1px solid #ccc; padding:8px;">
                    <?= htmlspecialchars($cmd['order_date_commande'], ENT_QUOTES) ?>
                </td>
                <td style="border:1px solid #ccc; padding:8px;">
                    <?= htmlspecialchars($cmd['order_type'], ENT_QUOTES) ?>
                </td>
                <td style="border:1px solid #ccc; padding:8px;">
                    <?= htmlspecialchars($cmd['order_statut_commande'], ENT_QUOTES) ?>
                </td>
                <td style="border:1px solid #ccc; padding:8px; text-align:center;">
                    <a href="index.php?section=commande&action=view&id=<?= (int)$cmd['order_id'] ?>">
                        Voir
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>

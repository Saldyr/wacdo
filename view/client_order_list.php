<?php include __DIR__ . '/header.php'; ?>
<?php $STATUT_LABELS = require __DIR__ . '/../config/statuses.php'; ?>

<h1>Mes commandes</h1>

<?php if (empty($commandes)): ?>
    <p>Vous n'avez encore passé aucune commande.</p>
<?php else: ?>
    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border:1px solid #ccc; padding:8px;"># Commande</th>
                <th style="border:1px solid #ccc; padding:8px;">Date</th>
                <th style="border:1px solid #ccc; padding:8px;">Passée le</th>
                <th style="border:1px solid #ccc; padding:8px;">Type</th>
                <th style="border:1px solid #ccc; padding:8px;">Statut</th>
                <th style="border:1px solid #ccc; padding:8px;">Détails</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commandes as $cmd): ?>
                <tr>
                    <!-- Ticket -->
                    <td style="border:1px solid #ccc; padding:8px;">
                        <?= htmlspecialchars($cmd['order_numero_ticket'], ENT_QUOTES) ?>
                    </td>

                    <!-- Date de la commande -->
                    <td style="border:1px solid #ccc; padding:8px;">
                        <?= htmlspecialchars($cmd['order_date_commande'], ENT_QUOTES) ?>
                    </td>

                    <!-- Horodatage de création -->
                    <td style="border:1px solid #ccc; padding:8px;">
                        <?= htmlspecialchars($cmd['order_created_at'] ?? '--', ENT_QUOTES) ?>
                    </td>

                    <!-- Type de commande -->
                    <td style="border:1px solid #ccc; padding:8px;">
                        <?php
                        switch ($cmd['order_type']) {
                            case 'a_emporter':
                                echo 'À emporter';
                                break;
                            case 'livraison':
                                echo 'Livraison';
                                break;
                            default:
                                echo 'Sur place';
                        }
                        ?>
                    </td>

                    <!-- Statut -->
                    <td style="border:1px solid #ccc; padding:8px;">
                        <?= htmlspecialchars($STATUT_LABELS[$cmd['order_statut_commande']] ?? $cmd['order_statut_commande'], ENT_QUOTES) ?>
                    </td>

                    <!-- Lien vers le détail -->
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
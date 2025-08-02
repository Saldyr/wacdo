<?php include __DIR__ . '/header.php'; ?>

<h1>Historique des commandes</h1>

<form method="get" action="index.php" class="filters" style="margin-bottom:1em;">
    <input type="hidden" name="section" value="commande">
    <input type="hidden" name="action" value="history">
    <label>Du <input type="date" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '', ENT_QUOTES) ?>"></label>
    <label>Au <input type="date" name="to" value="<?= htmlspecialchars($_GET['to'] ?? '', ENT_QUOTES) ?>"></label>
    <button type="submit">Filtrer</button>
</form>

<?php if (empty($commandes)): ?>
    <p>Aucune commande dans l'historique.</p>
<?php else: ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Commande</th>
                <th>Date</th>
                <th>Créée le</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Client</th>
                <?php if ($role === 4): ?>
                    <th>Actions</th>
                <?php else: ?>
                    <th>Détails</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commandes as $c): ?>
                <tr>
                    <td><?= (int)$c['order_id'] ?></td>
                    <td><?= htmlspecialchars($c['order_date_commande'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($c['order_created_at'] ?? '--', ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($c['order_type'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($STATUT_LABELS[$c['order_statut_commande']] ?? $c['order_statut_commande'], ENT_QUOTES) ?></td>
                    <td><?= (int)$c['user_id'] ?></td>
                    <?php if ($role === 4): ?>
                        <td>
                            <a href="index.php?section=commande&action=view&id=<?= (int)$c['order_id'] ?>">Détails</a>
                        </td>
                    <?php else: ?>
                        <td>
                            <a href="index.php?section=commande&action=view&id=<?= (int)$c['order_id'] ?>">Voir</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p style="margin-top:2em;"><a href="index.php?section=commande">← Retour au back-office</a></p>

<?php include __DIR__ . '/footer.php'; ?>
<?php include __DIR__ . '/header.php'; ?>

<h1>Boissons</h1>
<p><a href="index.php?section=boisson&action=add">Ajouter une boisson</a></p>

<?php if (empty($boissons)): ?>
    <p>Aucune boisson enregistrée.</p>
<?php else: ?>
    <ul>
        <?php foreach ($boissons as $b): ?>
            <li>
                <?= htmlspecialchars($b['boisson_nom'], ENT_QUOTES) ?> —
                <?= number_format($b['boisson_prix'], 2) ?> €

                <!-- Lien Modifier -->
                <a
                    href="index.php?section=boisson&action=edit&id=<?= (int)$b['boisson_id'] ?>">Modifier</a>

                <!-- Formulaire POST pour la suppression -->
                <form
                    method="post"
                    action="index.php?section=boisson&action=delete"
                    style="display:inline">
                    <input type="hidden" name="id" value="<?= (int)$b['boisson_id'] ?>">
                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                    <button
                        type="submit"
                        onclick="return confirm('Supprimer cette boisson ?')">Supprimer</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><a href="index.php">Retour à l'accueil</a></p>

<?php include __DIR__ . '/footer.php'; ?>
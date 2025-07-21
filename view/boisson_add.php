<?php include __DIR__ . '/header.php'; ?>

<h1>Ajouter une boisson</h1>
<p><a href="index.php?section=boisson">Retour à la liste</a></p>

<form method="post" action="index.php?section=boisson&action=add">
    <!-- Token CSRF -->
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

    <div>
        <label for="boisson_nom">Nom :</label><br>
        <input 
            type="text" 
            id="boisson_nom" 
            name="boisson_nom" 
            required
        >
    </div>

    <div style="margin-top:1em;">
        <label for="boisson_prix">Prix (€) :</label><br>
        <input 
            type="number" 
            step="0.01" 
            id="boisson_prix" 
            name="boisson_prix" 
            required
        >
    </div>

    <div style="margin-top:1em;">
        <label>
            <input
                type="checkbox"
                name="boisson_disponibilite"
                value="1"
                checked
            >
            Disponible
        </label>
    </div>

    <p style="margin-top:1em;">
        <button type="submit">Ajouter</button>
        <a href="index.php?section=boisson">Annuler</a>
    </p>
</form>

<?php include __DIR__ . '/footer.php'; ?>

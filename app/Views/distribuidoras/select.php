<?php foreach($distribuidoras as $distribuidora): ?>
    <option value="<?= htmlspecialchars($distribuidora['id_distribuidora']) ?>">
        <?= htmlspecialchars($distribuidora['nome_distribuidora']) ?>
    </option>
<?php endforeach; ?>
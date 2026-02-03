<?php foreach($sindicos as $sindico): ?>
    <option value="<?= htmlspecialchars($sindico['id_sindico']) ?>">
        <?= htmlspecialchars($sindico['nome']) ?>
    </option>
<?php endforeach; ?>
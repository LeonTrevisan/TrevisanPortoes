<?php foreach($clientes as $cliente): ?>
    <option value="<?= htmlspecialchars($cliente['id_cliente']) ?>">
        <?= htmlspecialchars($cliente['nome']) ?> (<?= htmlspecialchars($cliente['tipo_cliente']) ?>)
    </option>
<?php endforeach; ?>
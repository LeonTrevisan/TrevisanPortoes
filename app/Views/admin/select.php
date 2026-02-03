<?php foreach($admins as $admin): ?>
    <option value="<?= htmlspecialchars($admin['id_admin']) ?>">
        <?= htmlspecialchars($admin['nome']) ?>
    </option>
<?php endforeach; ?>
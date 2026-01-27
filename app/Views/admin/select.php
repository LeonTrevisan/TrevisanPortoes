<?php foreach($admins as $admin): ?>
    <option value="<?= htmlspecialchars($admin['id']) ?>">
        <?= htmlspecialchars($admin['nome']) ?>
    </option>
<?php endforeach; ?>
<?php foreach ($admins as $admin): ?>
<tr>
    <td><?= $admin['nome'] ?></td>
    <td><?= formatarTelefone($admin['telefone']) ?></td>
    <td><?= $admin['email'] ?></td>
    <td>
        <div class="action-buttons">
            <button onclick="editarAdmin(<?= $admin['id_admin'] ?>)">Editar</button>

            <?php if ($admin['deleted_at'] === null): ?>
                <button onclick="alterarStatus(<?= $admin['id_admin'] ?>, 'admin', 'desativar')">Desativar</button>
            <?php else: ?>
                <button onclick="alterarStatus(<?= $admin['id_admin'] ?>, 'admin', 'ativar')">Ativar</button>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php endforeach; ?>

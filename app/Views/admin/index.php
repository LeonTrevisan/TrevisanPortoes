<?php foreach ($admins as $admin): ?>
<tr>
    <td><?= $admin['nome'] ?></td>
    <td><?= formatarTelefone($admin['telefone']) ?></td>
    <td><?= $admin['email'] ?></td>
    <td>
        <div class="action-buttons">
            <button onclick="editarAdmin(<?= $admin['id_admin'] ?>)">Editar</button>

            <?php if ($admin['deleted_at'] === null): ?>
                <form method="POST" action="/desativar">
                    <input type="hidden" name="tipo" value="cliente">
                    <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
                    <button type="submit">Desativar</button>
                </form>
            <?php else: ?>
                <form method="POST" action="/ativar">
                    <input type="hidden" name="tipo" value="cliente">
                    <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
                    <button type="submit">Reativar</button>
                </form>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php endforeach; ?>

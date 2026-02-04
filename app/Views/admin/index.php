<?php foreach ($admins as $admin): ?>
<tr>
    <td><?= $admin['nome'] ?></td>
    <td><?= formatarTelefone($admin['telefone']) ?></td>
    <td><?= $admin['email'] ?></td>
    <td>
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="editarAdmin(<?= $admin['id_admin'] ?>)">Editar</button>
            <button class="btn btn-info" onclick="verFichaAdmin(<?= $admin['id_admin'] ?>)">Ver Ficha</button>

            <?php if ($admin['deleted_at'] === null): ?>
                <form method="POST" action="<?= $baseUrl ?>/softDelete/desativar">
                    <input type="hidden" name="tabela" value="tb_admin_cond">
                    <input type="hidden" name="id" value="<?= $admin['id_admin'] ?>">
                    <button class="btn btn-danger" type="submit">Desativar</button>
                </form>
            <?php else: ?>
                <form method="POST" action="<?= $baseUrl ?>/softDelete/reativar">
                    <input type="hidden" name="tabela" value="tb_admin_cond">
                    <input type="hidden" name="id" value="<?= $admin['id_admin'] ?>">
                    <button class="btn btn-success" type="submit">Reativar</button>
                </form>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php endforeach; ?>

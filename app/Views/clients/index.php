<?php foreach ($clientes as $cliente): ?>
<tr>
    <td><?= $cliente['nome'] ?></td>
    <td><?= $cliente['tipo_cliente'] ?></td>
    <td><?= formatarTelefone($cliente['telefone']) ?></td>
    <td>
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="editarCliente(<?= $cliente['id_cliente'] ?>)">Editar</button>
            <button class="btn btn-info" onclick="verFichaCliente(<?= $cliente['id_cliente'] ?>)">Ver Ficha</button>

            <?php if ($cliente['deleted_at'] === null): ?>
                <form method="POST" action="/softDelete/desativar">
                    <input type="hidden" name="tabela" value="tb_cliente">
                    <input type="hidden" name="id" value="<?= $cliente['id_cliente'] ?>">
                    <button class="btn btn-danger" type="submit">Desativar</button>
                </form>
            <?php else: ?>
                <form method="POST" action="/softDelete/reativar">
                    <input type="hidden" name="tabela" value="tb_cliente">
                    <input type="hidden" name="id" value="<?= $cliente['id_cliente'] ?>">
                    <button class="btn btn-success" type="submit">Reativar</button>
                </form>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php endforeach; ?>

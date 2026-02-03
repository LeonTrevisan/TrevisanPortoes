<?php foreach ($sindicos as $sindico): ?>
<tr>
    <td><?= $sindico['nome'] ?></td>
    <td><?= formatarTelefone($sindico['telefone']) ?></td>
    <td>
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="editarSindico(<?= $sindico['id_sindico'] ?>)">Editar</button>
            <button class="btn btn-info" onclick="verFichaSindico(<?= $sindico['id_sindico'] ?>)">Ver Ficha</button>

            <?php if ($sindico['deleted_at'] === null): ?>
                <form method="POST" action="/softDelete/desativar">
                    <input type="hidden" name="tabela" value="tb_sindico">
                    <input type="hidden" name="id" value="<?= $sindico['id_sindico'] ?>">
                    <button class="btn btn-danger" type="submit">Desativar</button>
                </form>
            <?php else: ?>
                <form method="POST" action="/softDelete/reativar">
                    <input type="hidden" name="tabela" value="tb_sindico">
                    <input type="hidden" name="id" value="<?= $sindico['id_sindico'] ?>">
                    <button class="btn btn-success" type="submit">Reativar</button>
                </form>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php endforeach; ?>
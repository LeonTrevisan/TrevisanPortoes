<?php foreach ($servicos as $servico): ?>
<tr>
    <td><?= $servico['cliente_nome'] ?></td>
    <td><?= $servico['tipo_servico'] ?></td>
    <td><?= date('d/m/Y', strtotime($servico['data_hora'])) ?></td>
    <td>
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="editarServico(<?= $servico['id_servico'] ?>)">Editar</button>
            <button class="btn btn-info" onclick="verFichaServico(<?= $servico['id_servico'] ?>)">Ver Ficha</button>
        </div>
    </td>
</tr>
<?php endforeach; ?>
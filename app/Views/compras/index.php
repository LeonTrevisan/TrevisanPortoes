<?php foreach ($compras as $compra): ?>
<tr>
    <td><?= date('d/m/Y', strtotime($compra['data_compra'])) ?></td>
    <td><?= $compra['material'] ?></td>
    <td><?= $compra['qtd_compra'] ?></td>
    <td>R$ <?= number_format($compra['valor_un'], 2, ',', '.') ?></td>
    <td>R$ <?= number_format($compra['valor_total'], 2, ',', '.') ?></td>
    <td><?= $compra['nome_distribuidora'] ?? 'N/A' ?></td>
    <td>
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="editarCompra(<?= $compra['id_compra'] ?>)">Editar</button>
        </div>
    </td>
</tr>
<?php endforeach; ?>
<?php foreach ($funcionarios as $funcionario): ?>
<?php
    $isDeleted = !empty($funcionario['deleted_at']);
    $isSelf = isset($usuarioLogado['id_funcionario']) && (int)$usuarioLogado['id_funcionario'] === (int)$funcionario['id_funcionario'];
    $roleLabel = !empty($funcionario['role_nome']) ? $funcionario['role_nome'] : 'Sem role';
?>
<tr>
    <td><?= htmlspecialchars($funcionario['nome']) ?></td>
    <td><?= htmlspecialchars($funcionario['email']) ?></td>
    <td><?= htmlspecialchars($roleLabel) ?></td>
    <td>
        <?php if ($isDeleted): ?>
            <span class="status status-cancelado">Desativado</span>
        <?php else: ?>
            <span class="status status-concluido">Ativo</span>
        <?php endif; ?>
    </td>
    <td>
        <?php if ($isAdmin): ?>
            <div class="action-buttons">
                <button class="btn btn-primary" type="button" onclick="editarFuncionario(<?= (int)$funcionario['id_funcionario'] ?>)">
                    Editar
                </button>

                <?php if (!$isDeleted && !$isSelf): ?>
                    <form method="POST" action="<?= $baseUrl ?>/funcionarios/desativar">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfDisableToken) ?>">
                        <input type="hidden" name="id" value="<?= (int)$funcionario['id_funcionario'] ?>">
                        <button class="btn btn-danger" type="submit">Desativar</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <span class="muted">Somente leitura</span>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>

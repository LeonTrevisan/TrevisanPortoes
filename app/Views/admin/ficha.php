<div class="ficha-admin">
    <h2>Ficha Completa do Administrador</h2>
    <div class="admin-info">
        <h3>Informações do Administrador</h3>
        <p><strong>Nome:</strong> <?= htmlspecialchars($admin['nome']) ?></p>
        <p><strong>Telefone:</strong> <?= formatarTelefone($admin['telefone']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($admin['email']) ?></p>
    </div>

    <div class="condominios-info">
        <h3>Condomínios Vinculados</h3>
        <?php if (empty($condominios)): ?>
            <p>Nenhum condomínio vinculado.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($condominios as $condominio): ?>
                    <li>
                        <strong>Nome:</strong> <?= htmlspecialchars($condominio['nome']) ?><br>
                        <strong>Tipo:</strong> <?= htmlspecialchars($condominio['tipo_cliente']) ?><br>
                        <strong>Telefone:</strong> <?= formatarTelefone($condominio['telefone']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
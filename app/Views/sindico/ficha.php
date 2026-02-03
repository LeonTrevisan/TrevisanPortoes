<div class="ficha-sindico">
    <h2>Ficha Completa do Síndico</h2>
    <div class="sindico-info">
        <h3>Informações do Síndico</h3>
        <p><strong>Nome:</strong> <?= htmlspecialchars($sindico['nome']) ?></p>
        <p><strong>Telefone:</strong> <?= formatarTelefone($sindico['telefone']) ?></p>
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
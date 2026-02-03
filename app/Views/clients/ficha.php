<div class="ficha-cliente">
    <h2>Ficha Completa do Cliente</h2>
    <div class="cliente-info">
        <h3>Informações do Cliente</h3>
        <p><strong>Nome:</strong> <?= htmlspecialchars($cliente['nome']) ?></p>
        <p><strong>Tipo:</strong> <?= htmlspecialchars($cliente['tipo_cliente']) ?></p>
        <p><strong>Telefone:</strong> <?= formatarTelefone($cliente['telefone']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($cliente['email'] ?? 'N/A') ?></p>
        <?php if ($cliente['cnpj']): ?>
            <p><strong>CNPJ:</strong> 
                <?php if (strpos($cliente['cnpj'], '.pdf') !== false): ?>
                    <a href="/<?= htmlspecialchars($cliente['cnpj']) ?>" target="_blank">Ver PDF</a>
                <?php else: ?>
                    <?= htmlspecialchars($cliente['cnpj']) ?>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="endereco-info">
        <h3>Endereço</h3>
        <p><strong>Rua:</strong> <?= htmlspecialchars($cliente['rua'] ?? 'N/A') ?></p>
        <p><strong>Número:</strong> <?= htmlspecialchars($cliente['numero'] ?? 'N/A') ?></p>
        <p><strong>Bairro:</strong> <?= htmlspecialchars($cliente['bairro'] ?? 'N/A') ?></p>
        <p><strong>Cidade:</strong> <?= htmlspecialchars($cliente['cidade'] ?? 'N/A') ?></p>
        <p><strong>Complemento:</strong> <?= htmlspecialchars($cliente['complemento'] ?? 'N/A') ?></p>
    </div>

    <?php if ($cliente['id_sindico']): ?>
    <div class="sindico-info">
        <h3>Informações do Síndico</h3>
        <p><strong>Nome:</strong> <?= htmlspecialchars($cliente['sindico_nome']) ?></p>
        <!-- Adicionar mais info do síndico se necessário -->
    </div>
    <?php endif; ?>

    <?php if ($cliente['id_admin']): ?>
    <div class="admin-info">
        <h3>Informações do Administrador</h3>
        <p><strong>Nome:</strong> <?= htmlspecialchars($cliente['admin_nome']) ?></p>
        <!-- Adicionar mais info do admin se necessário -->
    </div>
    <?php endif; ?>

    <div class="servicos-info">
        <h3>Serviços Prestados</h3>
        <?php if (empty($servicos)): ?>
            <p>Nenhum serviço registrado.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($servicos as $servico): ?>
                    <li>
                        <strong>Data:</strong> <?= htmlspecialchars($servico['data_hora']) ?><br>
                        <strong>Tipo:</strong> <?= htmlspecialchars($servico['tipo_servico']) ?><br>
                        <strong>Descrição:</strong> <?= htmlspecialchars($servico['descricao'] ?? 'N/A') ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
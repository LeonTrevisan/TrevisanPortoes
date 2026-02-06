<div class="ficha-servico ficha-completa">
    <button class="btn-close" data-page="servicos" onclick="voltarParaDashboard(this)">×</button>
    <h2>Ficha Completa do Serviço</h2>
    <div class="servico-info ficha-info">
        <p>
            <h3>Informações do Serviço</h3>
        </p>
        <p><strong>Cliente:</strong> <?= htmlspecialchars($servico['cliente_nome']) ?></p>
        <p><strong>Tipo:</strong> <?= htmlspecialchars($servico['tipo_servico']) ?></p>
        <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($servico['data_hora'])) ?></p>
        <p><strong>Descrição:</strong> <?= htmlspecialchars($servico['descricao'] ?? 'N/A') ?></p>
        <p><strong>Observação:</strong> <?= htmlspecialchars($servico['observacao'] ?? 'N/A') ?></p>
        <?php if ($servico['foto']): ?>
            <p><strong>Foto:</strong> <a href="<?= htmlspecialchars($servico['foto']) ?>" target="_blank">Ver Foto</a></p>
        <?php endif; ?>
        <?php if ($servico['comprovante']): ?>
            <p><strong>Comprovante:</strong> <a href="<?= htmlspecialchars($servico['comprovante']) ?>" target="_blank">Ver Comprovante</a></p>
        <?php endif; ?>
    </div>
    <div class="pagamento-info ficha-info">
        <p>
            <h3>Pagamento</h3>
        </p>
        <p><strong>Status:</strong> <?= htmlspecialchars($servico['status_pagamento'] ?? 'Sem pagamento') ?></p>
        <p><strong>Forma de pagamento:</strong> <?= htmlspecialchars($servico['forma_pagamento'] ?? 'N/A') ?></p>
        <p><strong>Valor:</strong> <?= $servico['valor'] !== null ? 'R$ ' . number_format((float)$servico['valor'], 2, ',', '.') : 'N/A' ?></p>
    </div>
</div>

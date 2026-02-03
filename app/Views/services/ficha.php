<div class="ficha-servico">
    <h2>Ficha Completa do Serviço</h2>
    <div class="servico-info">
        <h3>Informações do Serviço</h3>
        <p><strong>Cliente:</strong> <?= htmlspecialchars($servico['cliente_nome']) ?></p>
        <p><strong>Tipo:</strong> <?= htmlspecialchars($servico['tipo_servico']) ?></p>
        <p><strong>Data/Hora:</strong> <?= date('d/m/Y H:i', strtotime($servico['data_hora'])) ?></p>
        <p><strong>Descrição:</strong> <?= htmlspecialchars($servico['descricao'] ?? 'N/A') ?></p>
        <p><strong>Observação:</strong> <?= htmlspecialchars($servico['observacao'] ?? 'N/A') ?></p>
        <?php if ($servico['foto']): ?>
            <p><strong>Foto:</strong> <a href="<?= htmlspecialchars($servico['foto']) ?>" target="_blank">Ver Foto</a></p>
        <?php endif; ?>
        <?php if ($servico['comprovante']): ?>
            <p><strong>Comprovante:</strong> <a href="<?= htmlspecialchars($servico['comprovante']) ?>" target="_blank">Ver Comprovante</a></p>
        <?php endif; ?>
    </div>
</div>
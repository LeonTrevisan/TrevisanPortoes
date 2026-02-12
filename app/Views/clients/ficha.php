<div class="ficha-cliente ficha-completa">
    <button class="btn-close" data-page="clientes" onclick="voltarParaDashboard(this)">×</button>
    <h2>Ficha Completa do Cliente</h2>
    <div class="cliente-info ficha-info">
        <p>
            <h3>Informações do Cliente</h3>
        </p>
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
        <h3>Informações do Administrador do Condomínio</h3>
        <p><strong>Nome:</strong> <?= htmlspecialchars($cliente['admin_cond_nome'] ?? $cliente['admin_nome'] ?? 'N/A') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($cliente['admin_cond_email'] ?? 'N/A') ?></p>
        <p><strong>Telefone:</strong> <?= !empty($cliente['admin_cond_telefone']) ? htmlspecialchars(formatarTelefone($cliente['admin_cond_telefone'])) : 'N/A' ?></p>
    </div>
    <?php endif; ?>

    <div class="servicos-info info">
        <h3>Serviços Prestados</h3>
        <div class="filters">
            <div class="form-group">
                <label for="servico_tipo_filter">Tipo de Serviço:</label>
                <select id="servico_tipo_filter">
                    <option value="">Todos</option>
                    <?php if (!empty($tiposServico)): ?>
                        <?php foreach ($tiposServico as $tipo): ?>
                            <option value="<?= htmlspecialchars($tipo['tipo_servico']) ?>">
                                <?= htmlspecialchars($tipo['tipo_servico']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="servico_status_filter">Status do Pagamento:</label>
                <select id="servico_status_filter">
                    <option value="">Todos</option>
                    <?php if (!empty($statusPagamentos)): ?>
                        <?php foreach ($statusPagamentos as $status): ?>
                            <option value="<?= htmlspecialchars($status['status_pagamento']) ?>">
                                <?= htmlspecialchars($status['status_pagamento']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="servico_filter_type">Filtrar por:</label>
                <select id="servico_filter_type">
                    <option value="">Selecione</option>
                    <option value="periodo">Período</option>
                    <option value="ano">Ano</option>
                </select>
            </div>
            <div class="form-group">
                <label for="servico_filter_value">Valor:</label>
                <select id="servico_filter_value" disabled>
                    <option value="">Selecione</option>
                </select>
            </div>
        </div>

        <?php if (empty($servicos)): ?>
            <p>Nenhum serviço registrado.</p>
        <?php else: ?>
            <ul id="servicoList" data-layout="ficha">
                <?php foreach ($servicos as $servico): ?>
                    <li
                        data-status="<?= htmlspecialchars($servico['status_pagamento'] ?? '') ?>"
                        data-date="<?= htmlspecialchars(date('d/m/Y', strtotime($servico['data_hora']))) ?>"
                        data-tipo="<?= htmlspecialchars($servico['tipo_servico']) ?>"
                    >
                        <hr>
                        <strong>Data:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($servico['data_hora']))) ?><br>
                        <strong>Tipo:</strong> <?= htmlspecialchars($servico['tipo_servico']) ?><br>
                        <strong>Descrição:</strong> <?= htmlspecialchars($servico['descricao'] ?? 'N/A') ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

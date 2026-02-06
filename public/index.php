<?php 
    require __DIR__ . '/../vendor/autoload.php';
    require_once '../app/Helpers/formatacao.php';

    use App\Controllers\AdminController;
    use App\Repositories\AdminRepository;
    use App\Repositories\SoftDeleteRepository;
    use App\Services\AdminService;
    use App\Core\Database;

    $db = Database::connect();

    $adminController = new AdminController();
    $baseUrl = dirname($_SERVER['SCRIPT_NAME']);

    $dashboard = [
        'servicos_mes' => 0,
        'compras_mes' => 0.0,
        'pagamentos_pendentes' => 0
    ];
    $ultimosServicos = [];
    $statusPagamentos = [];
    $formasPagamento = [];
    $tiposServico = [];

    $meses = [
        '01' => 'Janeiro',
        '02' => 'Fevereiro',
        '03' => 'Março',
        '04' => 'Abril',
        '05' => 'Maio',
        '06' => 'Junho',
        '07' => 'Julho',
        '08' => 'Agosto',
        '09' => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro'
    ];
    $mesAtualLabel = $meses[date('m')] . ' ' . date('Y');
    $mesInicio = (new DateTimeImmutable('first day of this month'))->format('Y-m-01');
    $mesFim = (new DateTimeImmutable('first day of next month'))->format('Y-m-01');

    $fetchScalar = function(string $sql, array $params = []) use ($db) {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    };

    try {
        $dashboard['servicos_mes'] = (int)$fetchScalar(
            "SELECT COUNT(*) FROM tb_servico WHERE data_hora >= :inicio AND data_hora < :fim",
            [':inicio' => $mesInicio, ':fim' => $mesFim]
        );
        $dashboard['compras_mes'] = (float)$fetchScalar(
            "SELECT COALESCE(SUM(valor_total), 0) FROM tb_compras WHERE data_compra >= :inicio AND data_compra < :fim",
            [':inicio' => $mesInicio, ':fim' => $mesFim]
        );
        $dashboard['pagamentos_pendentes'] = (int)$fetchScalar(
            "SELECT COUNT(*)
             FROM tb_pagamento p
             JOIN tb_status_pagamento s ON p.id_status = s.id_status
             WHERE s.status_pagamento = 'Pendente'"
        );

        $stmt = $db->prepare("
            SELECT s.data_hora, c.nome AS cliente_nome, ts.tipo_servico, sp.status_pagamento
            FROM tb_servico s
            JOIN tb_cliente c ON s.id_cliente = c.id_cliente
            JOIN tb_tipo_servico ts ON s.id_tipo = ts.id_tipo
            LEFT JOIN tb_pagamento p ON p.id_servico = s.id_servico
            LEFT JOIN tb_status_pagamento sp ON p.id_status = sp.id_status
            ORDER BY s.data_hora DESC
            LIMIT 5
        ");
        $stmt->execute();
        $ultimosServicos = $stmt->fetchAll();

        $statusPagamentos = $db->query("
            SELECT id_status, status_pagamento
            FROM tb_status_pagamento
            ORDER BY id_status ASC
        ")->fetchAll();

        $formasPagamento = $db->query("
            SELECT id_forma_pagamento, forma_pagamento
            FROM tb_forma_pagamento
            ORDER BY id_forma_pagamento ASC
        ")->fetchAll();

        $tiposServico = $db->query("
            SELECT id_tipo, tipo_servico
            FROM tb_tipo_servico
            ORDER BY tipo_servico ASC
        ")->fetchAll();
    } catch (\Throwable $e) {
    }

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trevisan Portões Automáticos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        const baseUrl = '<?= $baseUrl ?>';
    </script>
    <script src="assets/js/script.js" defer></script>
</head>
<body>
    <div class="header">
        <h1>Trevisan Portões Automáticos</h1>
    </div>

    <div class="container">
        <div class="sidebar">
            <div class="menu-item active" data-page="dashboard" onclick="showPage('dashboard', this)">Dashboard</div>
            <div class="menu-item" data-page="clientes" onclick="showPage('clientes', this)">Clientes</div>
            <div class="menu-item" data-page="admin" onclick="showPage('admin', this)">Adminsitradores</div>
            <div class="menu-item" data-page="sindico" onclick="showPage('sindico', this)">Síndicos</div>
            <div class="menu-item" data-page="servicos" onclick="showPage('servicos', this)">Serviços</div>
            <div class="menu-item" data-page="pecas" onclick="showPage('pecas', this)">Peças e Materiais</div>
            <div class="menu-item" data-page="fichas" onclick="showPage('fichas', this)">Fichas</div>
        </div>

        <div class="main-content">
            <!-- Dashboard -->
            <div id="dashboard" class="page active">
                <div class="page-header">
                    <h2>Dashboard</h2>
                    <p>Visão geral do sistema</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card clickable" onclick="irParaServicos30Dias()">
                        <h3>Serviços no Mês (<?= $mesAtualLabel ?>)</h3>
                        <div class="value"><?= $dashboard['servicos_mes'] ?></div>
                    </div>
                    <div class="stat-card clickable" onclick="irParaCompras30Dias()">
                        <h3>Compras do Mês (<?= $mesAtualLabel ?>)</h3>
                        <div class="value">R$ <?= number_format($dashboard['compras_mes'], 2, ',', '.') ?></div>
                    </div>
                    <div class="stat-card clickable" onclick="irParaPendentes()">
                        <h3>Pagamentos Pendentes</h3>
                        <div class="value"><?= $dashboard['pagamentos_pendentes'] ?></div>
                    </div>
                </div>

                    <h3 style="margin-bottom: 1rem;">Últimos Serviços</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>Serviço</th>
                                <th>Status Pagamento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ultimosServicos)): ?>
                                <tr>
                                    <td colspan="4">Nenhum serviço encontrado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ultimosServicos as $servico): ?>
                                    <?php
                                        $statusPagamento = $servico['status_pagamento'] ?? 'Sem pagamento';
                                        $statusClass = 'status-agendado';
                                        if ($statusPagamento === 'Pago') {  
                                            $statusClass = 'status-concluido';
                                        } elseif ($statusPagamento === 'Cancelado') {
                                            $statusClass = 'status-cancelado';
                                        }
                                    ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($servico['data_hora'])) ?></td>
                                        <td><?= htmlspecialchars($servico['cliente_nome']) ?></td>
                                        <td><?= htmlspecialchars($servico['tipo_servico']) ?></td>
                                        <td><span class="status <?= $statusClass ?>"><?= htmlspecialchars($statusPagamento) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

 <!-- Adminsitradores -->
            <div id="admin" class="page">
                <div class="page-header">
                    <h2>Adminsitradores</h2>
                    <p>Gerencie os administradores de condomínio</p>
                </div>

                <div class="menu">
                <button class="btn btn-primary" onclick="novoAdmin()">Novo Administrador</button>
                </div>

                <input type="text" id="search_admin" class="search-bar" placeholder="Buscar por nome, telefone ou documento">

                <div class="clients">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>Email</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="adminTable">
                            <?php $adminController->index(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

 <!-- Clientes -->
            <div id="clientes" class="page">
                <div class="page-header">
                    <h2>Clientes</h2>
                    <p>Gerencie os clientes (casas e condomínios)</p>
                </div>

                <div class="menu">
                <button class="btn btn-primary" onclick="novoCliente()">Novo Cliente</button>
                </div>

                <input type="text" id="search_cliente" class="search-bar" placeholder="Buscar por nome ou telefone">

                <div class="clients">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Telefone</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="clienteTable">
                            <?php
                            $clienteController = new App\Controllers\ClienteController();
                            $clienteController->index();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

 <!-- Síndicos -->
            <div id="sindico" class="page">
                <div class="page-header">
                    <h2>Síndicos</h2>
                    <p>Gerencie os síndicos</p>
                </div>

                <div class="menu">
                <button class="btn btn-primary" onclick="novoSindico()">Novo Síndico</button>
                </div>

                <input type="text" id="search_sindico" class="search-bar" placeholder="Buscar por nome ou telefone">

                <div class="clients">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="sindicoTable">
                            <?php
                            $sindicoController = new App\Controllers\SindicoController();
                            $sindicoController->index();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

 <!-- Serviços -->
            <div id="servicos" class="page">
                <div class="page-header">
                    <h2>Serviços</h2>
                    <p>Gerencie os serviços prestados</p>
                </div>

                <div class="menu">
                <button class="btn btn-primary" onclick="novoServico()">Novo Serviço</button>
                </div>

                <input type="text" id="search_servico" class="search-bar" placeholder="Buscar por cliente">

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

                <div class="clients">
                    <table>
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="servicoTable">
                            <?php
                            $servicoController = new App\Controllers\ServicoController();
                            $servicoController->index();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

 <!-- Peças e Materiais -->
            <div id="pecas" class="page">
                <div class="page-header">
                    <h2>Peças e Materiais</h2>
                    <p>Gerencie as compras de peças e materiais</p>
                </div>

                <div class="menu">
                <button class="btn btn-primary" onclick="novaCompra()">Nova Compra</button>
                </div>

                <input type="text" id="search_compra" class="search-bar" placeholder="Buscar por material ou distribuidora">

                <div class="filters">
                    <div class="form-group">
                        <label for="compra_filter_type">Filtrar por:</label>
                        <select id="compra_filter_type">
                            <option value="">Selecione</option>
                            <option value="periodo">Período</option>
                            <option value="ano">Ano</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="compra_filter_value">Valor:</label>
                        <select id="compra_filter_value" disabled>
                            <option value="">Selecione</option>
                        </select>
                    </div>
                </div>

                <div class="clients">
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Material</th>
                                <th>Quantidade</th>
                                <th>Valor Unitário</th>
                                <th>Valor Total</th>
                                <th>Distribuidora</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="compraTable">
                            <?php
                            $compraController = new App\Controllers\CompraController();
                            $compraController->index();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Fichas -->
            <div id="fichas" class="page">
                <div class="page-header">
                    <h2>Fichas</h2>
                    <p>Impressao de fichas para preenchimento manual</p>
                </div>

                <div class="ficha-controls">
                    <div class="ficha-type-group">
                        <span class="ficha-type-label">Tipo de ficha:</span>
                        <label class="ficha-type-option">
                            <input type="radio" name="ficha_tipo" value="generica" checked>
                            Ficha generica
                        </label>
                        <label class="ficha-type-option">
                            <input type="radio" name="ficha_tipo" value="especifica">
                            Ficha com cliente
                        </label>
                    </div>
                    <button class="btn btn-primary" type="button" onclick="imprimirFicha()">Imprimir ficha</button>
                </div>

                <div id="ficha-cliente-wrapper" class="card ficha-cliente-card">
                    <div class="form-group">
                        <label for="ficha_cliente_select">Cliente:</label>
                        <select id="ficha_cliente_select" name="ficha_cliente_select" data-searchable="1" data-search-status="ficha_cliente_search_status" size="6">
                            <div id="ficha_cliente_search_status" class="select-search-status">Digite para filtrar</div>
                            <option value="">Selecione</option>
                            <?php
                            $fichaClienteController = new App\Controllers\ClienteController();
                            $fichaClienteController->select();
                            ?>
                        </select>
                    </div>
                    <p class="ficha-note">O endereco sera preenchido automaticamente na ficha.</p>
                </div>

                <div class="ficha-print-area">
                    <div id="ficha-generica" class="ficha-sheet active">
                        <div class="ficha-header">
                            <div class="ficha-title">Ficha de Servico</div>
                            <div class="ficha-subtitle">Preenchimento manual</div>
                        </div>
                        <div class="ficha-field">
                            <span class="ficha-label">Nome do cliente:</span>
                            <div class="ficha-line"></div>
                        </div>
                        <div class="ficha-field ficha-field-stack">
                            <span class="ficha-label">Endereco do cliente:</span>
                            <div class="ficha-lines ficha-lines-medium"></div>
                        </div>
                        <div class="ficha-field">
                            <span class="ficha-label">Data:</span>
                            <div class="ficha-line ficha-line-short"></div>
                        </div>
                        <div class="ficha-field">
                            <span class="ficha-label">Tipo do servico:</span>
                            <div class="ficha-line"></div>
                        </div>
                        <div class="ficha-field ficha-field-stack">
                            <span class="ficha-label">Descricao:</span>
                            <div class="ficha-lines ficha-lines-large"></div>
                        </div>
                    </div>

                    <div id="ficha-especifica" class="ficha-sheet">
                        <div class="ficha-header">
                            <div class="ficha-title">Ficha de Servico</div>
                            <div class="ficha-subtitle">Cliente selecionado</div>
                        </div>
                        <div class="ficha-field">
                            <span class="ficha-label">Nome do cliente:</span>
                            <div class="ficha-line"><span class="ficha-value" id="ficha_cliente_nome"></span></div>
                        </div>
                        <div class="ficha-field ficha-field-stack">
                            <span class="ficha-label">Endereco do cliente:</span>
                            <div class="ficha-lines ficha-lines-medium">
                                <span class="ficha-value" id="ficha_cliente_endereco"></span>
                            </div>
                        </div>
                        <div class="ficha-field">
                            <span class="ficha-label">Data:</span>
                            <div class="ficha-line ficha-line-short"></div>
                        </div>
                        <div class="ficha-field">
                            <span class="ficha-label">Tipo do servico:</span>
                            <div class="ficha-line"></div>
                        </div>
                        <div class="ficha-field ficha-field-stack">
                            <span class="ficha-label">Descricao:</span>
                            <div class="ficha-lines ficha-lines-large"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modais -->
    <!-- Modal Administrador -->
    <div id="modalAdm" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="tituloModalAdm">Novo Administrador</h3>
            </div>
            <form id="formAdm" action="<?= $baseUrl ?>/admin/store" method="POST">
                <input type="hidden" name="id" id="id_admin">
                <div class="form-group">
                    <label for="nome_admin">Nome:</label>
                    <input type="text" id="nome_admin" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="telefone_admin">Telefone:</label>
                    <input type="text" id="telefone_admin" name="telefone" required>
                </div>
                <div class="form-group">
                    <label for="email_admin">Email:</label>
                    <input type="email" id="email_admin" name="email" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalAdm')">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvarAdm">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Cliente -->
    <div id="modalCliente" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="tituloModalCliente">Novo Cliente</h3>
            </div>
            <form id="formCliente" action="<?= $baseUrl ?>/clientes/store" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id_cliente">
                <div class="form-group">
                    <label for="nome_cliente">Nome:</label>
                    <input type="text" id="nome_cliente" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="telefone_cliente">Telefone:</label>
                    <input type="text" id="telefone_cliente" name="telefone" required>
                </div>
                <div class="form-group">
                    <label for="email_cliente">Email:</label>
                    <input type="email" id="email_cliente" name="email">
                </div>
                <div class="form-group">
                    <label for="id_tipo_cliente">Tipo de Cliente:</label>
                    <select id="id_tipo_cliente" name="id_tipo_cliente" required onchange="toggleCondominioFields()">
                        <option value="1">Residencial</option>
                        <option value="2">Condomínio</option>
                    </select>
                </div>
                <div id="condominio-fields" style="display: none;">
                    <input type="hidden" name="cnpj_existing" id="cnpj_existing">
                    <div class="form-group">
                        <label for="cnpj_cliente">CNPJ (PDF):</label>
                        <input type="file" id="cnpj_cliente" name="cnpj" accept=".pdf">
                    </div>
                    <div class="form-group">
                        <label for="id_sindico">Síndico:</label>
                        <select id="id_sindico" name="id_sindico">
                            <option value="">Selecione</option>
                            <?php
                            $sindicoController = new App\Controllers\SindicoController();
                            $sindicoController->select();
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_admin">Administrador:</label>
                        <select id="id_admin" name="id_admin">
                            <option value="">Selecione</option>
                            <?php $adminController->select(); ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="rua_cliente">Rua:</label>
                    <input type="text" id="rua_cliente" name="rua" required>
                </div>
                <div class="form-group">
                    <label for="numero_cliente">Número:</label>
                    <input type="number" id="numero_cliente" name="numero" required>
                </div>
                <div class="form-group">
                    <label for="bairro_cliente">Bairro:</label>
                    <input type="text" id="bairro_cliente" name="bairro" required>
                </div>
                <div class="form-group">
                    <label for="cidade_cliente">Cidade:</label>
                    <input type="text" id="cidade_cliente" name="cidade" required>
                </div>
                <div class="form-group">
                    <label for="complemento_cliente">Complemento:</label>
                    <input type="text" id="complemento_cliente" name="complemento">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalCliente')">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvarCliente">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Síndico -->
    <div id="modalSindico" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="tituloModalSindico">Novo Síndico</h3>
            </div>
            <form id="formSindico" action="<?= $baseUrl ?>/sindico/store" method="POST">
                <input type="hidden" name="id" id="id_sindico_hidden">
                <div class="form-group">
                    <label for="nome_sindico">Nome:</label>
                    <input type="text" id="nome_sindico" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="telefone_sindico">Telefone:</label>
                    <input type="text" id="telefone_sindico" name="telefone" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalSindico')">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvarSindico">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Serviço -->
    <div id="modalServico" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="tituloModalServico">Novo Serviço</h3>
            </div>
            <form id="formServico" action="<?= $baseUrl ?>/servicos/store" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id_servico">
                <div class="form-group">
                    <label for="id_cliente_servico">Cliente:</label>
                    <select id="id_cliente_servico" name="id_cliente" required size="6" data-searchable="1" data-search-status="cliente_search_status">
                        <div id="cliente_search_status" class="select-search-status">Digite para filtrar</div>
                        <option value="">Selecione</option>
                        <?php
                        $clienteController = new App\Controllers\ClienteController();
                        $clienteController->select();
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_tipo_servico">Tipo de Serviço:</label>
                    <select id="id_tipo_servico" name="id_tipo" required>
                        <option value="1">Instalação</option>
                        <option value="2">Manutenção Corretiva</option>
                        <option value="3">Automação Preventiva</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="data_hora_servico">Data:</label>
                    <input type="date" id="data_hora_servico" name="data_hora" required>
                </div>
                <div class="form-group">
                    <label for="statusPag">Status do Pagamento:</label>
                    <select id="statusPag" name="id_status" required>
                        <?php if (empty($statusPagamentos)): ?>
                            <option value="">Sem status</option>
                        <?php else: ?>
                            <?php foreach ($statusPagamentos as $status): ?>
                                <?php $isPago = strtolower($status['status_pagamento']) === 'pago'; ?>
                                <option value="<?= $status['id_status'] ?>" data-is-paid="<?= $isPago ? '1' : '0' ?>"><?= htmlspecialchars($status['status_pagamento']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group" id="forma_pagamento">
                    <label for="id_forma_pagamento">Forma de Pagamento:</label>
                    <select id="id_forma_pagamento" name="id_forma_pagamento">
                        <?php if (empty($formasPagamento)): ?>
                            <option value="">Sem formas cadastradas</option>
                        <?php else: ?>
                            <?php foreach ($formasPagamento as $forma): ?>
                                <option value="<?= $forma['id_forma_pagamento'] ?>"><?= htmlspecialchars($forma['forma_pagamento']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="valor_servico">Valor do Serviço:</label>
                    <input type="text" id="valor_servico" name="valor_servico" required>
                </div>
                <div class="form-group">
                    <label for="descricao_servico">Descrição:</label>
                    <textarea id="descricao_servico" name="descricao"></textarea>
                </div>
                <div class="form-group">
                    <label for="observacao_servico">Observação:</label>
                    <textarea id="observacao_servico" name="observacao"></textarea>
                </div>
                <div class="form-group">
                    <label for="foto_servico">Foto:</label>
                    <input type="file" id="foto_servico" name="foto" accept="image/*">
                    <input type="hidden" id="foto_existing" name="foto_existing">
                    <div id="foto_current"></div>
                </div>
                <div class="form-group">
                    <label for="comprovante_servico">Comprovante:</label>
                    <input type="file" id="comprovante_servico" name="comprovante" accept="image/*,.pdf">
                    <input type="hidden" id="comprovante_existing" name="comprovante_existing">
                    <div id="comprovante_current"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalServico')">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvarServico">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Compra -->
    <div id="modalCompra" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="tituloModalCompra">Nova Compra</h3>
            </div>
            <form id="formCompra" action="<?= $baseUrl ?>/compras/store" method="POST">
                <input type="hidden" name="id" id="id_compra">
                <div class="form-group">
                    <label for="data_compra">Data:</label>
                    <input type="date" id="data_compra" name="data_compra" required>
                </div>
                <div class="form-group">
                    <label for="material_compra">Material:</label>
                    <input type="text" id="material_compra" name="material" required>
                </div>
                <div class="form-group">
                    <label for="qtd_compra">Quantidade:</label>
                    <input type="number" id="qtd_compra" name="qtd_compra" required>
                </div>
                <div class="form-group">
                    <label for="valor_un_compra">Valor Unitário:</label>
                    <input type="number" step="0.01" id="valor_un_compra" name="valor_un" required>
                </div>
                <div class="form-group">
                    <label for="id_distribuidora">Distribuidora:</label>
                    <select id="id_distribuidora" name="id_distribuidora">
                        <option value="">Selecione</option>
                        <?php
                        $distribuidoraController = new App\Controllers\DistribuidoraController();
                        $distribuidoraController->select();
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalCompra')">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvarCompra">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Verificar se há parâmetro page na URL e navegar
        const urlParams = new URLSearchParams(window.location.search);
        const page = urlParams.get('page');
        if (page) {
            const menuItem = document.querySelector(`.menu-item[data-page="${page}"]`);
            if (menuItem) {
                showPage(page, menuItem);
            }
        }
    </script>

</body>
</html>



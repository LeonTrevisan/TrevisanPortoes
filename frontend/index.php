<?php 
    include '../backend/php/conexao.php';
    include "../backend/php/lista_administrador.php";     
    include "../backend/php/lista_sindico.php";  
    require_once '../backend/php/helper/formatacao.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trevisan Portões Automáticos</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <div class="header">
        <h1>Trevisan Portões Automáticos</h1>
    </div>

    <div class="container">
        <div class="sidebar">
            <div class="menu-item active" onclick="showPage('dashboard', this)">Dashboard</div>
            <div class="menu-item" onclick="showPage('clientes', this)">Clientes</div>
            <div class="menu-item" onclick="showPage('admin', this)">Adminsitradores</div>
            <div class="menu-item" onclick="showPage('sindico', this)">Síndicos</div>
            <div class="menu-item" onclick="showPage('servicos', this)">Serviços</div>
            <div class="menu-item" onclick="showPage('pecas', this)">Peças e Materiais</div>
        </div>

        <div class="main-content">
            <!-- Dashboard -->
            <div id="dashboard" class="page active">
                <div class="page-header">
                    <h2>Dashboard</h2>
                    <p>Visão geral do sistema</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Pagamentos Pendentes</h3>
                        <div class="value">8</div>
                    </div>
                    <div class="stat-card">
                        <h3>Serviços Concluídos (Mês)</h3>
                        <div class="value">127</div>
                    </div>
                    <div class="stat-card">
                        <h3>Compras do Mês</h3>
                        <div class="value">R$ 8.450</div>
                    </div>
                </div>

                <div class="card">
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
                            <tr>
                                <td>07/01/2026</td>
                                <td>João Silva</td>
                                <td>Manutenção Preventiva</td>
                                <td><span class="status status-agendado">Pendente</span></td>
                            </tr>
                            <tr>
                                <td>08/01/2026</td>
                                <td>Maria Santos</td>
                                <td>Instalação de Equipamento</td>
                                <td><span class="status status-agendado">Pendente</span></td>
                            </tr>
                            <tr>
                                <td>09/01/2026</td>
                                <td>Pedro Costa</td>
                                <td>Reparo de Sistema</td>
                                <td><span class="status status-agendado">Pendente</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Clientes -->
            <div id="clientes" class="page">
                <div class="page-header">
                    <h2>Gestão de Clientes</h2>
                    <p>Visualize e gerencie todos os clientes</p>
                </div>
                <div class="menu">
                <button class="btn btn-primary" onclick="openModal('modalCliente')">Novo Cliente</button>
                </div>
                <input type="text" class="search-bar" placeholder="Buscar por nome, telefone ou documento">

                <div class="clients">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>CNPJ</th>
                                <th>Tipo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="clientesTable">
                            <?php include "../backend/php/lista_clientes.php";?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Adminsitradores -->
            <div id="admin" class="page">
                <div class="page-header">
                    <h2>Adminsitradores</h2>
                    <p>Gerencie os administradores de condomínio</p>
                </div>

                <div class="menu">
                <button class="btn btn-primary" onclick="openModal('modalAdm')">Novo Administrador</button>
                </div>

                <input type="text" class="search-bar" placeholder="Buscar por nome, telefone ou documento">

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
                            <?php listaAdm();?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Síndicos -->
            <div id="sindico" class="page">
                <div class="page-header">
                    <h2>Síndicos</h2>
                    <p>Gerencie os síndicos de condomínio</p>
                </div>

                <div class="menu">
                <button class="btn btn-primary" onclick="openModal('modalSindico')">Novo Síndico</button>
                </div>

                <input type="text" class="search-bar" placeholder="Buscar por nome, telefone ou documento">

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
                            <?php listaSindico();?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Serviços -->
            <div id="servicos" class="page">
                <div class="page-header">
                    <h2>Serviços</h2>
                    <p>Gerencie serviços agendados e prestados</p>
                </div>
                <div class="menu">
                    <button class="btn btn-primary" onclick="openModal('modalServico')">Novo Serviço</button>
                    <div class="filtro-servico">
                        <label>Filtrar por:</label>
                        <select id="tipo-filtro" class="select-filter" onchange="atualizarOpcoesFiltro()">
                            <option value="periodo">Período</option>
                            <option value="ano">Ano</option>
                        </select>
                        
                        <!-- Filtro por Período -->
                        <div id="filtro-periodo-container" style="display: inline-block; margin-left: 10px;">
                            <label>Últimos:</label>
                            <select id="filtro-periodo" class="select-filter">
                                <option value="7">7 Dias</option>
                                <option value="30" selected>30 Dias</option>
                                <option value="365">1 Ano</option>
                            </select>
                        </div>
                        
                        <!-- Filtro por Ano -->
                        <?php
                            $anoAtual = date('Y');
                            $anoInicio = $anoAtual - 4;
                        ?>
                        <div id="filtro-ano-container" style="display: none; margin-left: 10px;">
                            <label>Ano:</label>
                            <select id="filtro-ano" class="select-filter">
                                <option value="">Selecione um ano</option>
                                <?php for ($ano = $anoAtual; $ano >= $anoInicio; $ano--) {
                                    echo"
                                <option value=\"$ano\">$ano</option>";
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <input type="text" class="search-bar" placeholder="Buscar por cliente">
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="servicosTable">
                        <?php include "../backend/php/lista_servicos.php"; ?>
                    </tbody>
                </table>
            </div>

            <!-- Peças e Materiais -->
            <div id="pecas" class="page">
                <div class="page-header">
                    <h2>Peças e Materiais</h2>
                    <p>Controle de compras mensais</p>
                </div>
                <div class="menu">
                    <button class="btn btn-primary" onclick="openModal('modalCompra')">Registrar Compra</button>

                    <div class="filtro" id="filtro-mes">
                        <form id="formFiltro" onsubmit="return filtrarMateriais(event);">
                            <select name="filtro-mes" id="filtro" class="select-filter">
                                <option value="01">Janeiro</option>
                                <option value="02">Fevereiro</option>
                                <option value="03">Março</option>
                                <option value="04">Abril</option>
                                <option value="05">Maio</option>
                                <option value="06">Junho</option>
                                <option value="07">Julho</option>
                                <option value="08">Agosto</option>
                                <option value="09">Setembro</option>
                                <option value="10">Outubro</option>
                                <option value="11">Novembro</option>
                                <option value="12">Dezembro</option>
                            </select>
                        </form>
                    </div>
                </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Fornecedor</th>
                                <th>Quantidade</th>
                                <th>Valor Unitário</th>
                                <th>Valor Total</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php include "../backend/php/lista_material.php"; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <!-- Modal Cliente -->
    <div id="modalCliente" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="titleModalCliente">Novo Cliente</h3>
            </div>
            
            <form action="../backend/php/cadastro_cliente.php" method="post" enctype="multipart/form-data">
                <input type="hidden" id="id_cliente" name="id_cliente" value="">

                <div class="form-radio">
                    <input type="radio" name="tipo-cliente" id="tipo-morador" value="Residencial" checked />
                    <label for="tipo-morador">Residencial</label>
                </div>
                <div class="form-radio">
                    <input type="radio" name="tipo-cliente" id="tipo-condominio" value="Condomínio" />
                    <label for="tipo-condominio">Condomínio</label>
                </div>

                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome-cliente" required>
                </div>

                 <div class="form-group">
                    <label>Telefone</label>
                    <input type="tel" name="tel-cliente" required>
                </div>

                <div id="condform" class="cond-info">
                    <hr>
                    <div class="form-group">
                        <label>CNPJ</label>
                        <input type="file" accept=".png, .jpg, .jpeg, .png" name="cnpj-doc">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email-cliente">
                    </div>
                    <div class="form-group">
                        <label>Adminsitrador</label>
                        <select name="adm-cliente">
                            <?php selectAdm(); ?>    
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Síndico</label>
                        <select name="sindico-cliente">
                            <?php selectSindico(); ?> 
                        </select>
                    </div>
                    <hr>
                </div>
               
                <div class="form-group">
                <label>Rua</label>
                <input type="text" name="rua-cliente" required>
                </div>

                <div class="form-group">
                <label>Bairro</label>
                <input type="text" name="bairro-cliente" required>
                </div>

                <div class="form-group">
                <label>Número</label>
                <input type="number" name="num-cliente" required>
                </div>

                <div class="form-group">
                <label>Cidade</label>
                <input type="text" name="cidade-cliente" required>
                </div>

                <div class="modal-actions">
                    <button id="btnSalvarCliente" type="submit" class="btn btn-success">Salvar</button>
                    <button type="button" class="btn btn-danger">Cancelar</button>
                   
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Admin -->
    <div id="modalAdm" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="tituloModalAdmin">Novo Administrador</h3>
            </div>

            <form id="formAdmin" action="../backend/php/cadastro_admin.php" method="post" enctype="multipart/form-data">

                <input type="hidden" id="id_admin" name="id_admin" value="">

                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome-admin" required>
                </div>

                 <div class="form-group">
                    <label>Telefone</label>
                    <input type="tel" name="tel-admin" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email-admin">
                </div>
                   
                <div class="modal-actions">
                    <button id="btnSalvarAdmin" type="submit" class="btn btn-success">Salvar</button>
                    <button type="button" class="btn btn-danger" onclick="closeModal('modalAdm')">Cancelar</button>
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

            <form id="formSindico" action="../backend/php/cadastro_sindico.php" method="post" enctype="multipart/form-data">
                <input type="hidden" id="id_sindico" name="id_sindico" value="">

                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome-sindico" required>
                </div>

                 <div class="form-group">
                    <label>Telefone</label>
                    <input type="tel" name="tel-sindico" required>
                </div>
                   
                <div class="modal-actions">
                    <button id="btnSalvarSindico" type="submit" class="btn btn-success">Salvar</button>
                    <button type="button" class="btn btn-danger" onclick="closeModal('modalSindico')">Cancelar</button>
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
            <form id="formServico" method="post" action="../backend/php/cadastro_servico.php" enctype="multipart/form-data">
                <input type="hidden" id="id_servico" name="id_servico" value="">
                <div class="form-group">
                    <label>Cliente</label>
                    <select name="clientes" required>
                        <?php 
                            $sql = "SELECT id_cliente, nome FROM tb_cliente ORDER BY nome ASC";
                            $results = $conn->query($sql);
                            if (!$results) {
                                echo "<option value=''>Erro ao carregar clientes</option>";
                            } else {
                                $clients = $results->fetch_all(MYSQLI_ASSOC);
                                if (empty($clients)) {
                                    echo "<option value=''>Nenhum cliente cadastrado</option>";
                                } else {
                                    foreach ($clients as $client) {
                                        echo "<option value=\"" . htmlspecialchars($client['id_cliente']) . "\">" . htmlspecialchars($client['nome']) . "</option>";
                                    }
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Data do Serviço</label>
                    <input type="date" name="data_hora" required>
                </div>
                <div class="form-group">
                    <label>Tipo de Serviço</label>
                    <select name="tipo" required>
                        <?php 
                            include '../backend/php/conexao.php';
                            $sql = "SELECT * FROM tb_tipo_servico ORDER BY id_tipo ASC";
                            $results = $conn->query($sql);
                            if ($results) {
                                while($row = $results->fetch_assoc()) {
                                    echo "<option value=\"" . htmlspecialchars($row['id_tipo']) . "\">" . htmlspecialchars($row['tipo_servico']) . "</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="descricao"></textarea>
                </div>
                <div class="form-group">
                    <label>Observação</label>
                    <textarea name="observacao"></textarea>
                </div>
                <div class="form-group">
                    <label>Foto</label>
                    <input type="file" accept=".png, .jpg, .jpeg" name="foto-servico">
                </div>
                <div class="form-group">
                    <label>Comprovante</label>
                    <input type="file" accept=".png, .jpg, .jpeg, .pdf" name="comprovante-servico">
                </div>
                <div class="form-group">
                    <label>Valor</label>
                    <input name="preco" type="number" required>
                </div>
                <div class="form-group">
                    <label>Pagamento</label>
                    <select name="statusPagamento" id="statusPag" required>
                        <?php 
                            include '../backend/php/conexao.php';
                            $sql = "SELECT * FROM tb_status_pagamento ORDER BY id_status ASC";
                            $results = $conn->query($sql);
                            if ($results) {
                                while($row = $results->fetch_assoc()) {
                                    echo "<option value=\"" . htmlspecialchars($row['id_status']) . "\">" . htmlspecialchars($row['status_pagamento']) . "</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
                <div id="forma_pagamento" class="form-group">
                    <hr>
                    <label>Forma de pagamento</label>
                    <select name="formaPagamento" id="formaPag" required>
                        <?php 
                            $sql = "SELECT * FROM tb_forma_pagamento ORDER BY id_forma_pagamento ASC";
                            $results = $conn->query($sql);
                            if ($results) {
                                while($row = $results->fetch_assoc()) {
                                    echo "<option value=\"" . htmlspecialchars($row['id_forma_pagamento']) . "\">" . htmlspecialchars($row['forma_pagamento']) . "</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-danger" onclick="closeModal('modalServico')">Cancelar</button>
                    <button type="submit" id="btnSalvarServico" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Peça -->
    <div id="modalCompra" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="tituloModalCompra">Nova Compra</h3>
            </div>
            <form id="formCompra" method="post" action="../backend/php/cadastro_compra.php" enctype="multipart/form-data">
                <input type="hidden" id="id_compra" name="id_compra" value="">
                <div class="form-group">
                    <label>Data da Compra</label>
                    <input type="date" name="data_hora" required>
                </div>
                <div class="form-group">
                    <label>Material</label>
                    <input type="text" name="material" required>
                </div>
                <div class="form-group">
                    <label>Fornecedor</label>
                    <select name="fornecedor" required>
                        <?php 
                            $sql = "SELECT * FROM tb_distribuidora ORDER BY nome_distribuidora ASC";
                            $results = $conn->query($sql);
                            if ($results) {
                                while($row = $results->fetch_assoc()) {
                                    echo "<option value=\"" . htmlspecialchars($row['id_distribuidora']) . "\">" . htmlspecialchars($row['nome_distribuidora']) . "</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantidade</label>
                    <input type="text" name="qtd" required>
                </div>
                <div class="form-group">
                    <label>Valor Unitário</label>
                    <input type="number" name="valor" step="0.1" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-danger" onclick="closeModal('modalCompra')">Cancelar</button>
                    <button type="submit" id="btnSalvarCompra" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
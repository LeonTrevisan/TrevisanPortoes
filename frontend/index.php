<?php include '../backend/php/conexao.php';?>
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

                <button class="btn btn-primary" onclick="openModal('modalCliente')">Novo Cliente</button>

                <input type="text" class="search-bar" placeholder="Buscar cliente por nome, telefone ou documento">

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
                            <?php include "../backend/php/lista_clientes.php";
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ficha do cliente -->
            <div id="ficha" class="page">
                <div class="page-header">
                    <h2>Ficha do Cliente</h2>
                    <p>Detalhes completos do cliente</p>
                </div>

                <div id="fichaContent">
                    <?php 
                        include "../backend/php/ficha_cliente.php";  
                    ?>
                </div>
            </div>

            <!-- Serviços -->
            <div id="servicos" class="page">
                <div class="page-header">
                    <h2>Serviços</h2>
                    <p>Gerencie serviços agendados e prestados</p>
                </div>

                <button class="btn btn-primary" onclick="openModal('modalServico')">Novo Serviço</button>
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

                <button class="btn btn-primary" onclick="openModal('modalPeca')">Registrar Compra</button>

                <div class="card">
                    <h3 style="margin-bottom: 1rem;">Compras do Mês - Janeiro 2026</h3>
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
                            <tr>
                                <td>02/01/2026</td>
                                <td>Parafusos M6</td>
                                <td>Fornecedor ABC</td>
                                <td>100 un</td>
                                <td>R$ 0,50</td>
                                <td>R$ 50,00</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-primary btn-small">Editar</button>
                                        <button class="btn btn-danger btn-small">Excluir</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>04/01/2026</td>
                                <td>Cabo Elétrico 2,5mm</td>
                                <td>Distribuidora XYZ</td>
                                <td>50 m</td>
                                <td>R$ 12,00</td>
                                <td>R$ 600,00</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-primary btn-small">Editar</button>
                                        <button class="btn btn-danger btn-small">Excluir</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cliente -->
    <div id="modalCliente" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Novo Cliente</h3>
            </div>

            
            <form action="../backend/php/cadastro_cliente.php" method="post" enctype="multipart/form-data">
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
                            <?php include '../backend/php/lista_administrador.php'; ?>    
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Síndico</label>
                        <select name="sindico-cliente">
                            <?php include '../backend/php/lista_sindico.php'; ?> 
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
                    <button type="submit" class="btn btn-success">Salvar</button>
                    <button type="button" class="btn btn-danger">Cancelar</button>
                   
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
    <div id="modalPeca" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Registrar Compra</h3>
            </div>
            <form>
                <div class="form-group">
                    <label>Data da Compra</label>
                    <input type="date" required>
                </div>
                <div class="form-group">
                    <label>Descrição do Material</label>
                    <input type="text" required>
                </div>
                <div class="form-group">
                    <label>Fornecedor</label>
                    <input type="text" required>
                </div>
                <div class="form-group">
                    <label>Quantidade</label>
                    <input type="text" required>
                </div>
                <div class="form-group">
                    <label>Valor Unitário</label>
                    <input type="number" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Observações</label>
                    <textarea></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-danger" onclick="closeModal('modalPeca')">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
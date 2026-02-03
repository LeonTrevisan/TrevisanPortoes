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

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trevisan Portões Automáticos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js" defer></script>
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
                <button class="btn btn-primary" onclick="openModal('modalCliente')">Novo Cliente</button>
                </div>

                <input type="text" class="search-bar" placeholder="Buscar por nome ou telefone">

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
                <button class="btn btn-primary" onclick="openModal('modalSindico')">Novo Síndico</button>
                </div>

                <input type="text" class="search-bar" placeholder="Buscar por nome ou telefone">

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
                <button class="btn btn-primary" onclick="openModal('modalServico')">Novo Serviço</button>
                </div>

                <input type="text" class="search-bar" placeholder="Buscar por cliente ou tipo">

                <div class="clients">
                    <table>
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Data/Hora</th>
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
                <button class="btn btn-primary" onclick="openModal('modalCompra')">Nova Compra</button>
                </div>

                <input type="text" class="search-bar" placeholder="Buscar por material ou distribuidora">

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

        </div>
    </div>

    <!-- Modais -->
    <!-- Modal Administrador -->
    <div id="modalAdm" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="tituloModalAdm">Novo Administrador</h3>
            </div>
            <form id="formAdm" action="/admin/store" method="POST">
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
            <form id="formCliente" action="/clientes/store" method="POST" enctype="multipart/form-data">
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
            <form id="formSindico" action="/sindico/store" method="POST">
                <input type="hidden" name="id" id="id_sindico">
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
            <form id="formServico" action="/servicos/store" method="POST">
                <input type="hidden" name="id" id="id_servico">
                <div class="form-group">
                    <label for="id_cliente_servico">Cliente:</label>
                    <select id="id_cliente_servico" name="id_cliente" required>
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
                    <label for="data_hora_servico">Data/Hora:</label>
                    <input type="datetime-local" id="data_hora_servico" name="data_hora" required>
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
                    <label for="foto_servico">Foto (URL):</label>
                    <input type="url" id="foto_servico" name="foto">
                </div>
                <div class="form-group">
                    <label for="comprovante_servico">Comprovante (URL):</label>
                    <input type="url" id="comprovante_servico" name="comprovante">
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
            <form id="formCompra" action="/compras/store" method="POST">
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

</body>
</html>

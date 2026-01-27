<?php 
    require __DIR__ . '/../vendor/autoload.php';
    require_once '../app/Helpers/formatacao.php';

    use App\Controllers\AdminController;
    use App\Repositories\AdminRepository;
    use App\Services\AdminService;
    use App\Core\Database;

    $db = Database::connect();

    $adminController = new AdminController(
        new AdminService(
            new AdminRepository($db)
    )
);

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

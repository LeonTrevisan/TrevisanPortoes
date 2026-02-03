<?php

namespace App\Controllers;

use App\Core\Database;
use App\Repositories\ServicoRepository;
use App\Services\ServicoService;

class ServicoController
{
    private ServicoService $service;

    public function __construct() {
        $db = Database::connect();
        $repo = new ServicoRepository($db);
        $this->service = new ServicoService($repo);
    }

    public function store(): void {
        try {
            $this->service->cadastrar([
                'id_cliente' => (int)$_POST['id_cliente'],
                'id_tipo' => (int)$_POST['id_tipo'],
                'descricao' => $_POST['descricao'] ?? null,
                'observacao' => $_POST['observacao'] ?? null,
                'foto' => $_POST['foto'] ?? null,
                'comprovante' => $_POST['comprovante'] ?? null,
                'data_hora' => $_POST['data_hora'] ?? date('Y-m-d H:i:s')
            ]);

            header('Location: /?page=servicos&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: /?page=servicos&status=error&message=' . urlencode($e->getMessage()));
            exit();
        }
    }

    public function obter() {
        $id = $_GET['id'] ?? null;
        if(!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID do serviço não fornecido.']);
            return;
        }
        $servico = $this->service->buscarPorId((int)$id);
        if (!$servico) {
            http_response_code(404);
            echo json_encode(['error' => 'Serviço não encontrado.']);
            return;
        }
        header('Content-Type: application/json');
        echo json_encode($servico);
    }

    public function index() {
        $servicos = $this->service->listarTodos();
        require __DIR__ . '/../Views/services/index.php';
    }

    public function ficha() {
        $id = $_GET['id'] ?? null;
        if(!$id) {
            http_response_code(400);
            echo 'ID não fornecido.';
            return;
        }
        $servico = $this->service->buscarPorId((int)$id);
        if (!$servico) {
            http_response_code(404);
            echo 'Serviço não encontrado.';
            return;
        }
        require __DIR__ . '/../Views/services/ficha.php';
    }

    public function update(): void {
        $id = $_POST['id'] ?? null;
        if(!$id) {
            header('Location: /?page=servicos&status=error&message=ID não fornecido');
            exit();
        }
        try {
            $this->service->atualizar((int)$id, [
                'id_cliente' => (int)$_POST['id_cliente'],
                'id_tipo' => (int)$_POST['id_tipo'],
                'descricao' => $_POST['descricao'] ?? null,
                'observacao' => $_POST['observacao'] ?? null,
                'foto' => $_POST['foto'] ?? null,
                'comprovante' => $_POST['comprovante'] ?? null,
                'data_hora' => $_POST['data_hora'] ?? date('Y-m-d H:i:s')
            ]);
            header('Location: /?page=servicos&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: /?page=servicos&status=error&message=' . urlencode($e->getMessage()));
            exit();
        }
    }
}
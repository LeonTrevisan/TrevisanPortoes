<?php

namespace App\Controllers;

use App\Core\Database;
use App\Repositories\CompraRepository;
use App\Services\CompraService;

class CompraController
{
    private CompraService $service;

    public function __construct() {
        $db = Database::connect();
        $repo = new CompraRepository($db);
        $this->service = new CompraService($repo);
    }

    public function store(): void {
        try {
            $this->service->cadastrar([
                'data_compra' => $_POST['data_compra'] ?? date('Y-m-d'),
                'material' => $_POST['material'] ?? '',
                'qtd_compra' => (int)$_POST['qtd_compra'],
                'valor_un' => (float)$_POST['valor_un'],
                'id_distribuidora' => $_POST['id_distribuidora'] ? (int)$_POST['id_distribuidora'] : null
            ]);

            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=pecas&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=pecas&status=error&message=' . urlencode($e->getMessage()));
            exit();
        }
    }

    public function obter() {
        $id = $_GET['id'] ?? null;
        if(!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID da compra não fornecido.']);
            return;
        }
        $compra = $this->service->buscarPorId((int)$id);
        if (!$compra) {
            http_response_code(404);
            echo json_encode(['error' => 'Compra não encontrada.']);
            return;
        }
        header('Content-Type: application/json');
        echo json_encode($compra);
    }

    public function index() {
        $compras = $this->service->listarTodos();
        require __DIR__ . '/../Views/compras/index.php';
    }

    public function update(): void {
        $id = $_POST['id'] ?? null;
        if(!$id) {
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=pecas&status=error&message=ID não fornecido');
            exit();
        }
        try {
            $this->service->atualizar((int)$id, [
                'data_compra' => $_POST['data_compra'] ?? date('Y-m-d'),
                'material' => $_POST['material'] ?? '',
                'qtd_compra' => (int)$_POST['qtd_compra'],
                'valor_un' => (float)$_POST['valor_un'],
                'id_distribuidora' => $_POST['id_distribuidora'] ? (int)$_POST['id_distribuidora'] : null
            ]);
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=pecas&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=pecas&status=error&message=' . urlencode($e->getMessage()));
            exit();
        }
    }
}
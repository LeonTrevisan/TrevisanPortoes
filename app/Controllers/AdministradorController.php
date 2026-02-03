<?php

namespace App\Controllers;

use App\Core\Database;
use App\Repositories\AdminRepository;
use App\Repositories\SoftDeleteRepository;
use App\Services\AdminService;

class AdministradorController
{
    private AdminService $service;

    public function __construct() {
        $db = Database::connect();
        $repo = new AdminRepository($db);
        $softDelete = new SoftDeleteRepository();
        $this->service = new AdminService($repo, $softDelete);
    }

    public function store(): void {
        try {
            $this->service->cadastrar([
                'nome' => $_POST['nome'] ?? '',
                'telefone' => $_POST['telefone'] ?? '',
                'email' => $_POST['email'] ?? ''
            ]);

            header('Location: /?page=admin&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: /?page=admin&status=error&message=' . urlencode($e->getMessage()));
            exit();
        }
    }

    public function obter() {
        $id = $_GET['id'] ?? null;
        if(!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID do administrador não fornecido.']);
            return;
        }
        $admin = $this->service->buscarPorId((int)$id);
        if (!$admin) {
            http_response_code(404);
            echo json_encode(['error' => 'Administrador não encontrado.']);
            return;
        }
        header('Content-Type: application/json');
        echo json_encode($admin);
    }

    public function select() {
        $admins = $this->service->listarAtivos();
        require __DIR__ . '/../Views/admin/select.php';
    }

    public function index() {
        $admins = $this->service->listarTodos();
        require __DIR__ . '/../Views/admin/index.php';
    }

    public function ficha() {
        $id = $_GET['id'] ?? null;
        if(!$id) {
            http_response_code(400);
            echo 'ID não fornecido.';
            return;
        }
        $admin = $this->service->buscarPorId((int)$id);
        if (!$admin) {
            http_response_code(404);
            echo 'Administrador não encontrado.';
            return;
        }
        // Condomínios do admin
        $condominios = []; // Implementar
        require __DIR__ . '/../Views/admin/ficha.php';
    }

    public function update(): void {
        $id = $_POST['id'] ?? null;
        if(!$id) {
            header('Location: /?page=admin&status=error&message=ID não fornecido');
            exit();
        }
        try {
            // Assumir que AdminService tem método atualizar
            // Por enquanto, não implementado
            header('Location: /?page=admin&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: /?page=admin&status=error&message=' . urlencode($e->getMessage()));
            exit();
        }
    }
}
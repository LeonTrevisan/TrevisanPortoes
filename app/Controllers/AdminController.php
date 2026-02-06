<?php

namespace App\Controllers;

use App\Core\Database;
use App\Repositories\AdminRepository;
use App\Repositories\SoftDeleteRepository;
use App\Services\AdminService;

class AdminController
{
    private AdminService $service;

    public function __construct() {
        $db = Database::connect();
        $repo = new AdminRepository($db);
        $softDelete = new SoftDeleteRepository();
        $this->service = new AdminService($repo, $softDelete);
    }

    // Store a newly created admin in storage.
    public function store(): void {
        try {
            $this->service->cadastrar([
                'nome' => $_POST['nome'] ?? '',
                'telefone' => $_POST['telefone'] ?? '',
                'email' => $_POST['email'] ?? ''
            ]);

            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=admin&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=admin&status=error&message=' . urlencode($e->getMessage()));
            exit();
        }
    }

    public function update(): void {
        $id = $_POST['id'] ?? $_POST['id_admin'] ?? null;
        if(!$id) {
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=admin&status=error&message=ID nÃ£o fornecido');
            exit();
        }
        try {
            $this->service->atualizar((int)$id, [
                'nome' => $_POST['nome'] ?? '',
                'telefone' => $_POST['telefone'] ?? '',
                'email' => $_POST['email'] ?? ''
            ]);

            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=admin&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=admin&status=error&message=' . urlencode($e->getMessage()));
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
    
    // Display the select for admins.
    public function select() {
        $admins = $this->service->listarAtivos();
        require __DIR__ . '/../Views/admin/select.php';
    }

    //Index of all admins.
    public function index() {
        $admins = $this->service->listarTodos();
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
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
        $condominios = $this->service->getCondominios((int)$id);
        require __DIR__ . '/../Views/admin/ficha.php';
    }
}

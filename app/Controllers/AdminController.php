<?php

namespace App\Controllers;

use App\Core\Database;
use App\Repositories\AdminRepository;
use App\Services\AdminService;

class AdminController
{
    private AdminService $service;

    public function __construct() {
        $db = Database::connect();
        $repo = new AdminRepository($db);
        $this->service = new AdminService($repo);
    }

    // Store a newly created admin in storage.
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
    
    // Display the select for admins.
    public function select() {
        $admins = $this->service->listarAtivos();
        require __DIR__ . '/../Views/admin/select.php';
    }

    //Index of all admins.
    public function index() {
        $admins = $this->service->listarTodos();
        require __DIR__ . '/../Views/admin/index.php';
    }
}
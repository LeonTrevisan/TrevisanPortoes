<?php

namespace App\Controllers;

use App\Core\Database;
use App\Repositories\SindicoRepository;
use App\Repositories\SoftDeleteRepository;
use App\Services\SindicoService;

class SindicoController
{
    private SindicoService $service;

    public function __construct() {
        $db = Database::connect();
        $repo = new SindicoRepository($db);
        $softDelete = new SoftDeleteRepository();
        $this->service = new SindicoService($repo, $softDelete);
    }

    public function store(): void {
        try {
            $this->service->cadastrar([
                'nome' => $_POST['nome'] ?? '',
                'telefone' => $_POST['telefone'] ?? ''
            ]);

            header('Location: /?page=sindico&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: /?page=sindico&status=error&message=' . urlencode($e->getMessage()));
            exit();
        }
    }

    public function obter() {
        $id = $_GET['id'] ?? null;
        if(!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID do síndico não fornecido.']);
            return;
        }
        $sindico = $this->service->buscarPorId((int)$id);
        if (!$sindico) {
            http_response_code(404);
            echo json_encode(['error' => 'Síndico não encontrado.']);
            return;
        }
        header('Content-Type: application/json');
        echo json_encode($sindico);
    }

    public function select() {
        $sindicos = $this->service->listarAtivos();
        require __DIR__ . '/../Views/sindico/select.php';
    }

    public function index() {
        $sindicos = $this->service->listarTodos();
        require __DIR__ . '/../Views/sindico/index.php';
    }

    public function ficha() {
        $id = $_GET['id'] ?? null;
        if(!$id) {
            http_response_code(400);
            echo 'ID não fornecido.';
            return;
        }
        $sindico = $this->service->buscarPorId((int)$id);
        if (!$sindico) {
            http_response_code(404);
            echo 'Síndico não encontrado.';
            return;
        }
        $condominios = $this->service->getCondominios((int)$id);
        require __DIR__ . '/../Views/sindico/ficha.php';
    }

    public function update(): void {
        $id = $_POST['id'] ?? null;
        if(!$id) {
            header('Location: /?page=sindico&status=error&message=ID não fornecido');
            exit();
        }
        try {
            $this->service->atualizar((int)$id, [
                'nome' => $_POST['nome'] ?? '',
                'telefone' => $_POST['telefone'] ?? ''
            ]);
            header('Location: /?page=sindico&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: /?page=sindico&status=error&message=' . urlencode($e->getMessage()));
            exit();
        }
    }
}
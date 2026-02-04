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
            $fotoPath = null;
            $comprovantePath = null;

            // Processar upload de foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fotoPath = $this->uploadFile($_FILES['foto'], 'servicos');
            }

            // Processar upload de comprovante
            if (isset($_FILES['comprovante']) && $_FILES['comprovante']['error'] === UPLOAD_ERR_OK) {
                $comprovantePath = $this->uploadFile($_FILES['comprovante'], 'servicos');
            }

            $this->service->cadastrar([
                'id_cliente' => (int)$_POST['id_cliente'],
                'id_tipo' => (int)$_POST['id_tipo'],
                'descricao' => $_POST['descricao'] ?? null,
                'observacao' => $_POST['observacao'] ?? null,
                'foto' => $fotoPath,
                'comprovante' => $comprovantePath,
                'data_hora' => $_POST['data_hora'] ?? date('Y-m-d H:i:s')
            ]);

            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=servicos&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=servicos&status=error&message=' . urlencode($e->getMessage()));
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
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=servicos&status=error&message=ID não fornecido');
            exit();
        }
        try {
            $fotoPath = $_POST['foto_existing'] ?? null;
            $comprovantePath = $_POST['comprovante_existing'] ?? null;

            // Processar upload de foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fotoPath = $this->uploadFile($_FILES['foto'], 'servicos');
            }

            // Processar upload de comprovante
            if (isset($_FILES['comprovante']) && $_FILES['comprovante']['error'] === UPLOAD_ERR_OK) {
                $comprovantePath = $this->uploadFile($_FILES['comprovante'], 'servicos');
            }

            $this->service->atualizar((int)$id, [
                'id_cliente' => (int)$_POST['id_cliente'],
                'id_tipo' => (int)$_POST['id_tipo'],
                'descricao' => $_POST['descricao'] ?? null,
                'observacao' => $_POST['observacao'] ?? null,
                'foto' => $fotoPath,
                'comprovante' => $comprovantePath,
                'data_hora' => $_POST['data_hora'] ?? date('Y-m-d H:i:s')
            ]);
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=servicos&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/?page=servicos&status=error&message=' . urlencode($e->getMessage()));
            exit();
        }
    }

    private function uploadFile(array $file, string $subfolder): ?string
    {
        $uploadDir = __DIR__ . '/../../public/uploads/' . $subfolder . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return 'uploads/' . $subfolder . '/' . $fileName;
        }

        return null;
    }
}
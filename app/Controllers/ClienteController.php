<?php

namespace App\Controllers;

use App\Core\Database;
use App\Repositories\ClienteRepository;
use App\Repositories\SoftDeleteRepository;
use App\Services\ClienteService;

class ClienteController
{
    private ClienteService $service;

    public function __construct() {
        $db = Database::connect();
        $repo = new ClienteRepository($db);
        $softDelete = new SoftDeleteRepository();
        $this->service = new ClienteService($repo, $softDelete);
    }

    public function store(): void {
        try {
            $cnpjPath = null;
            if (isset($_FILES['cnpj']) && $_FILES['cnpj']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['cnpj'];
                $fileType = mime_content_type($file['tmp_name']);
                if ($fileType !== 'application/pdf') {
                    throw new \Exception('Apenas PDFs são permitidos para CNPJ.');
                }
                $uploadDir = __DIR__ . '/../../public/uploads/cnpj/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $fileName = uniqid() . '.pdf';
                $filePath = $uploadDir . $fileName;
                if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                    throw new \Exception('Erro ao salvar o arquivo CNPJ.');
                }
                $cnpjPath = 'uploads/cnpj/' . $fileName;
            }

            $this->service->cadastrar([
                'id_admin' => $_POST['id_admin'] ?? null,
                'id_sindico' => $_POST['id_sindico'] ?? null,
                'id_tipo_cliente' => (int)$_POST['id_tipo_cliente'],
                'telefone' => $_POST['telefone'] ?? '',
                'nome' => $_POST['nome'] ?? '',
                'email' => $_POST['email'] ?? null,
                'cnpj' => $cnpjPath,
                'rua' => $_POST['rua'] ?? '',
                'bairro' => $_POST['bairro'] ?? '',
                'numero' => (int)($_POST['numero'] ?? 0),
                'cidade' => $_POST['cidade'] ?? '',
                'complemento' => $_POST['complemento'] ?? ''
            ]);

            header('Location: /?page=clientes&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: /?page=clientes&status=error&message=' . urlencode($e->getMessage()));
            exit();
        }
    }

    public function obter() {
        $id = $_GET['id'] ?? null;
        if(!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID do cliente não fornecido.']);
            return;
        }
        $cliente = $this->service->buscarPorId((int)$id);
        if (!$cliente) {
            http_response_code(404);
            echo json_encode(['error' => 'Cliente não encontrado.']);
            return;
        }
        header('Content-Type: application/json');
        echo json_encode($cliente);
    }

    public function select() {
        $clientes = $this->service->listarAtivos();
        require __DIR__ . '/../Views/clients/select.php';
    }

    public function index() {
        $clientes = $this->service->listarTodos();
        require __DIR__ . '/../Views/clients/index.php';
    }

    public function ficha() {
        $id = $_GET['id'] ?? null;
        if(!$id) {
            http_response_code(400);
            echo 'ID não fornecido.';
            return;
        }
        $cliente = $this->service->buscarPorId((int)$id);
        if (!$cliente) {
            http_response_code(404);
            echo 'Cliente não encontrado.';
            return;
        }
        require __DIR__ . '/../Views/clients/ficha.php';
    }

    public function update(): void {
        $id = $_POST['id'] ?? null;
        if(!$id) {
            header('Location: /?page=clientes&status=error&message=ID não fornecido');
            exit();
        }
        try {
            $cnpjPath = $_POST['cnpj_existing'] ?? null; // Assume existing path if not uploading new
            if (isset($_FILES['cnpj']) && $_FILES['cnpj']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['cnpj'];
                $fileType = mime_content_type($file['tmp_name']);
                if ($fileType !== 'application/pdf') {
                    throw new \Exception('Apenas PDFs são permitidos para CNPJ.');
                }
                $uploadDir = __DIR__ . '/../../public/uploads/cnpj/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $fileName = uniqid() . '.pdf';
                $filePath = $uploadDir . $fileName;
                if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                    throw new \Exception('Erro ao salvar o arquivo CNPJ.');
                }
                $cnpjPath = 'uploads/cnpj/' . $fileName;
            }

            $this->service->atualizar((int)$id, [
                'id_admin' => $_POST['id_admin'] ?? null,
                'id_sindico' => $_POST['id_sindico'] ?? null,
                'id_tipo_cliente' => (int)$_POST['id_tipo_cliente'],
                'telefone' => $_POST['telefone'] ?? '',
                'nome' => $_POST['nome'] ?? '',
                'email' => $_POST['email'] ?? null,
                'cnpj' => $cnpjPath,
                'rua' => $_POST['rua'] ?? '',
                'bairro' => $_POST['bairro'] ?? '',
                'numero' => (int)($_POST['numero'] ?? 0),
                'cidade' => $_POST['cidade'] ?? '',
                'complemento' => $_POST['complemento'] ?? ''
            ]);
            header('Location: /?page=clientes&status=success');
            exit();
        } catch(\Throwable $e) {
            header('Location: /?page=clientes&status=error&message=' . urlencode($e->getMessage()));
            exit();
        }
    }
}
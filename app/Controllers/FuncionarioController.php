<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Repositories\FuncionarioRepository;
use App\Services\FuncionarioService;

class FuncionarioController
{
    private FuncionarioService $service;

    public function __construct()
    {
        $db = Database::connect();
        $repo = new FuncionarioRepository($db);
        $this->service = new FuncionarioService($repo);
    }

    public function index(): void
    {
        Auth::start();
        Auth::requireLogin();

        $usuarioLogado = Auth::user();
        $funcionarios = $this->service->listarVisiveis($usuarioLogado);
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        $isAdmin = !empty($usuarioLogado['is_admin']);
        $csrfDisableToken = Auth::csrfToken('funcionario_disable');

        require __DIR__ . '/../Views/funcionarios/index.php';
    }

    public function obter(): void
    {
        Auth::start();
        Auth::requireLogin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ID do funcionario nao informado.']);
            return;
        }

        try {
            $usuarioLogado = Auth::user();
            $funcionario = $this->service->buscarVisivelPorId($id, $usuarioLogado);
            if (!$funcionario) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Funcionario nao encontrado.']);
                return;
            }

            header('Content-Type: application/json');
            echo json_encode($funcionario);
        } catch (\Throwable $e) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function store(): void
    {
        Auth::start();
        Auth::requireLogin();

        if (!Auth::validateCsrfToken('funcionario_form', $_POST['_token'] ?? null)) {
            $this->redirectContas('error', 'Token de seguranca invalido.');
        }

        try {
            $this->service->cadastrar($_POST, Auth::user());
            $this->redirectContas('success', 'Conta cadastrada com sucesso.');
        } catch (\Throwable $e) {
            $this->redirectContas('error', $e->getMessage());
        }
    }

    public function update(): void
    {
        Auth::start();
        Auth::requireLogin();

        if (!Auth::validateCsrfToken('funcionario_form', $_POST['_token'] ?? null)) {
            $this->redirectContas('error', 'Token de seguranca invalido.');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->redirectContas('error', 'ID do funcionario nao informado.');
        }

        try {
            $this->service->atualizar($id, $_POST, Auth::user());
            $this->redirectContas('success', 'Conta atualizada com sucesso.');
        } catch (\Throwable $e) {
            $this->redirectContas('error', $e->getMessage());
        }
    }

    public function desativar(): void
    {
        Auth::start();
        Auth::requireLogin();

        if (!Auth::validateCsrfToken('funcionario_disable', $_POST['_token'] ?? null)) {
            $this->redirectContas('error', 'Token de seguranca invalido.');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->redirectContas('error', 'ID do funcionario nao informado.');
        }

        try {
            $this->service->desativar($id, Auth::user());
            $this->redirectContas('success', 'Conta desativada com sucesso.');
        } catch (\Throwable $e) {
            $this->redirectContas('error', $e->getMessage());
        }
    }

    private function redirectContas(string $status, string $message): void
    {
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        header(
            'Location: ' . $baseUrl . '/?page=contas&status=' . urlencode($status) .
            '&message=' . urlencode($message)
        );
        exit();
    }
}

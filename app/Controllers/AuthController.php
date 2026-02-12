<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Repositories\FuncionarioRepository;
use App\Services\FuncionarioService;

class AuthController
{
    private FuncionarioService $service;

    public function __construct()
    {
        $db = Database::connect();
        $repo = new FuncionarioRepository($db);
        $this->service = new FuncionarioService($repo);
    }

    public function login(): void
    {
        Auth::start();

        if (!Auth::validateCsrfToken('login', $_POST['_token'] ?? null)) {
            $this->redirectLogin('error', 'Token de seguranca invalido. Recarregue a pagina.');
        }

        try {
            $usuario = $this->service->autenticar(
                $_POST['email'] ?? '',
                $_POST['senha'] ?? ''
            );

            Auth::login($usuario);
            $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
            header('Location: ' . $baseUrl . '/');
            exit();
        } catch (\Throwable $e) {
            $this->redirectLogin('error', $e->getMessage());
        }
    }

    public function logout(): void
    {
        Auth::start();

        if (!Auth::validateCsrfToken('logout', $_POST['_token'] ?? null)) {
            $this->redirectLogin('error', 'Token de seguranca invalido.');
        }

        Auth::logout();
        $this->redirectLogin('success', 'Logout realizado com sucesso.');
    }

    private function redirectLogin(string $status, string $message): void
    {
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        header(
            'Location: ' . $baseUrl . '/login.php?status=' . urlencode($status) .
            '&message=' . urlencode($message)
        );
        exit();
    }
}

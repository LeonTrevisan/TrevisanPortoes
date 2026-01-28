<?php

namespace App\Controllers;

use App\Repositories\SoftDeleteRepository;

class SoftDeleteController
{
    private SoftDeleteRepository $softDeleteRepo;

    public function __construct()
    {
        $this->softDeleteRepo = new SoftDeleteRepository();
    }

    public function desativar(): void
    {
        $id     = (int) $_POST['id'];
        $tipo   = $_POST['tipo'];

        $tabela = match ($tipo) {
            'admin'   => 'admins',
            'cliente' => 'clientes',
            'sindico' => 'sindicos',
            default   => throw new \Exception('Tipo inválido')
        };

        $this->softDeleteRepo->desativar($tabela, $id);

        header('Location: /');
        exit;
    }

    public function reativar(): void
    {
        $id     = (int) $_POST['id'];
        $tipo   = $_POST['tipo'];

        $tabela = match ($tipo) {
            'admin'   => 'admins',
            'cliente' => 'clientes',
            'sindico' => 'sindicos',
            default   => throw new \Exception('Tipo inválido')
        };

        $this->softDeleteRepo->reativar($tabela, $id);

        header('Location: /');
        exit;
    }
}

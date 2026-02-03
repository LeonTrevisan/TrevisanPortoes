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
        $tabela = $_POST['tabela'];

        $this->softDeleteRepo->desativar($tabela, $id);

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function reativar(): void
    {
        $id     = (int) $_POST['id'];
        $tabela = $_POST['tabela'];

        $this->softDeleteRepo->reativar($tabela, $id);

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

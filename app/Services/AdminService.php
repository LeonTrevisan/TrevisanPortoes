<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\SoftDeleteRepository;

class AdminService
{
    public function __construct(private AdminRepository $repository, private SoftDeleteRepository $softDelete) {}

    public function cadastrar(array $dados): void
    {
        if(empty($dados['nome']) || empty($dados['email'])){
            throw new \InvalidArgumentException("Nome e email são obrigatórios.");
        }

        $this->repository->criar(
            $dados['nome'],
            $dados['telefone'] ?? '',
            $dados['email']
        );
    }

    public function desativar(int $id): void
    {
        $admin = $this->repository->buscarPorId($id);

        if (!$admin) {
            throw new \Exception("Admin não encontrado");
        }

        $this->softDelete->desativar('tb_admin_cond', $id, 'id_admin');
    }

    public function reativar(int $id): void
    {
        $this->softDelete->reativar('tb_admin_cond', $id);
    }

    public function listarAtivos(): array
    {
        return $this->repository->getAtivos();
    }

    public function listarTodos(): array
    {
        return $this->repository->getAll();
    }

    public function buscarPorId(int $id): ?array
    {
        return $this->repository->findById($id);
    }

    public function getCondominios(int $id): array
    {
        return $this->repository->getCondominios($id);
    }
}
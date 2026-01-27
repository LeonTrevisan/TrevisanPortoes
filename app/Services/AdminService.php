<?php

namespace App\Services;

use App\Repositories\AdminRepository;

class AdminService
{
    public function __construct(private AdminRepository $repository) {}

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
}
<?php

namespace App\Services;

use App\Repositories\ServicoRepository;

class ServicoService
{
    public function __construct(private ServicoRepository $repository) {}

    public function cadastrar(array $dados): int
    {
        if(empty($dados['id_cliente']) || empty($dados['id_tipo']) || empty($dados['data_hora'])){
            throw new \InvalidArgumentException("Cliente, tipo e data/hora são obrigatórios.");
        }

        return $this->repository->criar($dados);
    }

    public function listarTodos(): array
    {
        return $this->repository->getAll();
    }

    public function buscarPorId(int $id): ?array
    {
        return $this->repository->findById($id);
    }

    public function getByCliente(int $id_cliente): array
    {
        return $this->repository->getByCliente($id_cliente);
    }

    public function atualizar(int $id, array $dados): void
    {
        $this->repository->atualizar($id, $dados);
    }
}

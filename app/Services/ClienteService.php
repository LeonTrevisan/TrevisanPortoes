<?php

namespace App\Services;

use App\Repositories\ClienteRepository;
use App\Repositories\SoftDeleteRepository;

class ClienteService
{
    public function __construct(private ClienteRepository $repository, private SoftDeleteRepository $softDelete) {}

    public function cadastrar(array $dados): void
    {
        if(empty($dados['nome']) || empty($dados['telefone'])){
            throw new \InvalidArgumentException("Nome e telefone são obrigatórios.");
        }

        $this->repository->criar($dados);
    }

    public function desativar(int $id): void
    {
        $cliente = $this->repository->findById($id);
        if (!$cliente) {
            throw new \Exception("Cliente não encontrado");
        }
        $this->softDelete->desativar('tb_cliente', $id, 'id_cliente');
    }

    public function reativar(int $id): void
    {
        $this->softDelete->reativar('tb_cliente', $id, 'id_cliente');
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

    public function atualizar(int $id, array $dados): void
    {
        $this->repository->atualizar($id, $dados);
    }

    public function getServicos(int $id): array
    {
        // Assumindo que há um método no repositório
        // Por enquanto, retornar vazio
        return [];
    }
}
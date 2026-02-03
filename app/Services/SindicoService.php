<?php

namespace App\Services;

use App\Repositories\SindicoRepository;
use App\Repositories\SoftDeleteRepository;

class SindicoService
{
    public function __construct(private SindicoRepository $repository, private SoftDeleteRepository $softDelete) {}

    public function cadastrar(array $dados): void
    {
        if(empty($dados['nome']) || empty($dados['telefone'])){
            throw new \InvalidArgumentException("Nome e telefone são obrigatórios.");
        }

        $this->repository->criar($dados['nome'], $dados['telefone']);
    }

    public function desativar(int $id): void
    {
        $sindico = $this->repository->findById($id);
        if (!$sindico) {
            throw new \Exception("Síndico não encontrado");
        }
        $this->softDelete->desativar('tb_sindico', $id, 'id_sindico');
    }

    public function reativar(int $id): void
    {
        $this->softDelete->reativar('tb_sindico', $id, 'id_sindico');
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
        $this->repository->atualizar($id, $dados['nome'], $dados['telefone']);
    }

    public function getCondominios(int $id): array
    {
        return $this->repository->getCondominios($id);
    }
}
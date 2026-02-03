<?php

namespace App\Services;

use App\Repositories\CompraRepository;

class CompraService
{
    public function __construct(private CompraRepository $repository) {}

    public function cadastrar(array $dados): void
    {
        if(empty($dados['material']) || empty($dados['qtd_compra']) || empty($dados['valor_un'])){
            throw new \InvalidArgumentException("Material, quantidade e valor unitário são obrigatórios.");
        }

        $dados['valor_total'] = $dados['qtd_compra'] * $dados['valor_un'];
        $this->repository->criar($dados);
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
        $dados['valor_total'] = $dados['qtd_compra'] * $dados['valor_un'];
        $this->repository->atualizar($id, $dados);
    }
}
<?php

namespace App\Services;

use App\Repositories\PagamentoRepository;

class PagamentoService
{
    public function __construct(private PagamentoRepository $repository) {}

    public function upsertByServico(int $id_servico, int $id_status, ?int $id_forma_pagamento, float $valor): void
    {
        $this->repository->upsertByServico($id_servico, $id_status, $id_forma_pagamento, $valor);
    }

    public function buscarPorServico(int $id_servico): ?array
    {
        return $this->repository->findByServicoId($id_servico);
    }
}

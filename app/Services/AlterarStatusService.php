<?php

namespace App\Services;

use App\Models\Contracts\SoftDelete;

class ClienteStatusService
{
    public function desativar(
        SoftDelete $cliente,
        callable $persist
    ): void {
        if (!$cliente->estaAtivo()) {
            throw new \DomainException("Cliente já está desativado.");
        }

        $cliente->desativar();
        $persist($cliente->getDeletedAt());
    }

    public function reativar(
        SoftDelete $cliente,
        callable $persist
    ): void {
        $cliente->reativar();
        $persist(null);
    }
}

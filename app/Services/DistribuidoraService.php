<?php

namespace App\Services;

use App\Repositories\DistribuidoraRepository;

class DistribuidoraService
{
    public function __construct(private DistribuidoraRepository $repository) {}

    public function listarAtivos(): array
    {
        return $this->repository->getAtivos();
    }
}
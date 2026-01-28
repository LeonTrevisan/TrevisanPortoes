<?php

namespace App\Models\Contracts;

interface SoftDeletable
{
    public function desativar(): void;
    public function reativar(): void;
    public function estaAtivo(): bool;
}

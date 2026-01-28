<?php

namespace App\Models;

use App\Models\Contracts\SoftDelete;
use App\Models\Traits\SoftDeleteTrait;

class Admin implements SoftDeletable
{
    use SoftDeleteTrait;

    public function __construct(
        private int $id,
        private string $nome,
        private string $email,
        ?string $deleted_at = null
    ) {
        $this->deleted_at = $deleted_at;
    }

    public function getId(): int
    {
        return $this->id;
    }
}

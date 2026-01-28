<?php

namespace App\Models\Traits;

use DateTime;

trait SoftDeleteTrait
{
    protected ?string $deleted_at = null;

    public function desativar(): void
    {
        $this->deleted_at = (new DateTime())->format('Y-m-d H:i:s');
    }

    public function reativar(): void
    {
        $this->deleted_at = null;
    }

    public function estaAtivo(): bool
    {
        return $this->deleted_at === null;
    }

    public function getDeletedAt(): ?string
    {
        return $this->deleted_at;
    }
}

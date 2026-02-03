<?php

namespace App\Repositories;

use App\Core\Database;

class SoftDeleteRepository
{
    /**
     * Desativa um registro usando deleted_at
     */
    public function desativar(string $tabela, int $id, string $idColumn = 'id'): void
    {
        Database::execute(
            "UPDATE {$tabela} SET deleted_at = NOW() WHERE {$idColumn} = ?",
            [$id]
        );
    }

    /**
     * Reativa um registro
     */
    public function reativar(string $tabela, int $id, string $idColumn = 'id'): void
    {
        Database::execute(
            "UPDATE {$tabela} SET deleted_at = NULL WHERE {$idColumn} = ?",
            [$id]
        );
    }
}

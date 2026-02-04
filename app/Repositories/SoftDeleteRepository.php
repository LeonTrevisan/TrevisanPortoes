<?php

namespace App\Repositories;

use App\Core\Database;

class SoftDeleteRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Desativa um registro usando deleted_at
     */
    public function desativar(string $tabela, int $id, string $idColumn = null): void
    {
        if ($idColumn === null) {
            // Inferir o nome da coluna baseado na tabela
            $idColumn = $this->getIdColumn($tabela);
        }
        $stmt = $this->db->prepare("UPDATE {$tabela} SET deleted_at = NOW() WHERE {$idColumn} = ?");
        $stmt->execute([$id]);
    }

    /**
     * Reativa um registro
     */
    public function reativar(string $tabela, int $id, string $idColumn = null): void
    {
        if ($idColumn === null) {
            // Inferir o nome da coluna baseado na tabela
            $idColumn = $this->getIdColumn($tabela);
        }
        $stmt = $this->db->prepare("UPDATE {$tabela} SET deleted_at = NULL WHERE {$idColumn} = ?");
        $stmt->execute([$id]);
    }

    private function getIdColumn(string $tabela): string
    {
        $map = [
            'tb_admin_cond' => 'id_admin',
            'tb_cliente' => 'id_cliente',
            'tb_sindico' => 'id_sindico',
            'tb_servico' => 'id_servico',
            'tb_compra' => 'id_compra',
            'tb_tipo_cliente' => 'id_tipo_cliente',
            'tb_distribuidora' => 'id_distribuidora',
        ];
        return $map[$tabela] ?? 'id';
    }
}

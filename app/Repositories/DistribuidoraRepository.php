<?php

namespace App\Repositories;

use PDO;

class DistribuidoraRepository
{
    public function __construct(private PDO $db) {}

    public function getAtivos(): array
    {
        $stmt = $this->db->query("SELECT * FROM tb_distribuidora ORDER BY nome_distribuidora ASC");
        return $stmt->fetchAll();
    }
}
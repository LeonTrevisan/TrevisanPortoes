<?php

namespace App\Repositories;

use PDO;

class PagamentoRepository
{
    public function __construct(private PDO $db) {}

    public function findByServicoId(int $id_servico): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM tb_pagamento 
            WHERE id_servico = :id_servico
            LIMIT 1
        ");
        $stmt->execute([':id_servico' => $id_servico]);
        return $stmt->fetch() ?: null;
    }

    public function upsertByServico(int $id_servico, int $id_status, ?int $id_forma_pagamento, float $valor): void
    {
        $existing = $this->findByServicoId($id_servico);
        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE tb_pagamento
                SET id_forma_pagamento = :id_forma_pagamento,
                    valor = :valor,
                    id_status = :id_status
                WHERE id_pagamento = :id_pagamento
            ");
            $stmt->execute([
                ':id_forma_pagamento' => $id_forma_pagamento,
                ':valor' => $valor,
                ':id_status' => $id_status,
                ':id_pagamento' => $existing['id_pagamento']
            ]);
            return;
        }

        $stmt = $this->db->prepare("
            INSERT INTO tb_pagamento (id_servico, id_forma_pagamento, valor, id_status)
            VALUES (:id_servico, :id_forma_pagamento, :valor, :id_status)
        ");
        $stmt->execute([
            ':id_servico' => $id_servico,
            ':id_forma_pagamento' => $id_forma_pagamento,
            ':valor' => $valor,
            ':id_status' => $id_status
        ]);
    }
}

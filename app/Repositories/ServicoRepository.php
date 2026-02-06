<?php

namespace App\Repositories;

use PDO;

class ServicoRepository
{
    public function __construct(private PDO $db) {}

    public function criar(array $dados): int
    {
        $sql = "INSERT INTO tb_servico (id_cliente, id_tipo, descricao, observacao, foto, comprovante, data_hora) 
                VALUES (:id_cliente, :id_tipo, :descricao, :observacao, :foto, :comprovante, :data_hora)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($dados);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, c.nome as cliente_nome, ts.tipo_servico,
                   p.id_status, p.id_forma_pagamento, p.valor,
                   sp.status_pagamento, fp.forma_pagamento
            FROM tb_servico s
            JOIN tb_cliente c ON s.id_cliente = c.id_cliente
            JOIN tb_tipo_servico ts ON s.id_tipo = ts.id_tipo
            LEFT JOIN tb_pagamento p ON p.id_servico = s.id_servico
            LEFT JOIN tb_status_pagamento sp ON p.id_status = sp.id_status
            LEFT JOIN tb_forma_pagamento fp ON p.id_forma_pagamento = fp.id_forma_pagamento
            WHERE s.id_servico = :id
        ");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT s.*, c.nome as cliente_nome, ts.tipo_servico, sp.status_pagamento
            FROM tb_servico s
            JOIN tb_cliente c ON s.id_cliente = c.id_cliente
            JOIN tb_tipo_servico ts ON s.id_tipo = ts.id_tipo
            LEFT JOIN tb_pagamento p ON p.id_servico = s.id_servico
            LEFT JOIN tb_status_pagamento sp ON p.id_status = sp.id_status
            ORDER BY s.data_hora DESC
        ");
        return $stmt->fetchAll();
    }

    public function getByCliente(int $id_cliente): array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, ts.tipo_servico
            FROM tb_servico s
            JOIN tb_tipo_servico ts ON s.id_tipo = ts.id_tipo
            WHERE s.id_cliente = :id_cliente
            ORDER BY s.data_hora DESC
        ");
        $stmt->bindValue(':id_cliente', $id_cliente);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function atualizar(int $id, array $dados): void
    {
        $sql = "UPDATE tb_servico SET 
                id_cliente = :id_cliente, 
                id_tipo = :id_tipo, 
                descricao = :descricao, 
                observacao = :observacao, 
                foto = :foto, 
                comprovante = :comprovante, 
                data_hora = :data_hora 
                WHERE id_servico = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_merge($dados, [':id' => $id]));
    }
}

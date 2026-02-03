<?php

namespace App\Repositories;

use PDO;

class CompraRepository
{
    public function __construct(private PDO $db) {}

    public function criar(array $dados): void
    {
        $sql = "INSERT INTO tb_compras (data_compra, material, qtd_compra, valor_un, valor_total, id_distribuidora) 
                VALUES (:data_compra, :material, :qtd_compra, :valor_un, :valor_total, :id_distribuidora)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($dados);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, d.nome_distribuidora
            FROM tb_compras c
            LEFT JOIN tb_distribuidora d ON c.id_distribuidora = d.id_distribuidora
            WHERE c.id_compra = :id
        ");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT c.*, d.nome_distribuidora
            FROM tb_compras c
            LEFT JOIN tb_distribuidora d ON c.id_distribuidora = d.id_distribuidora
            ORDER BY c.data_compra DESC
        ");
        return $stmt->fetchAll();
    }

    public function atualizar(int $id, array $dados): void
    {
        $sql = "UPDATE tb_compras SET 
                data_compra = :data_compra, 
                material = :material, 
                qtd_compra = :qtd_compra, 
                valor_un = :valor_un, 
                valor_total = :valor_total, 
                id_distribuidora = :id_distribuidora 
                WHERE id_compra = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_merge($dados, [':id' => $id]));
    }
}
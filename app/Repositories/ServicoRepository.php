<?php

namespace App\Repositories;

use PDO;

class ServicoRepository
{
    public function __construct(private PDO $db) {}

    public function criar(array $dados): void
    {
        $sql = "INSERT INTO tb_servico (id_cliente, id_tipo, descricao, observacao, foto, comprovante, data_hora) 
                VALUES (:id_cliente, :id_tipo, :descricao, :observacao, :foto, :comprovante, :data_hora)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($dados);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, c.nome as cliente_nome, ts.tipo_servico
            FROM tb_servico s
            JOIN tb_cliente c ON s.id_cliente = c.id_cliente
            JOIN tb_tipo_servico ts ON s.id_tipo = ts.id_tipo
            WHERE s.id_servico = :id
        ");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT s.*, c.nome as cliente_nome, ts.tipo_servico
            FROM tb_servico s
            JOIN tb_cliente c ON s.id_cliente = c.id_cliente
            JOIN tb_tipo_servico ts ON s.id_tipo = ts.id_tipo
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
<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class SindicoRepository
{
    public function __construct(private PDO $db) {}

    public function criar(string $nome, string $telefone): void
    {
        $sql = "INSERT INTO tb_sindico (nome, telefone) VALUES (:nome, :telefone)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nome' => $nome, ':telefone' => $telefone]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM tb_sindico WHERE id_sindico = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM tb_sindico ORDER BY nome ASC");
        return $stmt->fetchAll();
    }

    public function getAtivos(): array
    {
        $stmt = $this->db->query("SELECT * FROM tb_sindico WHERE deleted_at IS NULL ORDER BY nome ASC");
        return $stmt->fetchAll();
    }

    public function atualizar(int $id, string $nome, string $telefone): void
    {
        $sql = "UPDATE tb_sindico SET nome = :nome, telefone = :telefone WHERE id_sindico = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nome' => $nome, ':telefone' => $telefone, ':id' => $id]);
    }

    public function atualizarDeletedAt(int $id, ?string $deletedAt): void
    {
        Database::execute(
            "UPDATE tb_sindico SET deleted_at = ? WHERE id_sindico = ?",
            [$deletedAt, $id]
        );
    }

    public function getCondominios(int $id_sindico): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, tc.tipo_cliente
            FROM tb_cliente c
            JOIN tb_tipo_cliente tc ON c.id_tipo_cliente = tc.id_tipo_cliente
            WHERE c.id_sindico = :id_sindico AND c.deleted_at IS NULL
        ");
        $stmt->bindValue(':id_sindico', $id_sindico);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
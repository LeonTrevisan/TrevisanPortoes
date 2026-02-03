<?php

namespace App\Repositories;

use App\Core\Database;

use PDO;

class AdminRepository
{
    public function __construct(private PDO $db) {}

    public function criar(string $nome, string $telefone, string $email): void
    {
        $sql = "INSERT INTO 
                tb_admin_cond (nome, telefone, email) 
                VALUES (:nome, :telefone, :email)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':telefone' => $telefone,
            ':email' => $email
        ]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM tb_admin_cond 
            WHERE id_admin = :id
        ");

        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

     public function atualizarDeletedAt(int $id, ?string $deletedAt): void
    {
        Database::execute(
            "UPDATE tb_admin_cond SET deleted_at = ? WHERE id_admin = ?",
            [$deletedAt, $id]
        );
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT * 
            FROM tb_admin_cond 
            ORDER BY nome ASC, deleted_at ASC
        ");

        return $stmt->fetchAll();
    }

    public function getAtivos(): array
    {
        $stmt = $this->db->query("
            SELECT * 
            FROM tb_admin_cond 
            WHERE deleted_at IS NULL
            ORDER BY nome ASC
        ");

        return $stmt->fetchAll();
    }
}
<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ClienteRepository
{
    public function __construct(private PDO $db) {}

    public function criar(array $dados): void
    {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO tb_cliente (id_admin, id_sindico, id_tipo_cliente, telefone, nome, email, cnpj) 
                    VALUES (:id_admin, :id_sindico, :id_tipo_cliente, :telefone, :nome, :email, :cnpj)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_admin' => $dados['id_admin'],
                ':id_sindico' => $dados['id_sindico'],
                ':id_tipo_cliente' => $dados['id_tipo_cliente'],
                ':telefone' => $dados['telefone'],
                ':nome' => $dados['nome'],
                ':email' => $dados['email'],
                ':cnpj' => $dados['cnpj']
            ]);

            $id_cliente = $this->db->lastInsertId();

            // Inserir endereço
            $sqlEndereco = "INSERT INTO tb_endereco (id_cliente, rua, bairro, numero, cidade, complemento) 
                            VALUES (:id_cliente, :rua, :bairro, :numero, :cidade, :complemento)";
            $stmtEndereco = $this->db->prepare($sqlEndereco);
            $stmtEndereco->execute([
                ':id_cliente' => $id_cliente,
                ':rua' => $dados['rua'] ?? '',
                ':bairro' => $dados['bairro'] ?? '',
                ':numero' => $dados['numero'] ?? 0,
                ':cidade' => $dados['cidade'] ?? '',
                ':complemento' => $dados['complemento'] ?? ''
            ]);

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, tc.tipo_cliente, a.nome as admin_nome, s.nome as sindico_nome,
                   e.rua, e.bairro, e.numero, e.cidade, e.complemento
            FROM tb_cliente c
            LEFT JOIN tb_tipo_cliente tc ON c.id_tipo_cliente = tc.id_tipo_cliente
            LEFT JOIN tb_admin_cond a ON c.id_admin = a.id_admin
            LEFT JOIN tb_sindico s ON c.id_sindico = s.id_sindico
            LEFT JOIN tb_endereco e ON c.id_cliente = e.id_cliente
            WHERE c.id_cliente = :id
        ");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT c.*, tc.tipo_cliente, a.nome as admin_nome, s.nome as sindico_nome,
                   e.rua, e.bairro, e.numero, e.cidade, e.complemento
            FROM tb_cliente c
            LEFT JOIN tb_tipo_cliente tc ON c.id_tipo_cliente = tc.id_tipo_cliente
            LEFT JOIN tb_admin_cond a ON c.id_admin = a.id_admin
            LEFT JOIN tb_sindico s ON c.id_sindico = s.id_sindico
            LEFT JOIN tb_endereco e ON c.id_cliente = e.id_cliente
            ORDER BY c.nome ASC
        ");
        return $stmt->fetchAll();
    }

    public function getAtivos(): array
    {
        $stmt = $this->db->query("
            SELECT c.*, tc.tipo_cliente, a.nome as admin_nome, s.nome as sindico_nome,
                   e.rua, e.bairro, e.numero, e.cidade, e.complemento
            FROM tb_cliente c
            LEFT JOIN tb_tipo_cliente tc ON c.id_tipo_cliente = tc.id_tipo_cliente
            LEFT JOIN tb_admin_cond a ON c.id_admin = a.id_admin
            LEFT JOIN tb_sindico s ON c.id_sindico = s.id_sindico
            LEFT JOIN tb_endereco e ON c.id_cliente = e.id_cliente
            WHERE c.deleted_at IS NULL
            ORDER BY c.nome ASC
        ");
        return $stmt->fetchAll();
    }

    public function atualizar(int $id, array $dados): void
    {
        $this->db->beginTransaction();
        try {
            $sql = "UPDATE tb_cliente SET 
                    id_admin = :id_admin, 
                    id_sindico = :id_sindico, 
                    id_tipo_cliente = :id_tipo_cliente, 
                    telefone = :telefone, 
                    nome = :nome, 
                    email = :email, 
                    cnpj = :cnpj 
                    WHERE id_cliente = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_admin' => $dados['id_admin'],
                ':id_sindico' => $dados['id_sindico'],
                ':id_tipo_cliente' => $dados['id_tipo_cliente'],
                ':telefone' => $dados['telefone'],
                ':nome' => $dados['nome'],
                ':email' => $dados['email'],
                ':cnpj' => $dados['cnpj'],
                ':id' => $id
            ]);

            // Verificar se endereço existe
            $stmtCheck = $this->db->prepare("SELECT id_endereco FROM tb_endereco WHERE id_cliente = :id_cliente");
            $stmtCheck->execute([':id_cliente' => $id]);
            $endereco = $stmtCheck->fetch();

            if ($endereco) {
                // Atualizar endereço
                $sqlEndereco = "UPDATE tb_endereco SET 
                                rua = :rua, 
                                bairro = :bairro, 
                                numero = :numero, 
                                cidade = :cidade, 
                                complemento = :complemento 
                                WHERE id_cliente = :id_cliente";
                $stmtEndereco = $this->db->prepare($sqlEndereco);
                $stmtEndereco->execute([
                    ':rua' => $dados['rua'] ?? '',
                    ':bairro' => $dados['bairro'] ?? '',
                    ':numero' => $dados['numero'] ?? 0,
                    ':cidade' => $dados['cidade'] ?? '',
                    ':complemento' => $dados['complemento'] ?? '',
                    ':id_cliente' => $id
                ]);
            } else {
                // Inserir endereço
                $sqlEndereco = "INSERT INTO tb_endereco (id_cliente, rua, bairro, numero, cidade, complemento) 
                                VALUES (:id_cliente, :rua, :bairro, :numero, :cidade, :complemento)";
                $stmtEndereco = $this->db->prepare($sqlEndereco);
                $stmtEndereco->execute([
                    ':id_cliente' => $id,
                    ':rua' => $dados['rua'] ?? '',
                    ':bairro' => $dados['bairro'] ?? '',
                    ':numero' => $dados['numero'] ?? 0,
                    ':cidade' => $dados['cidade'] ?? '',
                    ':complemento' => $dados['complemento'] ?? ''
                ]);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function atualizarDeletedAt(int $id, ?string $deletedAt): void
    {
        Database::execute(
            "UPDATE tb_cliente SET deleted_at = ? WHERE id_cliente = ?",
            [$deletedAt, $id]
        );
    }

    public function getServicos(int $id_cliente): array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, ts.tipo_servico, sp.status_pagamento
            FROM tb_servico s
            JOIN tb_tipo_servico ts ON s.id_tipo = ts.id_tipo
            LEFT JOIN tb_pagamento p ON p.id_servico = s.id_servico
            LEFT JOIN tb_status_pagamento sp ON p.id_status = sp.id_status
            WHERE s.id_cliente = :id_cliente
            ORDER BY s.data_hora DESC
        ");
        $stmt->bindValue(':id_cliente', $id_cliente);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

<?php

namespace App\Repositories;

use PDO;

class FuncionarioRepository
{
    public function __construct(private PDO $db) {}

    public function ensureSchema(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS tb_role_funcionario (
                id_role INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                role_nome VARCHAR(50) NOT NULL UNIQUE
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ");

        $this->seedDefaultRoles();

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS tb_funcionarios (
                id_funcionario INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(225) NOT NULL,
                senha VARCHAR(255) NOT NULL,
                id_role INT NOT NULL,
                deleted_at DATETIME NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_funcionarios_id_role (id_role),
                INDEX idx_funcionarios_deleted_at (deleted_at),
                UNIQUE KEY uk_funcionarios_email (email),
                CONSTRAINT fk_funcionario_role
                    FOREIGN KEY (id_role) REFERENCES tb_role_funcionario(id_role)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        ");

        if (!$this->columnExists('tb_funcionarios', 'id_role')) {
            $this->db->exec("ALTER TABLE tb_funcionarios ADD COLUMN id_role INT NULL AFTER senha");
        }

        if (!$this->columnExists('tb_funcionarios', 'deleted_at')) {
            $this->db->exec("ALTER TABLE tb_funcionarios ADD COLUMN deleted_at DATETIME NULL");
        }

        if (!$this->columnExists('tb_funcionarios', 'created_at')) {
            $this->db->exec("ALTER TABLE tb_funcionarios ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
        }

        if (!$this->columnExists('tb_funcionarios', 'updated_at')) {
            $this->db->exec("ALTER TABLE tb_funcionarios ADD COLUMN updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP");
        }

        if (!$this->indexExists('tb_funcionarios', 'uk_funcionarios_email')) {
            try {
                $this->db->exec("ALTER TABLE tb_funcionarios ADD UNIQUE KEY uk_funcionarios_email (email)");
            } catch (\Throwable $e) {
                // Ignora se houver dados duplicados legados.
            }
        }

        if (!$this->indexExists('tb_funcionarios', 'idx_funcionarios_id_role')) {
            $this->db->exec("ALTER TABLE tb_funcionarios ADD INDEX idx_funcionarios_id_role (id_role)");
        }

        if (!$this->indexExists('tb_funcionarios', 'idx_funcionarios_deleted_at')) {
            $this->db->exec("ALTER TABLE tb_funcionarios ADD INDEX idx_funcionarios_deleted_at (deleted_at)");
        }

        $this->migrateLegacyRoleText();

        $defaultRoleId = $this->getDefaultFuncionarioRoleId();
        $stmt = $this->db->prepare("
            UPDATE tb_funcionarios
            SET id_role = :id_role
            WHERE id_role IS NULL OR id_role = 0
        ");
        $stmt->execute([':id_role' => $defaultRoleId]);

        if ($this->columnIsNullable('tb_funcionarios', 'id_role')) {
            try {
                $this->db->exec("ALTER TABLE tb_funcionarios MODIFY COLUMN id_role INT NOT NULL");
            } catch (\Throwable $e) {
                // Mantem como nula se houver dados invalidos legados.
            }
        }

        if (!$this->foreignKeyExists('tb_funcionarios', 'fk_funcionario_role')) {
            try {
                $this->db->exec("
                    ALTER TABLE tb_funcionarios
                    ADD CONSTRAINT fk_funcionario_role
                    FOREIGN KEY (id_role) REFERENCES tb_role_funcionario(id_role)
                    ON DELETE RESTRICT ON UPDATE CASCADE
                ");
            } catch (\Throwable $e) {
                // Ignora se o banco nao suportar adicionar FK no estado atual.
            }
        }
    }

    public function seedDefaultAdmin(string $email, string $plainPassword): void
    {
        $adminRoleId = $this->getDefaultAdminRoleId();

        $stmt = $this->db->prepare("SELECT id_funcionario FROM tb_funcionarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $id = $stmt->fetchColumn();

        if ($id) {
            $update = $this->db->prepare("
                UPDATE tb_funcionarios
                SET deleted_at = NULL, id_role = :id_role
                WHERE id_funcionario = :id
            ");
            $update->execute([
                ':id' => (int)$id,
                ':id_role' => $adminRoleId,
            ]);
            return;
        }

        $insert = $this->db->prepare("
            INSERT INTO tb_funcionarios (nome, email, senha, id_role)
            VALUES (:nome, :email, :senha, :id_role)
        ");
        $insert->execute([
            ':nome' => 'Administrador Master',
            ':email' => $email,
            ':senha' => password_hash($plainPassword, PASSWORD_DEFAULT),
            ':id_role' => $adminRoleId,
        ]);
    }

    public function listRoles(): array
    {
        $stmt = $this->db->query("
            SELECT id_role, role_nome
            FROM tb_role_funcionario
            ORDER BY id_role ASC
        ");
        return $stmt->fetchAll();
    }

    public function findRoleById(int $idRole): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id_role, role_nome
            FROM tb_role_funcionario
            WHERE id_role = :id_role
            LIMIT 1
        ");
        $stmt->execute([':id_role' => $idRole]);
        return $stmt->fetch() ?: null;
    }

    public function isAdminRoleId(int $idRole): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM tb_role_funcionario
            WHERE id_role = :id_role
              AND LOWER(role_nome) LIKE '%admin%'
        ");
        $stmt->execute([':id_role' => $idRole]);
        return ((int)$stmt->fetchColumn()) > 0;
    }

    public function findByEmail(string $email, bool $includeDisabled = false): ?array
    {
        $sql = "
            SELECT
                f.*,
                r.role_nome,
                CASE
                    WHEN LOWER(r.role_nome) LIKE '%admin%' THEN 1
                    ELSE 0
                END AS is_admin
            FROM tb_funcionarios f
            LEFT JOIN tb_role_funcionario r ON r.id_role = f.id_role
            WHERE f.email = :email
        ";
        if (!$includeDisabled) {
            $sql .= " AND f.deleted_at IS NULL";
        }
        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch() ?: null;
    }

    public function findById(int $id, bool $includeDisabled = true): ?array
    {
        $sql = "
            SELECT
                f.*,
                r.role_nome,
                CASE
                    WHEN LOWER(r.role_nome) LIKE '%admin%' THEN 1
                    ELSE 0
                END AS is_admin
            FROM tb_funcionarios f
            LEFT JOIN tb_role_funcionario r ON r.id_role = f.id_role
            WHERE f.id_funcionario = :id
        ";
        if (!$includeDisabled) {
            $sql .= " AND f.deleted_at IS NULL";
        }
        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function emailInUseByAnother(string $email, int $id): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM tb_funcionarios
            WHERE email = :email AND id_funcionario <> :id
        ");
        $stmt->execute([
            ':email' => $email,
            ':id' => $id,
        ]);

        return ((int)$stmt->fetchColumn()) > 0;
    }

    public function create(string $nome, string $email, string $senhaHash, int $idRole): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO tb_funcionarios (nome, email, senha, id_role)
            VALUES (:nome, :email, :senha, :id_role)
        ");
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senhaHash,
            ':id_role' => $idRole,
        ]);
    }

    public function update(int $id, array $fields): void
    {
        if (empty($fields)) {
            return;
        }

        $allowed = ['nome', 'email', 'id_role', 'senha'];
        $updates = [];
        $params = [':id' => $id];

        foreach ($fields as $field => $value) {
            if (!in_array($field, $allowed, true)) {
                continue;
            }

            $param = ':' . $field;
            $updates[] = "{$field} = {$param}";
            $params[$param] = $value;
        }

        if (empty($updates)) {
            return;
        }

        $sql = "UPDATE tb_funcionarios SET " . implode(', ', $updates) . " WHERE id_funcionario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function disable(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE tb_funcionarios SET deleted_at = NOW() WHERE id_funcionario = :id");
        $stmt->execute([':id' => $id]);
    }

    public function countActiveAdmins(?int $excludeId = null): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM tb_funcionarios f
            JOIN tb_role_funcionario r ON r.id_role = f.id_role
            WHERE f.deleted_at IS NULL
              AND LOWER(r.role_nome) LIKE '%admin%'
        ";
        $params = [];

        if ($excludeId !== null) {
            $sql .= " AND f.id_funcionario <> :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    public function listAllForManagement(): array
    {
        $stmt = $this->db->query("
            SELECT
                f.id_funcionario,
                f.nome,
                f.email,
                f.id_role,
                r.role_nome,
                f.deleted_at,
                f.created_at,
                CASE
                    WHEN LOWER(r.role_nome) LIKE '%admin%' THEN 1
                    ELSE 0
                END AS is_admin
            FROM tb_funcionarios f
            LEFT JOIN tb_role_funcionario r ON r.id_role = f.id_role
            ORDER BY f.deleted_at IS NOT NULL, f.nome ASC
        ");
        return $stmt->fetchAll();
    }

    public function getDefaultAdminRoleId(): int
    {
        $stmt = $this->db->query("
            SELECT id_role
            FROM tb_role_funcionario
            WHERE LOWER(role_nome) LIKE '%admin%'
            ORDER BY id_role ASC
            LIMIT 1
        ");
        $id = $stmt->fetchColumn();
        if ($id) {
            return (int)$id;
        }

        $fallback = $this->db->query("SELECT id_role FROM tb_role_funcionario ORDER BY id_role ASC LIMIT 1")->fetchColumn();
        if ($fallback) {
            return (int)$fallback;
        }

        throw new \RuntimeException('Nenhum role cadastrado na tabela tb_role_funcionario.');
    }

    public function getDefaultFuncionarioRoleId(): int
    {
        $stmt = $this->db->query("
            SELECT id_role
            FROM tb_role_funcionario
            WHERE LOWER(role_nome) LIKE '%func%'
            ORDER BY id_role ASC
            LIMIT 1
        ");
        $id = $stmt->fetchColumn();
        if ($id) {
            return (int)$id;
        }

        $stmt = $this->db->query("
            SELECT id_role
            FROM tb_role_funcionario
            WHERE LOWER(role_nome) NOT LIKE '%admin%'
            ORDER BY id_role ASC
            LIMIT 1
        ");
        $fallback = $stmt->fetchColumn();
        if ($fallback) {
            return (int)$fallback;
        }

        return $this->getDefaultAdminRoleId();
    }

    private function migrateLegacyRoleText(): void
    {
        if (!$this->columnExists('tb_funcionarios', 'role') || !$this->columnExists('tb_funcionarios', 'id_role')) {
            return;
        }

        $stmt = $this->db->query("
            SELECT id_funcionario, role
            FROM tb_funcionarios
            WHERE (id_role IS NULL OR id_role = 0)
              AND role IS NOT NULL
              AND role <> ''
        ");
        $rows = $stmt->fetchAll();
        if (empty($rows)) {
            return;
        }

        $update = $this->db->prepare("
            UPDATE tb_funcionarios
            SET id_role = :id_role
            WHERE id_funcionario = :id_funcionario
        ");

        foreach ($rows as $row) {
            $roleText = strtolower((string)$row['role']);
            $roleId = strpos($roleText, 'admin') !== false
                ? $this->getDefaultAdminRoleId()
                : $this->getDefaultFuncionarioRoleId();

            $update->execute([
                ':id_role' => $roleId,
                ':id_funcionario' => (int)$row['id_funcionario'],
            ]);
        }
    }

    private function seedDefaultRoles(): void
    {
        if (!$this->hasRoleLike('%admin%')) {
            $stmt = $this->db->prepare("INSERT INTO tb_role_funcionario (role_nome) VALUES (:role_nome)");
            $stmt->execute([':role_nome' => 'Administradores']);
        }

        if (!$this->hasRoleLike('%func%')) {
            $stmt = $this->db->prepare("INSERT INTO tb_role_funcionario (role_nome) VALUES (:role_nome)");
            $stmt->execute([':role_nome' => 'Funcionarios']);
        }
    }

    private function hasRoleLike(string $pattern): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM tb_role_funcionario
            WHERE LOWER(role_nome) LIKE :pattern
        ");
        $stmt->execute([':pattern' => strtolower($pattern)]);
        return ((int)$stmt->fetchColumn()) > 0;
    }

    private function columnExists(string $table, string $column): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name
              AND COLUMN_NAME = :column_name
        ");
        $stmt->execute([
            ':table_name' => $table,
            ':column_name' => $column,
        ]);

        return ((int)$stmt->fetchColumn()) > 0;
    }

    private function columnIsNullable(string $table, string $column): bool
    {
        $stmt = $this->db->prepare("
            SELECT IS_NULLABLE
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name
              AND COLUMN_NAME = :column_name
            LIMIT 1
        ");
        $stmt->execute([
            ':table_name' => $table,
            ':column_name' => $column,
        ]);

        $value = $stmt->fetchColumn();
        return strtoupper((string)$value) === 'YES';
    }

    private function indexExists(string $table, string $index): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name
              AND INDEX_NAME = :index_name
        ");
        $stmt->execute([
            ':table_name' => $table,
            ':index_name' => $index,
        ]);

        return ((int)$stmt->fetchColumn()) > 0;
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM information_schema.REFERENTIAL_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name
              AND CONSTRAINT_NAME = :constraint_name
        ");
        $stmt->execute([
            ':table_name' => $table,
            ':constraint_name' => $constraint,
        ]);

        return ((int)$stmt->fetchColumn()) > 0;
    }
}

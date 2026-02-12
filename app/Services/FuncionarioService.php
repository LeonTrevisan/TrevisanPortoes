<?php

namespace App\Services;

use App\Repositories\FuncionarioRepository;

class FuncionarioService
{
    public function __construct(private FuncionarioRepository $repository)
    {
        $this->repository->ensureSchema();
        $this->repository->seedDefaultAdmin('admin@gmail.com', 'admin123');
    }

    public function listarRoles(): array
    {
        return $this->repository->listRoles();
    }

    public function autenticar(string $email, string $senha): array
    {
        $email = strtolower(trim($email));
        $senha = trim($senha);

        if ($email === '' || $senha === '') {
            throw new \InvalidArgumentException('Email e senha sao obrigatorios.');
        }

        $usuario = $this->repository->findByEmail($email, true);
        if (!$usuario || !empty($usuario['deleted_at'])) {
            throw new \RuntimeException('Email ou senha invalidos.');
        }

        if (!password_verify($senha, $usuario['senha'])) {
            throw new \RuntimeException('Email ou senha invalidos.');
        }

        return $this->sanitizeUser($usuario);
    }

    public function listarVisiveis(array $usuarioLogado): array
    {
        if (!empty($usuarioLogado['is_admin'])) {
            return $this->repository->listAllForManagement();
        }

        $proprio = $this->repository->findById((int)$usuarioLogado['id_funcionario'], true);
        return $proprio ? [$this->sanitizeForListing($proprio)] : [];
    }

    public function buscarVisivelPorId(int $id, array $usuarioLogado): ?array
    {
        $usuario = $this->repository->findById($id, true);
        if (!$usuario) {
            return null;
        }

        $isAdmin = !empty($usuarioLogado['is_admin']);
        $isOwner = (int)$usuarioLogado['id_funcionario'] === $id;

        if (!$isAdmin && !$isOwner) {
            throw new \RuntimeException('Voce nao possui permissao para acessar este usuario.');
        }

        return $this->sanitizeForListing($usuario);
    }

    public function cadastrar(array $dados, array $usuarioLogado): void
    {
        $this->assertAdmin($usuarioLogado);

        $nome = trim((string)($dados['nome'] ?? ''));
        $email = strtolower(trim((string)($dados['email'] ?? '')));
        $senha = trim((string)($dados['senha'] ?? ''));
        $idRole = (int)($dados['id_role'] ?? 0);

        if ($nome === '' || $email === '' || $senha === '') {
            throw new \InvalidArgumentException('Nome, email e senha sao obrigatorios.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email invalido.');
        }
        if ($idRole <= 0 || !$this->repository->findRoleById($idRole)) {
            throw new \InvalidArgumentException('Role invalido.');
        }
        if ($this->repository->findByEmail($email, true)) {
            throw new \InvalidArgumentException('Ja existe uma conta com este email.');
        }

        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $this->repository->create($nome, $email, $hash, $idRole);
    }

    public function atualizar(int $id, array $dados, array $usuarioLogado): void
    {
        $this->assertAdmin($usuarioLogado);

        $funcionario = $this->repository->findById($id, true);
        if (!$funcionario) {
            throw new \RuntimeException('Funcionario nao encontrado.');
        }

        $nome = trim((string)($dados['nome'] ?? ''));
        $email = strtolower(trim((string)($dados['email'] ?? '')));
        $senha = trim((string)($dados['senha'] ?? ''));
        $idRole = (int)($dados['id_role'] ?? (int)$funcionario['id_role']);

        if ($nome === '' || $email === '') {
            throw new \InvalidArgumentException('Nome e email sao obrigatorios.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email invalido.');
        }
        if ($idRole <= 0 || !$this->repository->findRoleById($idRole)) {
            throw new \InvalidArgumentException('Role invalido.');
        }
        if ($this->repository->emailInUseByAnother($email, $id)) {
            throw new \InvalidArgumentException('Email ja cadastrado para outro usuario.');
        }

        $funcionarioEraAdmin = !empty($funcionario['is_admin']);
        $funcionarioContinuaraAdmin = $this->repository->isAdminRoleId($idRole);
        if ($funcionarioEraAdmin && !$funcionarioContinuaraAdmin) {
            $adminsRestantes = $this->repository->countActiveAdmins($id);
            if ($adminsRestantes < 1) {
                throw new \RuntimeException('Nao e permitido remover o ultimo administrador ativo.');
            }
        }

        $payload = [
            'nome' => $nome,
            'email' => $email,
            'id_role' => $idRole,
        ];

        if ($senha !== '') {
            $payload['senha'] = password_hash($senha, PASSWORD_DEFAULT);
        }

        $this->repository->update($id, $payload);
    }

    public function desativar(int $id, array $usuarioLogado): void
    {
        $this->assertAdmin($usuarioLogado);

        if ((int)$usuarioLogado['id_funcionario'] === $id) {
            throw new \RuntimeException('Nao e permitido desativar a propria conta logada.');
        }

        $funcionario = $this->repository->findById($id, true);
        if (!$funcionario) {
            throw new \RuntimeException('Funcionario nao encontrado.');
        }
        if (!empty($funcionario['deleted_at'])) {
            throw new \RuntimeException('Conta ja esta desativada.');
        }

        if (!empty($funcionario['is_admin'])) {
            $adminsRestantes = $this->repository->countActiveAdmins($id);
            if ($adminsRestantes < 1) {
                throw new \RuntimeException('Nao e permitido desativar o ultimo administrador ativo.');
            }
        }

        $this->repository->disable($id);
    }

    private function assertAdmin(array $usuarioLogado): void
    {
        if (empty($usuarioLogado['is_admin'])) {
            throw new \RuntimeException('Apenas administradores podem executar esta acao.');
        }
    }

    private function sanitizeUser(array $usuario): array
    {
        return [
            'id_funcionario' => (int)$usuario['id_funcionario'],
            'nome' => (string)$usuario['nome'],
            'email' => (string)$usuario['email'],
            'id_role' => (int)($usuario['id_role'] ?? 0),
            'role_nome' => (string)($usuario['role_nome'] ?? ''),
            'is_admin' => !empty($usuario['is_admin']),
        ];
    }

    private function sanitizeForListing(array $usuario): array
    {
        $dados = $this->sanitizeUser($usuario);
        $dados['deleted_at'] = $usuario['deleted_at'] ?? null;
        $dados['created_at'] = $usuario['created_at'] ?? null;
        return $dados;
    }
}

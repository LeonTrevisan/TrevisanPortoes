<?php

namespace App\Models;

class Cliente
{
    public function __construct(
        private ?int $id,
        private ?int $id_admin,
        private ?int $id_sindico,
        private int $id_tipo_cliente,
        private string $telefone,
        private string $nome,
        private ?string $email,
        private ?string $cnpj,
        private ?string $deleted_at = null
    ) {}

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getIdAdmin(): ?int { return $this->id_admin; }
    public function getIdSindico(): ?int { return $this->id_sindico; }
    public function getIdTipoCliente(): int { return $this->id_tipo_cliente; }
    public function getTelefone(): string { return $this->telefone; }
    public function getNome(): string { return $this->nome; }
    public function getEmail(): ?string { return $this->email; }
    public function getCnpj(): ?string { return $this->cnpj; }
    public function getDeletedAt(): ?string { return $this->deleted_at; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setIdAdmin(?int $id_admin): void { $this->id_admin = $id_admin; }
    public function setIdSindico(?int $id_sindico): void { $this->id_sindico = $id_sindico; }
    public function setIdTipoCliente(int $id_tipo_cliente): void { $this->id_tipo_cliente = $id_tipo_cliente; }
    public function setTelefone(string $telefone): void { $this->telefone = $telefone; }
    public function setNome(string $nome): void { $this->nome = $nome; }
    public function setEmail(?string $email): void { $this->email = $email; }
    public function setCnpj(?string $cnpj): void { $this->cnpj = $cnpj; }
    public function setDeletedAt(?string $deleted_at): void { $this->deleted_at = $deleted_at; }
}
<?php

namespace App\Models;

class Sindico
{
    public function __construct(
        private ?int $id,
        private string $nome,
        private string $telefone,
        private ?string $deleted_at = null
    ) {}

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function getTelefone(): string { return $this->telefone; }
    public function getDeletedAt(): ?string { return $this->deleted_at; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setNome(string $nome): void { $this->nome = $nome; }
    public function setTelefone(string $telefone): void { $this->telefone = $telefone; }
    public function setDeletedAt(?string $deleted_at): void { $this->deleted_at = $deleted_at; }
}
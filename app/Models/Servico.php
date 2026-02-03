<?php

namespace App\Models;

class Servico
{
    public function __construct(
        private ?int $id,
        private int $id_cliente,
        private int $id_tipo,
        private ?string $descricao,
        private ?string $observacao,
        private ?string $foto,
        private ?string $comprovante,
        private string $data_hora
    ) {}

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getIdCliente(): int { return $this->id_cliente; }
    public function getIdTipo(): int { return $this->id_tipo; }
    public function getDescricao(): ?string { return $this->descricao; }
    public function getObservacao(): ?string { return $this->observacao; }
    public function getFoto(): ?string { return $this->foto; }
    public function getComprovante(): ?string { return $this->comprovante; }
    public function getDataHora(): string { return $this->data_hora; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setIdCliente(int $id_cliente): void { $this->id_cliente = $id_cliente; }
    public function setIdTipo(int $id_tipo): void { $this->id_tipo = $id_tipo; }
    public function setDescricao(?string $descricao): void { $this->descricao = $descricao; }
    public function setObservacao(?string $observacao): void { $this->observacao = $observacao; }
    public function setFoto(?string $foto): void { $this->foto = $foto; }
    public function setComprovante(?string $comprovante): void { $this->comprovante = $comprovante; }
    public function setDataHora(string $data_hora): void { $this->data_hora = $data_hora; }
}
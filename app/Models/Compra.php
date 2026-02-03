<?php

namespace App\Models;

class Compra
{
    public function __construct(
        private ?int $id,
        private string $data_compra,
        private string $material,
        private int $qtd_compra,
        private float $valor_un,
        private float $valor_total,
        private ?int $id_distribuidora
    ) {}

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getDataCompra(): string { return $this->data_compra; }
    public function getMaterial(): string { return $this->material; }
    public function getQtdCompra(): int { return $this->qtd_compra; }
    public function getValorUn(): float { return $this->valor_un; }
    public function getValorTotal(): float { return $this->valor_total; }
    public function getIdDistribuidora(): ?int { return $this->id_distribuidora; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setDataCompra(string $data_compra): void { $this->data_compra = $data_compra; }
    public function setMaterial(string $material): void { $this->material = $material; }
    public function setQtdCompra(int $qtd_compra): void { $this->qtd_compra = $qtd_compra; }
    public function setValorUn(float $valor_un): void { $this->valor_un = $valor_un; }
    public function setValorTotal(float $valor_total): void { $this->valor_total = $valor_total; }
    public function setIdDistribuidora(?int $id_distribuidora): void { $this->id_distribuidora = $id_distribuidora; }
}
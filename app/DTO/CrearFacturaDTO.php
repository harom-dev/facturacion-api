<?php

namespace App\DTO;

class CrearFacturaDTO
{
    public function __construct(
        public string $serie,
        public string $numero,
        public int $cliente_id,
        public string $fecha,
        public array $items, // [['producto' => '', 'cantidad' => 1, 'precio' => 10.00], ...]
        public float $subtotal,
        public float $impuesto,
    ) {}

    public function getTotal(): float
    {
        return $this->subtotal + $this->impuesto;
    }
}
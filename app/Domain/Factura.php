<?php

namespace App\Domain;

class Factura
{
    public function __construct(
        public int $id,
        public string $serie,
        public string $numero,
        public string $fecha,
        public array $cliente,
        public array $items,
        public float $subtotal,
        public float $impuesto,
        public float $total,
        public string $estado,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'serie' => $this->serie,
            'numero' => $this->numero,
            'fecha' => $this->fecha,
            'cliente' => $this->cliente,
            'items' => $this->items,
            'subtotal' => $this->subtotal,
            'impuesto' => $this->impuesto,
            'total' => $this->total,
            'estado' => $this->estado,
        ];
    }

    public function cambiarEstado(string $nuevoEstado): void
    {
        if (!in_array($nuevoEstado, ['pendiente', 'pagada', 'anulada'])) {
            throw new \InvalidArgumentException("Estado inválido: {$nuevoEstado}");
        }
        $this->estado = $nuevoEstado;
    }

    public function esAnulable(): bool
    {
        return $this->estado !== 'anulada';
    }

    public function calcularTotal(): float
    {
        return $this->subtotal + $this->impuesto;
    }
}
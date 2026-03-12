<?php

namespace App\Domain;

class Factura
{
    public function __construct(
        public int $id,
        public int $empresa_id,
        public string $serie,
        public string $numero,
        public string $tipo,
        public string $fecha,
        public string $cliente_nombre,
        public string $cliente_documento,
        public ?string $cliente_email,
        public array $items,
        public float $subtotal,
        public float $impuesto,
        public float $total,
        public string $estado,
        public string $estado_sunat = 'pendiente',
    ) {}

    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'empresa_id'        => $this->empresa_id,
            'serie'             => $this->serie,
            'numero'            => $this->numero,
            'tipo'              => $this->tipo,
            'fecha'             => $this->fecha,
            'cliente_nombre'    => $this->cliente_nombre,
            'cliente_documento' => $this->cliente_documento,
            'cliente_email'     => $this->cliente_email,
            'items'             => $this->items,
            'subtotal'          => $this->subtotal,
            'impuesto'          => $this->impuesto,
            'total'             => $this->total,
            'estado'            => $this->estado,
            'estado_sunat'      => $this->estado_sunat,
        ];
    }

    public function cambiarEstado(string $nuevoEstado): void
    {
        if (!in_array($nuevoEstado, ['pendiente', 'pagada', 'anulada'])) {
            throw new \InvalidArgumentException("Estado inválido: {$nuevoEstado}");
        }

        if ($this->estado === 'anulada') {
            throw new \LogicException('No se puede cambiar el estado de una factura anulada');
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

<?php

namespace App\DTO;

class CrearFacturaDTO
{
    private string $serie;
    private int $cliente_id;
    private array $items;
    private string $fecha;
    private int $empresa_id;

    public function __construct(
        string $serie,
        int $cliente_id,
        array $items,
        string $fecha,
        int $empresa_id = 1
    ) {
        $this->validar($serie, $cliente_id, $items, $fecha);
        
        $this->serie = $serie;
        $this->cliente_id = $cliente_id;
        $this->items = $this->normalizarItems($items);
        $this->fecha = $fecha;
        $this->empresa_id = $empresa_id;
    }

    /**
     * Validaciones críticas para producción
     */
    private function validar(string $serie, int $cliente_id, array $items, string $fecha): void
    {
        // Validar serie
        if (empty($serie) || strlen($serie) > 10) {
            throw new \InvalidArgumentException('Serie inválida');
        }

        // Validar cliente_id
        if ($cliente_id <= 0) {
            throw new \InvalidArgumentException('cliente_id debe ser mayor a 0');
        }

        // Validar items
        if (empty($items)) {
            throw new \InvalidArgumentException('La factura debe tener al menos 1 item');
        }

        // Validar cada item
        foreach ($items as $key => $item) {
            if (!isset($item['descripcion']) || empty($item['descripcion'])) {
                throw new \InvalidArgumentException("Item {$key}: descripción requerida");
            }

            if (!isset($item['cantidad']) || $item['cantidad'] <= 0) {
                throw new \InvalidArgumentException("Item {$key}: cantidad debe ser mayor a 0");
            }

            if (!isset($item['precio_unitario']) || $item['precio_unitario'] < 0) {
                throw new \InvalidArgumentException("Item {$key}: precio_unitario debe ser >= 0");
            }
        }

        // Validar fecha
        if (!$this->esFormatoFechaValido($fecha)) {
            throw new \InvalidArgumentException('Fecha inválida (formato: Y-m-d)');
        }

        if (strtotime($fecha) > time()) {
            throw new \InvalidArgumentException('Fecha no puede ser futura');
        }
    }

    /**
     * Normalizar items: asegurar que están bien formados
     */
    private function normalizarItems(array $items): array
    {
        return array_map(function ($item) {
            return [
                'descripcion' => trim($item['descripcion'] ?? ''),
                'cantidad' => (int) ($item['cantidad'] ?? 0),
                'precio_unitario' => (float) ($item['precio_unitario'] ?? 0),
            ];
        }, $items);
    }

    private function esFormatoFechaValido(string $fecha): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $fecha);
        return $d && $d->format('Y-m-d') === $fecha;
    }

    // Getters
    public function getSerie(): string
    {
        return $this->serie;
    }

    public function getClienteId(): int
    {
        return $this->cliente_id;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getFecha(): string
    {
        return $this->fecha;
    }

    public function getEmpresaId(): int
    {
        return $this->empresa_id;
    }

    /**
     * Método para el Service: calcula el subtotal BASE (sin IGV)
     */
    public function calcularSubtotalBase(): float
    {
        return array_reduce($this->items, function ($total, $item) {
            return $total + ($item['cantidad'] * $item['precio_unitario']);
        }, 0);
    }
}
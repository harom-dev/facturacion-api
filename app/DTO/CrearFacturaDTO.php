<?php

namespace App\DTO;

class CrearFacturaDTO
{
    private string $serie;
    private string $tipo;
    private string $cliente_nombre;
    private string $cliente_documento;
    private ?string $cliente_email;
    private array $items;
    private string $fecha;
    private int $empresa_id;

    public function __construct(
        string $serie,
        string $tipo,
        string $cliente_nombre,
        string $cliente_documento,
        array $items,
        string $fecha,
        int $empresa_id,
        ?string $cliente_email = null,
    ) {
        $this->validar($serie, $tipo, $cliente_nombre, $cliente_documento, $items, $fecha);

        $this->serie = $serie;
        $this->tipo = $tipo;
        $this->cliente_nombre = trim($cliente_nombre);
        $this->cliente_documento = trim($cliente_documento);
        $this->cliente_email = $cliente_email;
        $this->items = $this->normalizarItems($items);
        $this->fecha = $fecha;
        $this->empresa_id = $empresa_id;
    }

    private function validar(string $serie, string $tipo, string $cliente_nombre, string $cliente_documento, array $items, string $fecha): void
    {
        if (empty($serie) || strlen($serie) > 10) {
            throw new \InvalidArgumentException('Serie inválida (máximo 10 caracteres)');
        }

        if (!in_array($tipo, ['factura', 'boleta'])) {
            throw new \InvalidArgumentException('Tipo inválido. Use: factura o boleta');
        }

        if (empty(trim($cliente_nombre))) {
            throw new \InvalidArgumentException('El nombre del cliente es requerido');
        }

        if (empty(trim($cliente_documento))) {
            throw new \InvalidArgumentException('El documento del cliente es requerido');
        }

        if (empty($items)) {
            throw new \InvalidArgumentException('La factura debe tener al menos 1 item');
        }

        foreach ($items as $key => $item) {
            if (!isset($item['descripcion']) || empty(trim($item['descripcion']))) {
                throw new \InvalidArgumentException("Item {$key}: descripción requerida");
            }

            if (!isset($item['cantidad']) || $item['cantidad'] <= 0) {
                throw new \InvalidArgumentException("Item {$key}: cantidad debe ser mayor a 0");
            }

            if (!isset($item['precio_unitario']) || $item['precio_unitario'] < 0) {
                throw new \InvalidArgumentException("Item {$key}: precio_unitario debe ser >= 0");
            }
        }

        if (!$this->esFormatoFechaValido($fecha)) {
            throw new \InvalidArgumentException('Fecha inválida (formato: Y-m-d)');
        }

        if (strtotime($fecha) > time()) {
            throw new \InvalidArgumentException('La fecha no puede ser futura');
        }
    }

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

    public function getSerie(): string { return $this->serie; }
    public function getTipo(): string { return $this->tipo; }
    public function getClienteNombre(): string { return $this->cliente_nombre; }
    public function getClienteDocumento(): string { return $this->cliente_documento; }
    public function getClienteEmail(): ?string { return $this->cliente_email; }
    public function getItems(): array { return $this->items; }
    public function getFecha(): string { return $this->fecha; }
    public function getEmpresaId(): int { return $this->empresa_id; }

    public function calcularSubtotalBase(): float
    {
        return array_reduce($this->items, function ($total, $item) {
            return $total + ($item['cantidad'] * $item['precio_unitario']);
        }, 0.0);
    }
}

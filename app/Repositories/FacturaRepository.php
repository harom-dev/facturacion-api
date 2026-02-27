<?php

namespace App\Repositories;

use App\Domain\Factura;

interface FacturaRepositoryInterface
{
    public function obtenerTodas(): array;
    public function obtenerPorId(int $id): ?Factura;
    public function crear(Factura $factura): Factura;
    public function actualizar(int $id, Factura $factura): ?Factura;
    public function eliminar(int $id): bool;
}

class FacturaRepository implements FacturaRepositoryInterface
{
    private array $facturas = [];
    private int $nextId = 1;

    public function __construct()
    {
        $this->inicializarDatosDemo();
    }

    private function inicializarDatosDemo(): void
    {
        $this->facturas = [
            new Factura(
                1,
                'F001',
                '000123',
                '2025-02-27',
                ['nombre' => 'Juan Pérez', 'documento' => '12345678'],
                [
                    ['producto' => 'Laptop', 'cantidad' => 1, 'precio' => 1000.00]
                ],
                1000.00,
                180.00,
                1180.00,
                'pagada'
            ),
            new Factura(
                2,
                'F001',
                '000124',
                '2025-02-26',
                ['nombre' => 'María García', 'documento' => '87654321'],
                [
                    ['producto' => 'Mouse', 'cantidad' => 2, 'precio' => 25.00]
                ],
                50.00,
                9.00,
                59.00,
                'pendiente'
            ),
        ];
        $this->nextId = 3;
    }

    public function obtenerTodas(): array
    {
        return array_values($this->facturas);
    }

    public function obtenerPorId(int $id): ?Factura
    {
        return $this->facturas[$id] ?? null;
    }

    public function crear(Factura $factura): Factura
    {
        $factura->id = $this->nextId++;
        $this->facturas[$factura->id] = $factura;
        return $factura;
    }

    public function actualizar(int $id, Factura $factura): ?Factura
    {
        if (!isset($this->facturas[$id])) {
            return null;
        }
        $factura->id = $id;
        $this->facturas[$id] = $factura;
        return $factura;
    }

    public function eliminar(int $id): bool
    {
        if (!isset($this->facturas[$id])) {
            return false;
        }
        unset($this->facturas[$id]);
        return true;
    }
}
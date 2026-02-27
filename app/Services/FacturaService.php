<?php

namespace App\Services;

use App\Domain\Factura;
use App\DTO\CrearFacturaDTO;
use App\Repositories\FacturaRepositoryInterface;

class FacturaService
{
    public function __construct(
        private FacturaRepositoryInterface $repository
    ) {}

    public function obtenerTodas(): array
    {
        $facturas = $this->repository->obtenerTodas();
        return array_map(fn(Factura $f) => $f->toArray(), $facturas);
    }

    public function obtenerPorId(int $id): ?array
    {
        $factura = $this->repository->obtenerPorId($id);
        return $factura ? $factura->toArray() : null;
    }

    public function crear(CrearFacturaDTO $dto): array
    {
        $factura = new Factura(
            id: 0, // será asignado por el repository
            serie: $dto->serie,
            numero: $dto->numero,
            fecha: $dto->fecha,
            cliente: ['id' => $dto->cliente_id],
            items: $dto->items,
            subtotal: $dto->subtotal,
            impuesto: $dto->impuesto,
            total: $dto->getTotal(),
            estado: 'pendiente'
        );

        $factura = $this->repository->crear($factura);
        return $factura->toArray();
    }

    public function actualizar(int $id, array $datos): ?array
    {
        $factura = $this->repository->obtenerPorId($id);
        
        if (!$factura) {
            return null;
        }

        if (isset($datos['estado'])) {
            $factura->cambiarEstado($datos['estado']);
        }

        $factura = $this->repository->actualizar($id, $factura);
        return $factura->toArray();
    }

    public function anular(int $id): ?array
    {
        $factura = $this->repository->obtenerPorId($id);
        
        if (!$factura || !$factura->esAnulable()) {
            return null;
        }

        $factura->cambiarEstado('anulada');
        $factura = $this->repository->actualizar($id, $factura);
        return $factura->toArray();
    }
}
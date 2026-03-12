<?php

namespace App\Services;

use App\Domain\Factura;
use App\DTO\CrearFacturaDTO;
use App\Models\Factura as FacturaModel;
use App\Repositories\FacturaRepositoryInterface;

class FacturaService
{
    private const IGV_RATE = 0.18;

    public function __construct(
        private FacturaRepositoryInterface $repository
    ) {}

    public function obtenerTodas(int $empresaId): array
    {
        $facturas = $this->repository->obtenerTodas($empresaId);
        return array_map(fn(Factura $f) => $f->toArray(), $facturas);
    }

    public function obtenerPorId(int $id, int $empresaId): ?array
    {
        $factura = $this->repository->obtenerPorId($id, $empresaId);
        return $factura ? $factura->toArray() : null;
    }

    public function crear(CrearFacturaDTO $dto): array
    {
        $subtotal = $dto->calcularSubtotalBase();
        $igv      = round($subtotal * self::IGV_RATE, 2);
        $total    = round($subtotal + $igv, 2);
        $numero   = $this->generarNumeroFactura($dto->getSerie(), $dto->getEmpresaId());

        $factura = new Factura(
            id:                0,
            empresa_id:        $dto->getEmpresaId(),
            serie:             $dto->getSerie(),
            numero:            $numero,
            tipo:              $dto->getTipo(),
            fecha:             $dto->getFecha(),
            cliente_nombre:    $dto->getClienteNombre(),
            cliente_documento: $dto->getClienteDocumento(),
            cliente_email:     $dto->getClienteEmail(),
            items:             $dto->getItems(),
            subtotal:          $subtotal,
            impuesto:          $igv,
            total:             $total,
            estado:            'pendiente',
            estado_sunat:      'pendiente',
        );

        $factura = $this->repository->crear($factura);
        return $factura->toArray();
    }

    public function actualizar(int $id, int $empresaId, array $datos): ?array
    {
        $factura = $this->repository->obtenerPorId($id, $empresaId);

        if (!$factura) {
            return null;
        }

        if (isset($datos['estado'])) {
            $factura->cambiarEstado($datos['estado']);
        }

        $factura = $this->repository->actualizar($id, $factura);
        return $factura->toArray();
    }

    public function anular(int $id, int $empresaId): ?array
    {
        $factura = $this->repository->obtenerPorId($id, $empresaId);

        if (!$factura || !$factura->esAnulable()) {
            return null;
        }

        $factura->cambiarEstado('anulada');
        $factura = $this->repository->actualizar($id, $factura);
        return $factura->toArray();
    }

    private function generarNumeroFactura(string $serie, int $empresaId): string
    {
        $ultimo = FacturaModel::where('empresa_id', $empresaId)
            ->where('serie', $serie)
            ->max('numero');

        $siguiente = $ultimo ? ((int) $ultimo) + 1 : 1;

        return str_pad($siguiente, 8, '0', STR_PAD_LEFT);
    }
}

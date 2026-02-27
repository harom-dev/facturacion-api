<?php

namespace App\Repositories;

use App\Domain\Factura as FacturaDomain;
use App\Models\Factura as FacturaModel;
use App\Models\ItemFactura;

interface FacturaRepositoryInterface
{
    public function obtenerTodas(int $empresaId): array;
    public function obtenerPorId(int $id, int $empresaId): ?FacturaDomain;
    public function crear(FacturaDomain $factura): FacturaDomain;
    public function actualizar(int $id, FacturaDomain $factura): ?FacturaDomain;
    public function eliminar(int $id): bool;
}

class FacturaRepository implements FacturaRepositoryInterface
{
    public function obtenerTodas(int $empresaId): array
    {
        $facturas = FacturaModel::where('empresa_id', $empresaId)
            ->with(['cliente', 'items'])
            ->get();

        return $facturas->map(fn($f) => $this->modeloToDomain($f))->toArray();
    }

    public function obtenerPorId(int $id, int $empresaId): ?FacturaDomain
    {
        $factura = FacturaModel::where('id', $id)
            ->where('empresa_id', $empresaId)
            ->with(['cliente', 'items'])
            ->first();

        return $factura ? $this->modeloToDomain($factura) : null;
    }

    public function crear(FacturaDomain $factura): FacturaDomain
    {
        $modelo = FacturaModel::create([
            'empresa_id' => $factura->empresa_id,
            'cliente_id' => $factura->cliente['id'],
            'serie' => $factura->serie,
            'numero' => $factura->numero,
            'fecha' => $factura->fecha,
            'subtotal' => $factura->subtotal,
            'impuesto' => $factura->impuesto,
            'total' => $factura->total,
            'estado' => $factura->estado,
        ]);

        // Crear items
        foreach ($factura->items as $item) {
            ItemFactura::create([
                'factura_id' => $modelo->id,
                'descripcion' => $item['descripcion'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'subtotal' => $item['cantidad'] * $item['precio_unitario'],
            ]);
        }

        $modelo->load(['cliente', 'items']);
        return $this->modeloToDomain($modelo);
    }

    public function actualizar(int $id, FacturaDomain $factura): ?FacturaDomain
    {
        $modelo = FacturaModel::find($id);

        if (!$modelo) {
            return null;
        }

        $modelo->update([
            'estado' => $factura->estado,
        ]);

        $modelo->load(['cliente', 'items']);
        return $this->modeloToDomain($modelo);
    }

    public function eliminar(int $id): bool
    {
        return FacturaModel::destroy($id) > 0;
    }

    /**
     * Convertir Modelo Eloquent a Domain
     */
    private function modeloToDomain(FacturaModel $modelo): FacturaDomain
    {
        return new FacturaDomain(
            id: $modelo->id,
            empresa_id: $modelo->empresa_id,
            serie: $modelo->serie,
            numero: $modelo->numero,
            fecha: $modelo->fecha->format('Y-m-d'),
            cliente: [
                'id' => $modelo->cliente_id,
                'nombre' => $modelo->cliente->nombre,
                'documento' => $modelo->cliente->documento,
            ],
            items: $modelo->items->map(fn($item) => [
                'descripcion' => $item->descripcion,
                'cantidad' => $item->cantidad,
                'precio_unitario' => $item->precio_unitario,
            ])->toArray(),
            subtotal: (float) $modelo->subtotal,
            impuesto: (float) $modelo->impuesto,
            total: (float) $modelo->total,
            estado: $modelo->estado,
        );
    }
}
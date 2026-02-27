<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FacturaService;
use App\DTO\CrearFacturaDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        private FacturaService $facturaService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Listado de facturas',
            'data' => $this->facturaService->obtenerTodas()
        ]);
    }

    public function show($id): JsonResponse
    {
        $factura = $this->facturaService->obtenerPorId($id);
        
        if (!$factura) {
            return response()->json([
                'status' => 'error',
                'message' => 'Factura no encontrada'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $factura
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'serie' => 'required|string|max:10',
            'numero' => 'required|string|max:20|unique:facturas',
            'cliente_id' => 'required|integer',
            'fecha' => 'required|date',
            'items' => 'required|array',
            'subtotal' => 'required|numeric|min:0',
            'impuesto' => 'required|numeric|min:0',
        ]);

        $dto = new CrearFacturaDTO(
            serie: $validated['serie'],
            numero: $validated['numero'],
            cliente_id: $validated['cliente_id'],
            fecha: $validated['fecha'],
            items: $validated['items'],
            subtotal: (float) $validated['subtotal'],
            impuesto: (float) $validated['impuesto'],
        );

        $factura = $this->facturaService->crear($dto);

        return response()->json([
            'status' => 'success',
            'message' => 'Factura creada',
            'data' => $factura
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $factura = $this->facturaService->actualizar($id, $request->all());

        if (!$factura) {
            return response()->json([
                'status' => 'error',
                'message' => 'Factura no encontrada'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Factura actualizada',
            'data' => $factura
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $factura = $this->facturaService->anular($id);

        if (!$factura) {
            return response()->json([
                'status' => 'error',
                'message' => 'Factura no encontrada o ya anulada'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Factura anulada',
            'data' => $factura
        ]);
    }
}
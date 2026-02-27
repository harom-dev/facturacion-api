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

    private function getEmpresaId(Request $request): int
    {
        // TODO: Cuando tengas JWT, extraerlo del token
        // Por ahora, del request
        return (int) $request->input('empresa_id', 1);
    }

    public function index(Request $request): JsonResponse
    {
        $empresaId = $this->getEmpresaId($request);

        return response()->json([
            'status' => 'success',
            'message' => 'Listado de facturas',
            'data' => $this->facturaService->obtenerTodas($empresaId)
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $empresaId = $this->getEmpresaId($request);
        $factura = $this->facturaService->obtenerPorId($id, $empresaId);
        
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
        try {
            $empresaId = $this->getEmpresaId($request);

            $validated = $request->validate([
                'client.name' => 'required|string|max:255',
                'client.document' => 'required|string|max:20',
                'items' => 'required|array|min:1',
                'items.*.description' => 'required|string|max:255',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
            ]);

            $items = array_map(function ($item) {
                return [
                    'descripcion' => $item['description'],
                    'cantidad' => $item['quantity'],
                    'precio_unitario' => $item['price'],
                ];
            }, $validated['items']);

            $dto = new CrearFacturaDTO(
                serie: 'F001',
                cliente_id: 1, // TODO: obtener del cliente autenticado
                items: $items,
                fecha: date('Y-m-d'),
                empresa_id: $empresaId
            );

            $factura = $this->facturaService->crear($dto);

            return response()->json([
                'status' => 'success',
                'message' => 'Factura creada correctamente',
                'data' => $factura
            ], 201);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $empresaId = $this->getEmpresaId($request);

            $validated = $request->validate([
                'estado' => 'required|in:pendiente,pagada,anulada',
            ]);

            $factura = $this->facturaService->actualizar($id, $empresaId, $validated);

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

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $empresaId = $this->getEmpresaId($request);
            $factura = $this->facturaService->anular($id, $empresaId);

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

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
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

    public function index(Request $request): JsonResponse
    {
        $empresa  = $request->attributes->get('empresa');
        $facturas = $this->facturaService->obtenerTodas($empresa->id);

        return response()->json([
            'status' => 'success',
            'data'   => $facturas,
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $empresa = $request->attributes->get('empresa');
        $factura = $this->facturaService->obtenerPorId($id, $empresa->id);

        if (!$factura) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Factura no encontrada',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $factura,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $empresa = $request->attributes->get('empresa');

            $validated = $request->validate([
                'serie'                   => 'required|string|max:10',
                'tipo'                    => 'required|in:factura,boleta',
                'cliente_nombre'          => 'required|string|max:255',
                'cliente_documento'       => 'required|string|max:20',
                'cliente_email'           => 'nullable|email|max:255',
                'fecha'                   => 'required|date_format:Y-m-d|before_or_equal:today',
                'items'                   => 'required|array|min:1',
                'items.*.descripcion'     => 'required|string|max:255',
                'items.*.cantidad'        => 'required|integer|min:1',
                'items.*.precio_unitario' => 'required|numeric|min:0',
            ]);

            $dto = new CrearFacturaDTO(
                serie:             $validated['serie'],
                tipo:              $validated['tipo'],
                cliente_nombre:    $validated['cliente_nombre'],
                cliente_documento: $validated['cliente_documento'],
                items:             $validated['items'],
                fecha:             $validated['fecha'],
                empresa_id:        $empresa->id,
                cliente_email:     $validated['cliente_email'] ?? null,
            );

            $factura = $this->facturaService->crear($dto);

            return response()->json([
                'status'  => 'success',
                'message' => 'Factura creada correctamente',
                'data'    => $factura,
            ], 201);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $empresa = $request->attributes->get('empresa');

            $validated = $request->validate([
                'estado' => 'required|in:pendiente,pagada,anulada',
            ]);

            $factura = $this->facturaService->actualizar($id, $empresa->id, $validated);

            if (!$factura) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Factura no encontrada',
                ], 404);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Factura actualizada',
                'data'    => $factura,
            ]);

        } catch (\LogicException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $empresa = $request->attributes->get('empresa');
            $factura = $this->facturaService->anular($id, $empresa->id);

            if (!$factura) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Factura no encontrada o ya anulada',
                ], 404);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Factura anulada',
                'data'    => $factura,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function descargarFactura(Request $request, int $id)
    {
        try {
            $empresa = $request->attributes->get('empresa');

            $factura = Factura::where('id', $id)
                ->where('empresa_id', $empresa->id)
                ->with(['items', 'empresa'])
                ->first();

            if (!$factura) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Factura no encontrada',
                ], 404);
            }

            $pdf = Pdf::loadView('facturas.pdf', ['factura' => $factura]);

            return $pdf->download("Factura-{$factura->serie}-{$factura->numero}.pdf");

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function previewFactura(Request $request, int $id)
    {
        try {
            $empresa = $request->attributes->get('empresa');

            $factura = Factura::where('id', $id)
                ->where('empresa_id', $empresa->id)
                ->with(['items', 'empresa'])
                ->first();

            if (!$factura) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Factura no encontrada',
                ], 404);
            }

            $pdf = Pdf::loadView('facturas.pdf', ['factura' => $factura]);

            return $pdf->stream("Factura-{$factura->serie}-{$factura->numero}.pdf");

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

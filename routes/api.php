<?php

use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PdfController;
use Illuminate\Support\Facades\Route;

// ─── Público ─────────────────────────────────────────────────────────────────
// El POS llama a esto una sola vez durante el setup inicial
Route::post('/empresas/registro', [EmpresaController::class, 'registro']);

// ─── Protegido por API Token de empresa ──────────────────────────────────────
// El POS envía: Authorization: Bearer {api_token}
Route::middleware('api.token')->group(function () {

    // Info y configuración de la empresa
    Route::get('/empresas/info',          [EmpresaController::class, 'info']);
    Route::post('/empresas/certificado',  [EmpresaController::class, 'subirCertificado']);
    Route::post('/empresas/tokens',       [EmpresaController::class, 'generarToken']);

    // Facturas (el POS manda los datos del cliente + items al momento de cobrar)
    Route::prefix('invoices')->group(function () {
        Route::get('/',       [InvoiceController::class, 'index']);
        Route::post('/',      [InvoiceController::class, 'store']);
        Route::get('{id}',    [InvoiceController::class, 'show']);
        Route::put('{id}',    [InvoiceController::class, 'update']);
        Route::delete('{id}', [InvoiceController::class, 'destroy']);
    });

    // PDF de facturas
    Route::get('/invoices/{id}/pdf',         [PdfController::class, 'descargarFactura']);
    Route::get('/invoices/{id}/pdf/preview', [PdfController::class, 'previewFactura']);
});

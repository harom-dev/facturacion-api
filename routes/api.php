<?php

use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('invoices')->group(function () {
    Route::get('/', [InvoiceController::class, 'index']);
    Route::post('/', [InvoiceController::class, 'store']);
    Route::get('{id}', [InvoiceController::class, 'show']);
    Route::put('{id}', [InvoiceController::class, 'update']);
    Route::delete('{id}', [InvoiceController::class, 'destroy']);
});
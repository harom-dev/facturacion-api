<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InvoiceController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/invoices', [InvoiceController::class, 'index'])->withoutMiddleware('web');
Route::post('/api/invoices', [InvoiceController::class, 'store'])->withoutMiddleware('web');
Route::get('/api/invoices/{id}', [InvoiceController::class, 'show'])->withoutMiddleware('web');
Route::put('/api/invoices/{id}', [InvoiceController::class, 'update'])->withoutMiddleware('web');
Route::delete('/api/invoices/{id}', [InvoiceController::class, 'destroy'])->withoutMiddleware('web');
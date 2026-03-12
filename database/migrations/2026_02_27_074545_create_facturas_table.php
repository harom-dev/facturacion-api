<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('cliente_nombre');
            $table->string('cliente_documento');
            $table->string('cliente_email')->nullable();
            $table->string('serie')->default('F001');
            $table->string('numero')->unique();
            $table->date('fecha');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('impuesto', 12, 2);
            $table->decimal('total', 12, 2);
            $table->enum('estado', ['pendiente', 'pagada', 'anulada'])->default('pendiente');
            $table->string('tipo')->default('factura'); // factura o boleta
            $table->string('estado_sunat')->default('pendiente'); // pendiente, enviado, aceptado, rechazado
            $table->json('respuesta_sunat')->nullable();
            $table->string('certificado_usado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};

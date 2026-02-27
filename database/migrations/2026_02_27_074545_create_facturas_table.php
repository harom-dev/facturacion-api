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
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('serie')->default('F001');
            $table->string('numero')->unique();
            $table->date('fecha');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('impuesto', 12, 2);
            $table->decimal('total', 12, 2);
            $table->enum('estado', ['pendiente', 'pagada', 'anulada'])->default('pendiente');
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

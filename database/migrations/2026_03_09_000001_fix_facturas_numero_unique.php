<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            // Quitar el unique simple en numero
            $table->dropUnique('facturas_numero_unique');

            // Agregar unique compuesto: misma empresa + misma serie + mismo numero = duplicado
            $table->unique(['empresa_id', 'serie', 'numero'], 'facturas_empresa_serie_numero_unique');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropUnique('facturas_empresa_serie_numero_unique');
            $table->unique('numero', 'facturas_numero_unique');
        });
    }
};

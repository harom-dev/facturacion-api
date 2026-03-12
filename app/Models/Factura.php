<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    protected $fillable = [
        'empresa_id', 
        'cliente_nombre', 
        'cliente_documento', 
        'cliente_email',
        'serie', 
        'numero', 
        'fecha', 
        'subtotal', 
        'impuesto', 
        'total', 
        'tipo',
        'estado',
        'estado_sunat',
        'respuesta_sunat',
        'certificado_usado'
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'float',
        'impuesto' => 'float',
        'total' => 'float',
        'respuesta_sunat' => 'array',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemFactura::class);
    }
}
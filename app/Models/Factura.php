<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    protected $fillable = ['empresa_id', 'cliente_id', 'serie', 'numero', 'fecha', 'subtotal', 'impuesto', 'total', 'estado'];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'float',
        'impuesto' => 'float',
        'total' => 'float',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemFactura::class, 'factura_id');
    }
}
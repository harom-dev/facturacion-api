<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemFactura extends Model
{
    protected $table = 'item_facturas';
    protected $fillable = ['factura_id', 'descripcion', 'cantidad', 'precio_unitario', 'subtotal'];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'float',
        'subtotal' => 'float',
    ];

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }
}
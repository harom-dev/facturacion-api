<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $fillable = ['empresa_id', 'nombre', 'documento', 'email', 'telefono', 'direccion'];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }
}
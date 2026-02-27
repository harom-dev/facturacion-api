<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    protected $fillable = ['nombre', 'ruc', 'direccion', 'telefono', 'email', 'activa'];

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }
}
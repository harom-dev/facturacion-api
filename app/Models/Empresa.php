<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    protected $fillable = [
        'nombre',
        'ruc',
        'direccion',
        'telefono',
        'email',
        'certificado_pfx',
        'certificado_password',
        'activa',
    ];

    // El certificado nunca se expone en responses JSON
    protected $hidden = ['certificado_pfx', 'certificado_password'];

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }

    public function apiTokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }

    public function tieneCertificado(): bool
    {
        return !empty($this->certificado_pfx);
    }
}

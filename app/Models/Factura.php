<?php

namespace App\Models;

class Factura
{
    public $id;
    public $serie;
    public $numero;
    public $fecha;
    public $cliente;
    public $items;
    public $subtotal;
    public $impuesto;
    public $total;
    public $estado;

    public function __construct($id, $serie, $numero, $fecha, $cliente, $items, $subtotal, $impuesto, $total, $estado)
    {
        $this->id = $id;
        $this->serie = $serie;
        $this->numero = $numero;
        $this->fecha = $fecha;
        $this->cliente = $cliente;
        $this->items = $items;
        $this->subtotal = $subtotal;
        $this->impuesto = $impuesto;
        $this->total = $total;
        $this->estado = $estado;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'serie' => $this->serie,
            'numero' => $this->numero,
            'fecha' => $this->fecha,
            'cliente' => $this->cliente,
            'items' => $this->items,
            'subtotal' => $this->subtotal,
            'impuesto' => $this->impuesto,
            'total' => $this->total,
            'estado' => $this->estado,
        ];
    }
}
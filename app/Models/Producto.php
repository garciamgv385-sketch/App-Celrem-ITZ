<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'nombre',
    'categoria',
    'marca',
    'sku',
    'unidad',
    'existencia',
    'stock_minimo',
    'precio_compra',
    'precio_venta',
    'proveedor',
    'ubicacion',
    'estado',
    'observaciones',
])]
class Producto extends Model
{
    protected function casts(): array
    {
        return [
            'existencia' => 'integer',
            'stock_minimo' => 'integer',
            'precio_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
        ];
    }

    public function estadoInventario(): string
    {
        if ($this->estado === 'inactivo') {
            return 'Inactivo';
        }

        if ($this->existencia <= 0) {
            return 'Sin existencia';
        }

        if ($this->existencia <= $this->stock_minimo) {
            return 'Stock bajo';
        }

        return 'Disponible';
    }

    public function claseEstadoInventario(): string
    {
        return match ($this->estadoInventario()) {
            'Disponible' => 'bg-success',
            'Stock bajo' => 'bg-warning text-dark',
            'Sin existencia' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function valorInventario(): float
    {
        return $this->existencia * (float) $this->precio_venta;
    }
}

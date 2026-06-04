<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nombre',
    'rfc',
    'telefono',
    'correo',
    'direccion',
    'contacto',
    'estado',
    'observaciones',
])]
class Proveedor extends Model
{
    protected $table = 'proveedores';

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }
}

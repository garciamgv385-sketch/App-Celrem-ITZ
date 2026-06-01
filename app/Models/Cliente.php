<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'telefono', 'correo', 'direccion', 'estado', 'observaciones'])]
class Cliente extends Model
{
    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class);
    }
}

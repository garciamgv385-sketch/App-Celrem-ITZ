<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'cliente_id',
    'tipo',
    'marca',
    'modelo',
    'anio',
    'placas',
    'kilometraje_actual',
    'tipo_combustible',
    'estado',
    'observaciones',
])]
class Vehiculo extends Model
{
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}

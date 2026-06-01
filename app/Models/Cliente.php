<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nombre', 'telefono', 'correo', 'direccion', 'estado', 'observaciones'])]
class Cliente extends Model
{
}

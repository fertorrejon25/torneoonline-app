<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{
    protected $table = 'temporadas'; // nombre de la tabla en tu base de datos

    protected $fillable = ['NombreTemporada']; // columnas que se pueden cargar masivamente
}

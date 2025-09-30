<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{
    protected $table = 'temporadas'; // nombre de la tabla en tu base de datos

    protected $fillable = ['NombreTemporada']; // columnas que se pueden cargar masivamente
//****para relacionar fecha temporada */
public function fechas()
{
    return $this->hasMany(Fecha::class);
}
}

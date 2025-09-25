<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $table = 'equipos';   // <- asegúrate que apunte a la tabla correcta

    protected $fillable = [
        'NombreEquipos',
        'FotoEquipo',
    ];
}

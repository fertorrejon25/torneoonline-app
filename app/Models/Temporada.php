<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{
    protected $table = 'temporadas';
    protected $fillable = ['NombreTemporada'];

    // Relación con Fecha
    public function fechas()
    {
        return $this->hasMany(Fecha::class);
    }

    // Relación muchos a muchos con Equipo
    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'temporada_equipos', 'temporada_id', 'equipo_id')
                    ->withTimestamps();
    }
}
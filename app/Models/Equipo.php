<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $table = 'equipos';
    protected $fillable = [
        'NombreEquipos',
        'FotoEquipo',
        'partidos_totales',
        'goles_totales_favor',
        'goles_totales_contra',
        'partidos_ganados',
        'partidos_empatados',
        'partidos_perdidos'
    ];

    // Relación con Jugadores
    public function jugadores()
    {
        return $this->hasMany(Jugador::class, 'equipo_id');
    }

    // Relación muchos a muchos con Temporadas
    public function temporadas()
    {
        return $this->belongsToMany(Temporada::class, 'temporada_equipos', 'equipo_id', 'temporada_id')
                    ->withTimestamps();
    }

    // Relación con Partidos como equipo local
    public function partidosLocal()
    {
        return $this->hasMany(Partido::class, 'equipo_local_id');
    }

    // Relación con Partidos como equipo visitante
    public function partidosVisitante()
    {
        return $this->hasMany(Partido::class, 'equipo_visitante_id');
    }


}
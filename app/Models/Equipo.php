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
        'partidos_perdidos',
    ];

    /** Jugadores que pertenecen al equipo */
    public function jugadores()
    {
        return $this->hasMany(Jugador::class, 'equipo_id');
    }

    /** Relación muchos a muchos con temporadas, usando la tabla pivot de estadísticas */
    public function temporadas()
    {
        return $this->belongsToMany(Temporada::class, 'equipo_estadisticas_temporada', 'equipo_id', 'temporada_id')
                    ->withPivot([
                        'partidos_jugados',
                        'partidos_ganados',
                        'partidos_empatados',
                        'partidos_perdidos',
                        'goles_favor',
                        'goles_contra',
                        'diferencia_goles',
                        'puntos'
                    ])
                    ->withTimestamps();
    }

    /** Partidos donde el equipo es local */
    public function partidosLocal()
    {
        return $this->hasMany(Partido::class, 'equipo_local_id');
    }

    /** Partidos donde el equipo es visitante */
    public function partidosVisitante()
    {
        return $this->hasMany(Partido::class, 'equipo_visitante_id');
    }

    /** Estadísticas por temporada */
    public function estadisticasTemporada()
    {
        return $this->hasMany(EquipoEstadisticasTemporada::class, 'equipo_id');
    }

    /** Obtener estadísticas de una temporada específica */
    public function estadisticasEnTemporada($temporadaId)
    {
        return $this->estadisticasTemporada()
                    ->where('temporada_id', $temporadaId)
                    ->first();
    }
}

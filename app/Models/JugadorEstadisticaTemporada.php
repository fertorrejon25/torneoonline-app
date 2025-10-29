<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JugadorEstadisticaTemporada extends Model
{
    use HasFactory;

    protected $table = 'jugador_estadisticas_temporada';

    protected $fillable = [
        'jugador_id',
        'temporada_id',
        'partidos_jugados',
        'goles',
        'asistencias',
    ];

    /**
     * Relación con el jugador
     */
    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }

    /**
     * Relación con la temporada
     */
    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }

    /**
     * Scope para estadísticas de una temporada específica
     */
    public function scopeDeTemporada($query, $temporadaId)
    {
        return $query->where('temporada_id', $temporadaId);
    }

    /**
     * Scope para estadísticas de un jugador específico
     */
    public function scopeDeJugador($query, $jugadorId)
    {
        return $query->where('jugador_id', $jugadorId);
    }

    /**
     * Accesor para el promedio de goles por partido
     */
    public function getPromedioGolesAttribute()
    {
        if ($this->partidos_jugados == 0) {
            return 0;
        }
        return round($this->goles / $this->partidos_jugados, 2);
    }

    /**
     * Accesor para el promedio de asistencias por partido
     */
    public function getPromedioAsistenciasAttribute()
    {
        if ($this->partidos_jugados == 0) {
            return 0;
        }
        return round($this->asistencias / $this->partidos_jugados, 2);
    }
}
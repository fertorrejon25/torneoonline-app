<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JugadorPartidoStat extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos
     */
    protected $table = 'jugador_partido_stats';

    /**
     * Campos que pueden asignarse masivamente
     */
    protected $fillable = [
        'partido_id',
        'jugador_id',
        'temporada_id',
        'jugo',
        'goles',
        'asistencias'
    ];

    /**
     * Tipos de datos casteados automáticamente
     */
    protected $casts = [
        'jugo' => 'boolean',
        'goles' => 'integer',
        'asistencias' => 'integer',
    ];

    /* ===========================================================
     |  RELACIONES
     =========================================================== */

    /**
     * Relación con el jugador
     */
    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }

    /**
     * Relación con el partido
     */
    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }

    /**
     * Relación con la temporada
     */
    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }

    /* ===========================================================
     |  MÉTODOS ÚTILES
     =========================================================== */

    /**
     * Devuelve si el jugador tuvo participación (jugó o marcó gol/asistencia)
     */
    public function getTuvoParticipacionAttribute()
    {
        return $this->jugo || $this->goles > 0 || $this->asistencias > 0;
    }

    /**
     * Devuelve un texto con el resumen de su actuación
     */
    public function getResumenAttribute()
    {
        $partes = [];
        if ($this->goles > 0) {
            $partes[] = "{$this->goles} gol" . ($this->goles > 1 ? 'es' : '');
        }
        if ($this->asistencias > 0) {
            $partes[] = "{$this->asistencias} asistencia" . ($this->asistencias > 1 ? 's' : '');
        }

        return empty($partes)
            ? ($this->jugo ? 'Jugó sin goles ni asistencias' : 'No jugó')
            : implode(' y ', $partes);
    }
}

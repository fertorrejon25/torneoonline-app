<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    use HasFactory;

    protected $table = 'partidos';

    protected $fillable = [
        'temporada_id',
        'fecha_id',
        'equipo_local_id',
        'equipo_visitante_id',
        'goles_local',
        'goles_visitante',
        'fecha',
        'hora',
    ];

    /**
     * Relación con las estadísticas de jugadores en el partido.
     */
    public function estadisticas()
    {
        return $this->hasMany(PartidoEstadistica::class, 'partido_id');
    }

    /**
     * Relación con la fecha.
     */
    public function fecha()
    {
        return $this->belongsTo(Fecha::class, 'fecha_id');
    }

    /**
     * Relación con la temporada.
     */
    public function temporada()
    {
        return $this->belongsTo(Temporada::class, 'temporada_id');
    }

    /**
     * Relación con el equipo local.
     */
    public function equipoLocal()
    {
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }

    /**
     * Relación con el equipo visitante.
     */
    public function equipoVisitante()
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }

    /**
     * Accesor para el nombre del equipo local.
     */
    public function getNombreLocalAttribute()
    {
        return $this->equipoLocal ? $this->equipoLocal->NombreEquipos : 'Equipo Local';
    }

    /**
     * Accesor para el nombre del equipo visitante.
     */
    public function getNombreVisitanteAttribute()
    {
        return $this->equipoVisitante ? $this->equipoVisitante->NombreEquipos : 'Equipo Visitante';
    }

    /**
     * Accesor para el resultado formateado.
     */
    public function getResultadoAttribute()
    {
        if ($this->goles_local === null || $this->goles_visitante === null) {
            return 'Pendiente';
        }

        return "{$this->goles_local} - {$this->goles_visitante}";
    }

    /**
     * Determina si el partido tiene resultado cargado.
     */
    public function getTieneResultadoAttribute()
    {
        return $this->goles_local !== null && $this->goles_visitante !== null;
    }

    /**
     * Determina el ganador del partido.
     */
    public function getGanadorAttribute()
    {
        if (!$this->tiene_resultado) {
            return null;
        }

        if ($this->goles_local > $this->goles_visitante) {
            return $this->equipo_local_id;
        } elseif ($this->goles_visitante > $this->goles_local) {
            return $this->equipo_visitante_id;
        }

        return 'empate';
    }

    /**
     * Scope: partidos con resultado.
     */
    public function scopeConResultado($query)
    {
        return $query->whereNotNull('goles_local')
                     ->whereNotNull('goles_visitante');
    }

    /**
     * Scope: partidos pendientes.
     */
    public function scopePendientes($query)
    {
        return $query->whereNull('goles_local')
                     ->orWhereNull('goles_visitante');
    }

    /**
     * Scope: partidos de una temporada.
     */
    public function scopeDeTemporada($query, $temporadaId)
    {
        return $query->where('temporada_id', $temporadaId);
    }

    /**
     * Scope: partidos de una fecha.
     */
    public function scopeDeFecha($query, $fechaId)
    {
        return $query->where('fecha_id', $fechaId);
    }

    /**
     * Scope: partidos de un equipo (local o visitante).
     */
    public function scopeDelEquipo($query, $equipoId)
    {
        return $query->where('equipo_local_id', $equipoId)
                     ->orWhere('equipo_visitante_id', $equipoId);
    }

    /**
     * Método seguro para obtener el ID de temporada desde la fecha.
     * (Evita errores si la relación 'fecha' es null)
     */
    public function getTemporadaDesdeFechaAttribute()
    {
        return optional($this->fecha)->temporada_id;
    }
}

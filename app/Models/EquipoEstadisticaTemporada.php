<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipoEstadisticaTemporada extends Model
{
    use HasFactory;

    protected $table = 'equipo_estadisticas_temporada';

    protected $fillable = [
        'equipo_id',
        'temporada_id',
        'partidos_jugados',
        'partidos_ganados',
        'partidos_empatados',
        'partidos_perdidos',
        'goles_favor',
        'goles_contra',
        'diferencia_goles',
        'puntos',
    ];

    /**
     * Relación con el equipo
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    /**
     * Relación con la temporada
     */
    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }
}
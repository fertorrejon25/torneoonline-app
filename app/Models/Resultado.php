<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    protected $table = 'resultados';

    protected $fillable = [
        'equipo_id',
        'temporada_id',
        'partidos_jugados',
        'partidos_ganados',
        'partidos_empatados',
        'partidos_perdidos',
        'goles_favor',      // ⬅️ Agregar
        'goles_contra',     // ⬅️ Agregar
    ];

    protected $casts = [
        'partidos_jugados' => 'integer',
        'partidos_ganados' => 'integer',
        'partidos_empatados' => 'integer',
        'partidos_perdidos' => 'integer',
        'goles_favor' => 'integer',
        'goles_contra' => 'integer',
    ];

    // Relación con equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    // Relación con temporada
    public function temporada()
    {
        return $this->belongsTo(Temporada::class, 'temporada_id');
    }

    // Accessor para calcular puntos
    public function getPuntosAttribute()
    {
        return ($this->partidos_ganados * 3) + $this->partidos_empatados;
    }

    // Accessor para diferencia de goles
    public function getDiferenciaGolesAttribute()
    {
        return $this->goles_favor - $this->goles_contra;
    }

    // Accessor para porcentaje de efectividad
    public function getEfectividadAttribute()
    {
        if ($this->partidos_jugados === 0) {
            return 0;
        }
        return round(($this->puntos / ($this->partidos_jugados * 3)) * 100, 2);
    }
}
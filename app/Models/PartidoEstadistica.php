<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidoEstadistica extends Model
{
    use HasFactory;

    protected $table = 'partido_estadisticas';

    protected $fillable = [
        'partido_id',
        'jugador_id',
        'jugo', // Campo booleano: si jugó en este partido específico
        'goles',
        'asistencias',
    ];

    protected $casts = [
        'jugo' => 'boolean',
        'goles' => 'integer',
        'asistencias' => 'integer',
    ];

    protected $attributes = [
        'jugo' => false,
        'goles' => 0,
        'asistencias' => 0,
    ];

    /**
     * Relación con el partido
     */
    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }

    /**
     * Relación con el jugador
     */
    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }
}
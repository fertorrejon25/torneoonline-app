<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jugador extends Model
{
    use HasFactory;

    protected $table = 'jugadores';

    protected $fillable = [
        'equipo_id',
        'user_jugadores',
        'partidos_jugados',
        'goles',
        'asistencias',
        'foto_jugador',
    ];

    // Relación con Equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    // Relación con User (para obtener el nombre del jugador)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_jugadores');
    }

    // Relación con estadísticas de temporada
    public function estadisticasTemporadas()
    {
        return $this->hasMany(JugadorEstadisticasTemporada::class, 'jugador_id');
    }
}
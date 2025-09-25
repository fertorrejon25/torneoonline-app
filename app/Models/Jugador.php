<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jugador extends Model
{
    protected $table = 'jugadores';

    protected $fillable = [
        'equipo_id',
        'user_jugadores',   // <- guarda users.id
        'partidos_jugados',
        'goles',
        'asistencias',
        'foto_jugador',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_jugadores');
    }
}

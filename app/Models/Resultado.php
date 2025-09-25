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
        "partidos_ganados",
        'partidos_empatados',
        'partidos_perdidos',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }
    public function temporada()
    {
        return $this->belongsTo(Temporada::class, 'temporada_id');
    }

}


 

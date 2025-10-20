<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
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


    public function fecha()
    {
        return $this->belongsTo(Fecha::class, 'fecha_id');
    }


    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }

    public function local()
    {
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }

    public function visitante()
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }
    /**para los partidos visitante y local */
    public function equipoLocal()
    {
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }

    public function equipoVisitante()
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }
    /************************************* */
}


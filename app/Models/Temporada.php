<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{
    use HasFactory;

    protected $table = 'temporadas';

    protected $fillable = [
        'NombreTemporada',
    ];

    /**
     * Relación: una temporada tiene muchas fechas.
     */
    public function fechas()
    {
        return $this->hasMany(Fecha::class, 'temporada_id');
    }

    /**
     * Relación: una temporada tiene muchos partidos.
     */
    public function partidos()
    {
        return $this->hasMany(Partido::class, 'temporada_id');
    }

    /**
     * Relación: una temporada tiene muchos equipos (a través de temporada_equipos).
     */
    public function equipos()
    {
        return $this->belongsToMany(
            Equipo::class,
            'temporada_equipos',
            'temporada_id',
            'equipo_id'
        )->withTimestamps();
    }

    /**
     * Accesor para mostrar el nombre formateado.
     */
    public function getNombreFormateadoAttribute()
    {
        return ucfirst($this->NombreTemporada);
    }

    /**
     * Scope para buscar temporadas activas o recientes.
     */
    public function scopeRecientes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}

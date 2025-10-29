<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fecha extends Model
{
    use HasFactory;

    protected $table = 'fechas';

    protected $fillable = [
        'temporada_id',
        'nombre',
        'dia',
    ];

    /**
     * Relación: una fecha pertenece a una temporada.
     */
    public function temporada()
    {
        return $this->belongsTo(Temporada::class, 'temporada_id');
    }

    /**
     * Relación: una fecha puede tener muchos partidos.
     */
    public function partidos()
    {
        return $this->hasMany(Partido::class, 'fecha_id');
    }

    /**
     * Accesor para obtener el nombre formateado de la fecha.
     * Ejemplo: "Fecha 1 - 2025-01-01"
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre}" . ($this->dia ? " - {$this->dia}" : '');
    }

    /**
     * Scope para buscar por temporada.
     */
    public function scopeDeTemporada($query, $temporadaId)
    {
        return $query->where('temporada_id', $temporadaId);
    }

    /**
     * Scope para ordenar por día.
     */
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('dia', 'asc');
    }
}

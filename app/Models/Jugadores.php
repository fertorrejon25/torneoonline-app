<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jugadores extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'apellido',
        'nombre',
        'dni',
        'mail',
        'equipos_id',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipos_id');
    }
}

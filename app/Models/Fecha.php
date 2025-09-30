<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Fecha extends Model
{
    protected $fillable = ['temporada_id','nombre','dia'];

    public function partidos()
    {
        return $this->hasMany(Partido::class);
    }
}
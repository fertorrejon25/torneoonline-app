<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipo;

class EquipoController extends Controller
{
    public function index()
    {
        // Devuelve nombre e imagen de cada equipo
        $equipos = Equipo::select('NombreEquipos', 'FotoEquipo')->get();

        return response()->json($equipos);
    }
}

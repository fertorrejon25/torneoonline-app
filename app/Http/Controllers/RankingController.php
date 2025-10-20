<?php

namespace App\Http\Controllers;
use App\Models\Temporada;
use App\Models\Equipo;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index()
    {
        // Obtener equipos ORDENADOS
        $equipos = Equipo::orderBy('NombreEquipos')->get();

        // Obtener la temporada más reciente
        $temporada = Temporada::orderBy('created_at', 'desc')->first();

        // Pasar a la vista de forma EXPLÍCITA
        return view('admin.ranking', [
            'equipos' => $equipos,
            'temporada' => $temporada  // ⬅️ Cambiado de temporada_activa a temporada
        ]);
    }
}
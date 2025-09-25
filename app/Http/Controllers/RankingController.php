<?php

namespace App\Http\Controllers;
use App\Models\Temporada;
use App\Models\Equipo;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index()
    {
        // Obtener equipos ORDENADOS - esto es importante
        $equipos = Equipo::orderBy('NombreEquipos')->get();

        // Obtener la temporada activa
        $temporada_activa = Temporada::where('estado', 'activa')->first();

        // Pasar a la vista de forma EXPLÍCITA
        return view('admin.ranking', [
            'equipos' => $equipos,
            'temporada_activa' => $temporada_activa
        ]);
    }
}
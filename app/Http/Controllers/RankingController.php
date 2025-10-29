<?php

namespace App\Http\Controllers;

use App\Models\Temporada;
use App\Models\Equipo;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index()
    {
        // Obtener todas las temporadas con sus equipos y estadísticas
        $temporadas = Temporada::with(['equipos.estadisticasTemporada' => function($query) {
            $query->orderBy('puntos', 'DESC')
                  ->orderBy('diferencia_goles', 'DESC');
        }])->get();

        return view('admin.ranking', compact('temporadas'));
    }

    public function show($temporadaId)
    {
        // Mostrar ranking de una temporada específica
        $temporada = Temporada::with(['equipos.estadisticasTemporada' => function($query) use ($temporadaId) {
            $query->where('temporada_id', $temporadaId)
                  ->orderBy('puntos', 'DESC')
                  ->orderBy('diferencia_goles', 'DESC');
        }])->findOrFail($temporadaId);

        return view('admin.ranking.show', compact('temporada'));
    }
}
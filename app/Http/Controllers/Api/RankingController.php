<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipoEstadisticaTemporada;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index()
    {
        $ranking = EquipoEstadisticaTemporada::with('equipo')
            ->orderByDesc('puntos')
            ->orderByDesc('diferencia_goles')
            ->get()
            ->map(function ($item, $index) {
                return [
                    '#' => $index + 1,
                    'Equipo' => $item->equipo->NombreEquipos ?? 'Sin nombre',
                    'PJ' => $item->partidos_jugados,
                    'PG' => $item->partidos_ganados,
                    'PE' => $item->partidos_empatados,
                    'PP' => $item->partidos_perdidos,
                    'GF' => $item->goles_favor,
                    'GC' => $item->goles_contra,
                    'DG' => $item->diferencia_goles,
                    'Pts' => $item->puntos,
                ];
            });

        return response()->json($ranking);
    }
}

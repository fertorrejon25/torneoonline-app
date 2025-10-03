<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Temporada;
use Illuminate\Http\Request;

class PartidosController extends Controller
{
    public function index($temporadaId)
    {
        $temporada = Temporada::findOrFail($temporadaId);
        $partidos = Partido::where('temporada_id', $temporadaId)
                            ->with(['equipoLocal','equipoVisitante','fecha'])
                            ->get();

        return view('admin.fixture.index', compact('temporada', 'partidos'));
    }

    public function generar($temporadaId)
    {
        // implementación existente o placeholder
        return back();
    }

    public function updateFechas($temporadaId)
    {
        // placeholder
        return back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_id' => 'required|exists:fechas,id',
            'equipo_local' => 'required|exists:equipos,id',
            'equipo_visitante' => 'required|exists:equipos,id|different:equipo_local',
            'goles_local' => 'nullable|integer',
            'goles_visitante' => 'nullable|integer',
            'fecha' => 'nullable|date',
            'hora' => 'nullable'
        ]);

        // map incoming field names to DB column names expected by the model
        $data = [
            'fecha_id' => $request->input('fecha_id'),
            'equipo_local_id' => $request->input('equipo_local'),
            'equipo_visitante_id' => $request->input('equipo_visitante'),
            'goles_local' => $request->input('goles_local'),
            'goles_visitante' => $request->input('goles_visitante'),
            'fecha' => $request->input('fecha'),
            'hora' => $request->input('hora'),
            'temporada_id' => $request->input('temporada_id') ?? null,
        ];

        Partido::create($data);

        return back()->with('success', 'Partido agregado correctamente.');
    }
}

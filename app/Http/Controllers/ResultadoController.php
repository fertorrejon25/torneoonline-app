<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;
use App\Models\Resultado;

class ResultadoController extends Controller
{
    public function create()
    {
        $equipos = Equipo::orderBy('NombreEquipos')->get();
        return view('admin.partidos', compact('equipos'));
    }
    
    public function store(Request $request)
    {
        // Validar y guardar los partidos
        $request->validate([
            'partidos' => 'required|array',
            'partidos.*.home_team_id' => 'required|exists:equipos,id',
            'partidos.*.away_team_id' => 'required|exists:equipos,id',
            'partidos.*.home_score' => 'required|integer|min:0',
            'partidos.*.away_score' => 'required|integer|min:0',
        ]);
        
        foreach ($request->partidos as $partido) {
            Resultado::create([
                'equipo_local_id' => $partido['home_team_id'],
                'equipo_visitante_id' => $partido['away_team_id'],
                'goles_local' => $partido['home_score'],
                'goles_visitante' => $partido['away_score'],
                'fecha' => now(),
            ]);
        }
        
        return back()->with('success', 'Partidos guardados correctamente.');
    }
    
    public function index()
    {
        $resultados = Resultado::with(['equipoLocal', 'equipoVisitante'])->get();
        return view('admin.resultados', compact('resultados'));
    }
}
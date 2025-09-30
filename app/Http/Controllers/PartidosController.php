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
                            ->with(['local','visitante'])
                            ->get()
                            ->groupBy(function($p){ return $p->created_at->format('Y-m-d'); });

        return view('temporadas.fixture', compact('temporada','partidos'));
    }

    public function generar($temporadaId)
    {
        $equipos = Equipo::pluck('id')->toArray();
        $fixture = $this->roundRobin($equipos);

        foreach ($fixture as $ronda) {
            foreach ($ronda as [$local, $visitante]) {
                Partido::create([
                    'temporada_id' => $temporadaId,
                    'equipo_local_id' => $local,
                    'equipo_visitante_id' => $visitante,
                ]);
            }
        }

        return redirect()->route('fixture.index', $temporadaId)
                         ->with('success','Fixture generado correctamente');
    }

    public function updateFechas(Request $request, $temporadaId)
    {
        foreach ($request->fechas as $id => $fecha) {
            Partido::where('id',$id)->update([
                'fecha' => $fecha,
                'hora' => $request->horas[$id] ?? null
            ]);
        }
        return back()->with('success','Fechas y horarios actualizados');
    }

    private function roundRobin($equipos)
    {
        $count = count($equipos);
        if ($count % 2) { $equipos[] = null; $count++; }
        $rondas = $count - 1;
        $partidosPorRonda = $count / 2;
        $fixture = [];

        for ($ronda = 0; $ronda < $rondas; $ronda++) {
            for ($i = 0; $i < $partidosPorRonda; $i++) {
                $local = $equipos[$i];
                $visitante = $equipos[$count - 1 - $i];
                if ($local && $visitante) {
                    $fixture[$ronda][] = [$local, $visitante];
                }
            }
            $primer = array_splice($equipos, 1, 1)[0];
            array_push($equipos, $primer);
        }

        return $fixture;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Temporada;
use App\Models\Resultado;
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
            'goles_local' => 'nullable|integer|min:0',
            'goles_visitante' => 'nullable|integer|min:0',
            'fecha' => 'nullable|date',
            'hora' => 'nullable|date_format:H:i'
        ]);

        // Mapear campos del formulario a nombres de columnas
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

        $partido = Partido::create($data);

        // Si el partido tiene resultado, actualizar estadísticas
        if ($partido->goles_local !== null && $partido->goles_visitante !== null) {
            $this->actualizarEstadisticas($partido);
        }

        return back()->with('success', 'Partido agregado correctamente.');
    }

    /**
     * Actualizar el resultado de un partido existente
     */
    public function updateResultado(Request $request, $id)
    {
        $request->validate([
            'goles_local' => 'required|integer|min:0',
            'goles_visitante' => 'required|integer|min:0',
        ]);

        $partido = Partido::findOrFail($id);
        
        // Guardar valores anteriores para revertir estadísticas si es necesario
        $golesLocalAnterior = $partido->goles_local;
        $golesVisitanteAnterior = $partido->goles_visitante;

        // Actualizar el partido
        $partido->goles_local = $request->goles_local;
        $partido->goles_visitante = $request->goles_visitante;
        $partido->save();

        // Si había resultado anterior, revertir estadísticas
        if ($golesLocalAnterior !== null && $golesVisitanteAnterior !== null) {
            $this->revertirEstadisticas($partido, $golesLocalAnterior, $golesVisitanteAnterior);
        }

        // Aplicar nuevas estadísticas
        $this->actualizarEstadisticas($partido);

        return back()->with('success', 'Resultado actualizado correctamente.');
    }

    /**
     * Actualizar las estadísticas cuando se guarda un resultado
     */
    private function actualizarEstadisticas(Partido $partido)
    {
        // Actualizar equipo local
        $this->actualizarEstadisticasEquipo(
            $partido->equipo_local_id,
            $partido->temporada_id,
            $partido->goles_local,
            $partido->goles_visitante
        );

        // Actualizar equipo visitante
        $this->actualizarEstadisticasEquipo(
            $partido->equipo_visitante_id,
            $partido->temporada_id,
            $partido->goles_visitante,
            $partido->goles_local
        );
    }

    /**
     * Revertir estadísticas de un resultado anterior
     */
    private function revertirEstadisticas(Partido $partido, $golesLocalAnt, $golesVisitanteAnt)
    {
        // Revertir equipo local
        $this->revertirEstadisticasEquipo(
            $partido->equipo_local_id,
            $partido->temporada_id,
            $golesLocalAnt,
            $golesVisitanteAnt
        );

        // Revertir equipo visitante
        $this->revertirEstadisticasEquipo(
            $partido->equipo_visitante_id,
            $partido->temporada_id,
            $golesVisitanteAnt,
            $golesLocalAnt
        );
    }

    /**
     * Actualizar estadísticas de un equipo
     */
    private function actualizarEstadisticasEquipo($equipoId, $temporadaId, $golesFavor, $golesContra)
    {
        $resultado = Resultado::firstOrCreate(
            [
                'equipo_id' => $equipoId,
                'temporada_id' => $temporadaId,
            ],
            [
                'partidos_jugados' => 0,
                'partidos_ganados' => 0,
                'partidos_empatados' => 0,
                'partidos_perdidos' => 0,
                'goles_favor' => 0,
                'goles_contra' => 0,
            ]
        );

        // Incrementar contadores
        $resultado->partidos_jugados += 1;
        $resultado->goles_favor += $golesFavor;
        $resultado->goles_contra += $golesContra;

        // Determinar resultado del partido
        if ($golesFavor > $golesContra) {
            $resultado->partidos_ganados += 1;
        } elseif ($golesFavor === $golesContra) {
            $resultado->partidos_empatados += 1;
        } else {
            $resultado->partidos_perdidos += 1;
        }

        $resultado->save();
    }

    /**
     * Revertir estadísticas de un equipo
     */
    private function revertirEstadisticasEquipo($equipoId, $temporadaId, $golesFavor, $golesContra)
    {
        $resultado = Resultado::where([
            'equipo_id' => $equipoId,
            'temporada_id' => $temporadaId,
        ])->first();

        if (!$resultado) {
            return; // No hay estadísticas para revertir
        }

        // Decrementar contadores
        $resultado->partidos_jugados -= 1;
        $resultado->goles_favor -= $golesFavor;
        $resultado->goles_contra -= $golesContra;

        // Revertir resultado del partido
        if ($golesFavor > $golesContra) {
            $resultado->partidos_ganados -= 1;
        } elseif ($golesFavor === $golesContra) {
            $resultado->partidos_empatados -= 1;
        } else {
            $resultado->partidos_perdidos -= 1;
        }

        // Si no quedan partidos, eliminar el registro
        if ($resultado->partidos_jugados <= 0) {
            $resultado->delete();
        } else {
            $resultado->save();
        }
    }
}
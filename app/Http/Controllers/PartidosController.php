<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Fecha;
use App\Models\EquipoEstadisticaTemporada;
use App\Models\JugadorEstadisticaTemporada;
use App\Models\JugadorPartidoStat; // ✅ Nueva tabla de detalle
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartidosController extends Controller
{
    /**
     * ==========================================
     * MUESTRA EL FORMULARIO DE EDICIÓN DETALLADA
     * ==========================================
     */
    public function editDetailed($id)
    {
        $partido = Partido::with([
            'equipoLocal.jugadores.user',
            'equipoVisitante.jugadores.user',
            'fecha'
        ])->findOrFail($id);

        $temporada_id = optional($partido->fecha)->temporada_id ?? $partido->temporada_id;

        $jugadoresLocal = $partido->equipoLocal->jugadores;
        $jugadoresVisitante = $partido->equipoVisitante->jugadores;
        $todosLosJugadores = $jugadoresLocal->merge($jugadoresVisitante);

        $estadisticasPartido = [];

        foreach ($todosLosJugadores as $jugador) {
            $stat = JugadorPartidoStat::where('partido_id', $id)
                ->where('jugador_id', $jugador->id)
                ->first();

            $estadisticasPartido[$jugador->id] = [
                'jugo' => $stat->jugo ?? false,
                'goles' => $stat->goles ?? 0,
                'asistencias' => $stat->asistencias ?? 0,
            ];
        }

        return view('admin.partidos.edit_detailed', compact('partido', 'estadisticasPartido'));
    }

    /**
     * ==========================================
     * ACTUALIZA EL PARTIDO DETALLADAMENTE
     * ==========================================
     */
    public function updateDetailed(Request $request, $id)
    {
        $partido = Partido::findOrFail($id);
        $temporada_id = optional($partido->fecha)->temporada_id ?? $partido->temporada_id;

        DB::beginTransaction();
        try {
            // Si el partido ya tenía resultado, revertimos estadísticas anteriores
            if (!is_null($partido->goles_local) && !is_null($partido->goles_visitante)) {
                $this->revertirEstadisticasEquipos($partido, $partido->goles_local, $partido->goles_visitante);
                $this->revertirEstadisticasJugadoresPorPartido($id, $temporada_id);
            }

            // Guardar nuevo resultado
            $partido->update([
                'goles_local' => $request->input('goles_local', 0),
                'goles_visitante' => $request->input('goles_visitante', 0),
            ]);

            // Aplicar nuevas estadísticas
            $this->aplicarEstadisticasJugadoresPorPartido($request->input('jugadores', []), $partido, $temporada_id);
            $this->aplicarEstadisticasEquipos($partido);

            DB::commit();
            return redirect()
                ->route('admin.temporada.show', $temporada_id)
                ->with('success', 'Partido y estadísticas actualizados correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * ==========================================
     * CREA O ACTUALIZA ESTADÍSTICAS DE JUGADORES POR PARTIDO
     * ==========================================
     */
    private function aplicarEstadisticasJugadoresPorPartido(array $jugadoresData, Partido $partido, int $temporada_id)
    {
        foreach ($jugadoresData as $jugadorId => $datos) {
            // Guardar detalle del partido
            JugadorPartidoStat::updateOrCreate(
                [
                    'partido_id' => $partido->id,
                    'jugador_id' => $jugadorId,
                    'temporada_id' => $temporada_id
                ],
                [
                    'jugo' => !empty($datos['jugo']),
                    'goles' => intval($datos['goles'] ?? 0),
                    'asistencias' => intval($datos['asistencias'] ?? 0),
                ]
            );

            // Actualizar acumulados por temporada
            $total = JugadorPartidoStat::where('jugador_id', $jugadorId)
                ->where('temporada_id', $temporada_id)
                ->selectRaw('
                    COALESCE(SUM(CASE WHEN jugo THEN 1 ELSE 0 END),0) AS pj,
                    COALESCE(SUM(goles),0) AS g,
                    COALESCE(SUM(asistencias),0) AS a
                ')
                ->first();

            JugadorEstadisticaTemporada::updateOrCreate(
                ['jugador_id' => $jugadorId, 'temporada_id' => $temporada_id],
                [
                    'partidos_jugados' => $total->pj,
                    'goles' => $total->g,
                    'asistencias' => $total->a
                ]
            );
        }
    }

    /**
     * ==========================================
     * REVIERTE ESTADÍSTICAS DE JUGADORES (POR PARTIDO)
     * ==========================================
     */
    private function revertirEstadisticasJugadoresPorPartido($partido_id, $temporada_id)
    {
        $jugadores = JugadorPartidoStat::where('partido_id', $partido_id)->get();

        foreach ($jugadores as $stat) {
            $stat->delete();

            // Recalcular acumulados del jugador
            $total = JugadorPartidoStat::where('jugador_id', $stat->jugador_id)
                ->where('temporada_id', $temporada_id)
                ->selectRaw('
                    COALESCE(SUM(CASE WHEN jugo THEN 1 ELSE 0 END),0) AS pj,
                    COALESCE(SUM(goles),0) AS g,
                    COALESCE(SUM(asistencias),0) AS a
                ')
                ->first();

            JugadorEstadisticaTemporada::updateOrCreate(
                ['jugador_id' => $stat->jugador_id, 'temporada_id' => $temporada_id],
                [
                    'partidos_jugados' => $total->pj,
                    'goles' => $total->g,
                    'asistencias' => $total->a
                ]
            );
        }
    }

    /**
     * ==========================================
     * REVERSIÓN Y APLICACIÓN DE ESTADÍSTICAS DE EQUIPOS
     * ==========================================
     */
    private function revertirEstadisticasEquipos($partido, $goles_local, $goles_visitante)
    {
        $temporada_id = optional($partido->fecha)->temporada_id ?? $partido->temporada_id;

        $estadisticas_local = EquipoEstadisticaTemporada::where('equipo_id', $partido->equipo_local_id)
            ->where('temporada_id', $temporada_id)
            ->first();

        $estadisticas_visitante = EquipoEstadisticaTemporada::where('equipo_id', $partido->equipo_visitante_id)
            ->where('temporada_id', $temporada_id)
            ->first();

        if ($estadisticas_local && $estadisticas_visitante) {
            $estadisticas_local->partidos_jugados--;
            $estadisticas_visitante->partidos_jugados--;

            $estadisticas_local->goles_favor -= $goles_local;
            $estadisticas_local->goles_contra -= $goles_visitante;
            $estadisticas_visitante->goles_favor -= $goles_visitante;
            $estadisticas_visitante->goles_contra -= $goles_local;

            if ($goles_local > $goles_visitante) {
                $estadisticas_local->partidos_ganados--;
                $estadisticas_visitante->partidos_perdidos--;
                $estadisticas_local->puntos -= 3;
            } elseif ($goles_local < $goles_visitante) {
                $estadisticas_local->partidos_perdidos--;
                $estadisticas_visitante->partidos_ganados--;
                $estadisticas_visitante->puntos -= 3;
            } else {
                $estadisticas_local->partidos_empatados--;
                $estadisticas_visitante->partidos_empatados--;
                $estadisticas_local->puntos--;
                $estadisticas_visitante->puntos--;
            }

            $estadisticas_local->diferencia_goles = $estadisticas_local->goles_favor - $estadisticas_local->goles_contra;
            $estadisticas_visitante->diferencia_goles = $estadisticas_visitante->goles_favor - $estadisticas_visitante->goles_contra;

            $estadisticas_local->save();
            $estadisticas_visitante->save();
        }
    }

    private function aplicarEstadisticasEquipos($partido)
    {
        $temporada_id = optional($partido->fecha)->temporada_id ?? $partido->temporada_id;

        $estadisticas_local = EquipoEstadisticaTemporada::firstOrCreate(
            ['equipo_id' => $partido->equipo_local_id, 'temporada_id' => $temporada_id]
        );

        $estadisticas_visitante = EquipoEstadisticaTemporada::firstOrCreate(
            ['equipo_id' => $partido->equipo_visitante_id, 'temporada_id' => $temporada_id]
        );

        $estadisticas_local->partidos_jugados++;
        $estadisticas_visitante->partidos_jugados++;

        $estadisticas_local->goles_favor += $partido->goles_local;
        $estadisticas_local->goles_contra += $partido->goles_visitante;
        $estadisticas_visitante->goles_favor += $partido->goles_visitante;
        $estadisticas_visitante->goles_contra += $partido->goles_local;

        if ($partido->goles_local > $partido->goles_visitante) {
            $estadisticas_local->partidos_ganados++;
            $estadisticas_local->puntos += 3;
            $estadisticas_visitante->partidos_perdidos++;
        } elseif ($partido->goles_local < $partido->goles_visitante) {
            $estadisticas_visitante->partidos_ganados++;
            $estadisticas_visitante->puntos += 3;
            $estadisticas_local->partidos_perdidos++;
        } else {
            $estadisticas_local->partidos_empatados++;
            $estadisticas_visitante->partidos_empatados++;
            $estadisticas_local->puntos++;
            $estadisticas_visitante->puntos++;
        }

        $estadisticas_local->diferencia_goles = $estadisticas_local->goles_favor - $estadisticas_local->goles_contra;
        $estadisticas_visitante->diferencia_goles = $estadisticas_visitante->goles_favor - $estadisticas_visitante->goles_contra;

        $estadisticas_local->save();
        $estadisticas_visitante->save();
    }

    /**
     * ==========================================
     * CREA UN NUEVO PARTIDO
     * ==========================================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_id' => 'required|exists:fechas,id',
            'equipo_local_id' => 'required|different:equipo_visitante_id',
            'equipo_visitante_id' => 'required',
        ]);

        $fecha = Fecha::findOrFail($validated['fecha_id']);

        Partido::create([
            'fecha_id' => $validated['fecha_id'],
            'temporada_id' => $fecha->temporada_id,
            'equipo_local_id' => $validated['equipo_local_id'],
            'equipo_visitante_id' => $validated['equipo_visitante_id'],
            'goles_local' => null,
            'goles_visitante' => null,
        ]);

        return back()->with('success', 'Partido creado correctamente.');
    }

    /**
     * ==========================================
     * ELIMINA UN PARTIDO
     * ==========================================
     */
    public function destroy($id)
    {
        $partido = Partido::findOrFail($id);
        $temporada_id = optional($partido->fecha)->temporada_id ?? $partido->temporada_id;

        DB::beginTransaction();
        try {
            if (!is_null($partido->goles_local) && !is_null($partido->goles_visitante)) {
                $this->revertirEstadisticasEquipos($partido, $partido->goles_local, $partido->goles_visitante);
            }

            $this->revertirEstadisticasJugadoresPorPartido($id, $temporada_id);

            $partido->delete();

            DB::commit();
            return response()->json(['ok' => true, 'deleted_id' => (int)$id]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ==========================================
     * LIMPIA TODOS LOS PARTIDOS DE UNA TEMPORADA
     * ==========================================
     */
    public function limpiarPartidos($temporadaId)
    {
        $partidos = Partido::where('temporada_id', $temporadaId)->get();

        foreach ($partidos as $p) {
            $this->revertirEstadisticasJugadoresPorPartido($p->id, $temporadaId);
            if ($p->goles_local !== null && $p->goles_visitante !== null) {
                $this->revertirEstadisticasEquipos($p, $p->goles_local, $p->goles_visitante);
            }
            $p->delete();
        }

        EquipoEstadisticaTemporada::where('temporada_id', $temporadaId)->delete();
        JugadorEstadisticaTemporada::where('temporada_id', $temporadaId)->delete();

        return back()->with('success', 'Todos los partidos y estadísticas fueron eliminados correctamente.');
    }
}

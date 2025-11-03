<?php

namespace App\Http\Controllers;

use App\Models\Fecha;
use App\Models\Temporada;
use App\Models\Partido;
use App\Models\EquipoEstadisticaTemporada;
use App\Models\JugadorPartidoStat;
use App\Models\JugadorEstadisticaTemporada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FechaController extends Controller
{
    /**
     * ==========================================
     * CREA UNA NUEVA FECHA
     * ==========================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'temporada_id' => 'required|exists:temporadas,id',
            'nombre'       => 'required|string|max:255',
            'dia'          => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            Fecha::create($request->only('temporada_id', 'nombre', 'dia'));

            DB::commit();
            return back()->with('success', 'Fecha creada correctamente.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la fecha: ' . $e->getMessage());
        }
    }

    /**
     * ==========================================
     * ELIMINA UNA FECHA Y SUS PARTIDOS (responde JSON, sin redirección)
     * - Revierte tabla de posiciones de equipos si corresponde
     * - Elimina stats por partido de jugadores
     * - Recalcula acumulados por temporada de esos jugadores
     * ==========================================
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            /** @var Fecha $fecha */
            $fecha = Fecha::with('partidos')->findOrFail($id);

            $nombreFecha  = $fecha->nombre;
            $temporadaId  = $fecha->temporada_id;

            foreach ($fecha->partidos as $p) {

                // 1) Revertir estadísticas de equipos si el partido tenía resultado
                if (!is_null($p->goles_local) && !is_null($p->goles_visitante)) {
                    $this->revertirEstadisticasEquiposPorPartido($p);
                }

                // 2) Tomar todos los jugadores que tenían stats en este partido
                $statsPartido = JugadorPartidoStat::where('partido_id', $p->id)->get();
                $jugadoresAfectados = $statsPartido->pluck('jugador_id')->unique()->values();

                // 3) Eliminar los snapshots del partido
                JugadorPartidoStat::where('partido_id', $p->id)->delete();

                // 4) Recalcular acumulados por temporada para cada jugador afectado
                foreach ($jugadoresAfectados as $jugadorId) {
                    $tot = JugadorPartidoStat::where('jugador_id', $jugadorId)
                        ->where('temporada_id', $temporadaId)
                        ->selectRaw("
                            COALESCE(SUM(CASE WHEN jugo THEN 1 ELSE 0 END),0) AS pj,
                            COALESCE(SUM(goles),0)                              AS g,
                            COALESCE(SUM(asistencias),0)                        AS a
                        ")
                        ->first();

                    // Si ya no le queda nada en la temporada, podés dejarlo en 0 o borrar el registro.
                    // Acá lo dejamos en 0s para mantener consistencia.
                    JugadorEstadisticaTemporada::updateOrCreate(
                        ['jugador_id' => $jugadorId, 'temporada_id' => $temporadaId],
                        [
                            'partidos_jugados' => (int)($tot->pj ?? 0),
                            'goles'            => (int)($tot->g  ?? 0),
                            'asistencias'      => (int)($tot->a  ?? 0),
                        ]
                    );
                }

                // 5) Eliminar partido
                $p->delete();
            }

            // 6) Eliminar la fecha
            $fecha->delete();

            DB::commit();

            return response()->json([
                'ok'           => true,
                'deleted_id'   => (int)$id,
                'temporada_id' => $temporadaId,
                'nombre'       => $nombreFecha
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'ok'      => false,
                'message' => 'Error al eliminar la fecha: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revierte la tabla de posiciones de los equipos a partir de un partido
     * (inverso de aplicarEstadisticasEquipos)
     */
    private function revertirEstadisticasEquiposPorPartido(Partido $partido): void
    {
        $temporada_id = optional($partido->fecha)->temporada_id ?? $partido->temporada_id;

        $local = EquipoEstadisticaTemporada::where('equipo_id', $partido->equipo_local_id)
            ->where('temporada_id', $temporada_id)
            ->first();

        $visitante = EquipoEstadisticaTemporada::where('equipo_id', $partido->equipo_visitante_id)
            ->where('temporada_id', $temporada_id)
            ->first();

        if (!$local || !$visitante) {
            return;
        }

        // Partidos jugados
        $local->partidos_jugados      = max(0, $local->partidos_jugados - 1);
        $visitante->partidos_jugados  = max(0, $visitante->partidos_jugados - 1);

        // Goles a favor/contra
        $local->goles_favor    = max(0, $local->goles_favor    - (int)$partido->goles_local);
        $local->goles_contra   = max(0, $local->goles_contra   - (int)$partido->goles_visitante);
        $visitante->goles_favor  = max(0, $visitante->goles_favor  - (int)$partido->goles_visitante);
        $visitante->goles_contra = max(0, $visitante->goles_contra - (int)$partido->goles_local);

        // Resultado
        if ($partido->goles_local > $partido->goles_visitante) {
            $local->partidos_ganados     = max(0, $local->partidos_ganados - 1);
            $visitante->partidos_perdidos = max(0, $visitante->partidos_perdidos - 1);
            $local->puntos               = max(0, $local->puntos - 3);
        } elseif ($partido->goles_local < $partido->goles_visitante) {
            $local->partidos_perdidos     = max(0, $local->partidos_perdidos - 1);
            $visitante->partidos_ganados  = max(0, $visitante->partidos_ganados - 1);
            $visitante->puntos            = max(0, $visitante->puntos - 3);
        } else {
            $local->partidos_empatados     = max(0, $local->partidos_empatados - 1);
            $visitante->partidos_empatados = max(0, $visitante->partidos_empatados - 1);
            $local->puntos                 = max(0, $local->puntos - 1);
            $visitante->puntos             = max(0, $visitante->puntos - 1);
        }

        // Diferencia de gol
        $local->diferencia_goles     = $local->goles_favor - $local->goles_contra;
        $visitante->diferencia_goles = $visitante->goles_favor - $visitante->goles_contra;

        $local->save();
        $visitante->save();
    }

    /**
     * ==========================================
     * CORRIGE FECHAS SIN temporada_id
     * ==========================================
     */
    public function fixMissingTemporadaIds()
    {
        try {
            DB::beginTransaction();

            $temporada_activa = Temporada::where('activa', true)->first();

            if (!$temporada_activa) {
                DB::rollBack();
                return back()->with('error', 'No hay temporada activa configurada.');
            }

            $fechas_sin_temporada = Fecha::whereNull('temporada_id')->get();
            $count = 0;

            foreach ($fechas_sin_temporada as $fecha) {
                $fecha->update(['temporada_id' => $temporada_activa->id]);
                $count++;
            }

            DB::commit();

            return back()->with('success', "Se corrigieron {$count} fechas sin temporada asignada.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al corregir fechas: ' . $e->getMessage());
        }
    }
}

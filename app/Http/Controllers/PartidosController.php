<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Fecha;
use App\Models\EquipoEstadisticaTemporada;
use App\Models\JugadorEstadisticaTemporada;
use Illuminate\Http\Request;

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

        $estadisticasPartido = [];
        $temporada_id = optional($partido->fecha)->temporada_id ?? $partido->temporada_id;

        // Obtener todos los jugadores
        $jugadoresLocal = $partido->equipoLocal->jugadores;
        $jugadoresVisitante = $partido->equipoVisitante->jugadores;
        $todosLosJugadores = $jugadoresLocal->merge($jugadoresVisitante);

        // Por defecto, todas las estadísticas en 0
        // (No podemos saber qué hizo cada jugador en este partido específico sin tabla intermedia)
        foreach ($todosLosJugadores as $jugador) {
            $estadisticasPartido[$jugador->id] = [
                'jugo' => false,
                'goles' => 0,
                'asistencias' => 0,
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

        // Guardar resultado anterior
        $goles_local_anterior = $partido->goles_local;
        $goles_visitante_anterior = $partido->goles_visitante;
        
        // Detectar si el partido ya tenía resultado definido
        $yaDefinido = ($goles_local_anterior !== null && $goles_visitante_anterior !== null);

        // Jugadores del formulario (incluye datos nuevos y anteriores)
        $jugadores = $request->input('jugadores', []);

        // PASO 1: Si ya estaba definido, REVERTIR estadísticas previas
        if ($yaDefinido) {
            $this->revertirEstadisticasEquipos($partido, $goles_local_anterior, $goles_visitante_anterior);
            $this->revertirEstadisticasJugadores($jugadores, $temporada_id);
        }

        // PASO 2: Actualizar resultado del partido
        $partido->update([
            'goles_local' => $request->input('goles_local', 0),
            'goles_visitante' => $request->input('goles_visitante', 0),
        ]);

        // PASO 3: Aplicar nuevas estadísticas
        $this->aplicarEstadisticasJugadores($jugadores, $temporada_id);
        $this->aplicarEstadisticasEquipos($partido);

        return redirect()
            ->route('admin.temporada.show', $temporada_id)
            ->with('success', 'Estadísticas y resultado actualizados correctamente.');
    }

    /**
     * ==========================================
     * REVIERTE ESTADÍSTICAS DE JUGADORES
     * ==========================================
     */
    private function revertirEstadisticasJugadores($jugadoresData, $temporada_id)
    {
        foreach ($jugadoresData as $jugadorId => $datos) {
            // Solo revertir si había datos anteriores
            if (!isset($datos['jugo_anterior']) && !isset($datos['goles_anterior']) && !isset($datos['asistencias_anterior'])) {
                continue;
            }

            $estadisticasJugador = JugadorEstadisticaTemporada::where('jugador_id', $jugadorId)
                ->where('temporada_id', $temporada_id)
                ->first();

            if ($estadisticasJugador) {
                // Revertir partidos jugados si había jugado antes
                if (isset($datos['jugo_anterior']) && $datos['jugo_anterior']) {
                    $estadisticasJugador->partidos_jugados = max(0, $estadisticasJugador->partidos_jugados - 1);
                }

                // Revertir goles y asistencias previas
                $estadisticasJugador->goles = max(0, $estadisticasJugador->goles - intval($datos['goles_anterior'] ?? 0));
                $estadisticasJugador->asistencias = max(0, $estadisticasJugador->asistencias - intval($datos['asistencias_anterior'] ?? 0));

                $estadisticasJugador->save();
            }
        }
    }

    /**
     * ==========================================
     * APLICA ESTADÍSTICAS DE JUGADORES
     * ==========================================
     */
    private function aplicarEstadisticasJugadores($jugadoresData, $temporada_id)
    {
        foreach ($jugadoresData as $jugadorId => $datos) {
            $estadisticasJugador = JugadorEstadisticaTemporada::firstOrCreate(
                [
                    'jugador_id' => $jugadorId,
                    'temporada_id' => $temporada_id
                ],
                [
                    'partidos_jugados' => 0,
                    'goles' => 0,
                    'asistencias' => 0
                ]
            );

            // Sumar partidos jugados si jugó
            if (isset($datos['jugo']) && $datos['jugo']) {
                $estadisticasJugador->partidos_jugados += 1;
            }

            // Sumar goles y asistencias
            $estadisticasJugador->goles += intval($datos['goles'] ?? 0);
            $estadisticasJugador->asistencias += intval($datos['asistencias'] ?? 0);

            $estadisticasJugador->save();
        }
    }

    /**
     * ==========================================
     * REVIERTE ESTADÍSTICAS DE EQUIPOS
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
            // Revertir partidos jugados
            $estadisticas_local->partidos_jugados = max(0, $estadisticas_local->partidos_jugados - 1);
            $estadisticas_visitante->partidos_jugados = max(0, $estadisticas_visitante->partidos_jugados - 1);

            // Revertir goles
            $estadisticas_local->goles_favor = max(0, $estadisticas_local->goles_favor - $goles_local);
            $estadisticas_local->goles_contra = max(0, $estadisticas_local->goles_contra - $goles_visitante);
            $estadisticas_visitante->goles_favor = max(0, $estadisticas_visitante->goles_favor - $goles_visitante);
            $estadisticas_visitante->goles_contra = max(0, $estadisticas_visitante->goles_contra - $goles_local);

            // Revertir resultado
            if ($goles_local > $goles_visitante) {
                // Local ganó
                $estadisticas_local->partidos_ganados = max(0, $estadisticas_local->partidos_ganados - 1);
                $estadisticas_visitante->partidos_perdidos = max(0, $estadisticas_visitante->partidos_perdidos - 1);
                $estadisticas_local->puntos = max(0, $estadisticas_local->puntos - 3);
            } elseif ($goles_local < $goles_visitante) {
                // Visitante ganó
                $estadisticas_local->partidos_perdidos = max(0, $estadisticas_local->partidos_perdidos - 1);
                $estadisticas_visitante->partidos_ganados = max(0, $estadisticas_visitante->partidos_ganados - 1);
                $estadisticas_visitante->puntos = max(0, $estadisticas_visitante->puntos - 3);
            } else {
                // Empate
                $estadisticas_local->partidos_empatados = max(0, $estadisticas_local->partidos_empatados - 1);
                $estadisticas_visitante->partidos_empatados = max(0, $estadisticas_visitante->partidos_empatados - 1);
                $estadisticas_local->puntos = max(0, $estadisticas_local->puntos - 1);
                $estadisticas_visitante->puntos = max(0, $estadisticas_visitante->puntos - 1);
            }

            // Recalcular diferencia
            $estadisticas_local->diferencia_goles = $estadisticas_local->goles_favor - $estadisticas_local->goles_contra;
            $estadisticas_visitante->diferencia_goles = $estadisticas_visitante->goles_favor - $estadisticas_visitante->goles_contra;

            $estadisticas_local->save();
            $estadisticas_visitante->save();
        }
    }

    /**
     * ==========================================
     * APLICA ESTADÍSTICAS DE EQUIPOS
     * ==========================================
     */
    private function aplicarEstadisticasEquipos($partido)
    {
        $temporada_id = optional($partido->fecha)->temporada_id ?? $partido->temporada_id;

        $estadisticas_local = EquipoEstadisticaTemporada::firstOrCreate(
            ['equipo_id' => $partido->equipo_local_id, 'temporada_id' => $temporada_id],
            [
                'partidos_jugados' => 0, 'partidos_ganados' => 0, 'partidos_empatados' => 0,
                'partidos_perdidos' => 0, 'goles_favor' => 0, 'goles_contra' => 0,
                'diferencia_goles' => 0, 'puntos' => 0
            ]
        );

        $estadisticas_visitante = EquipoEstadisticaTemporada::firstOrCreate(
            ['equipo_id' => $partido->equipo_visitante_id, 'temporada_id' => $temporada_id],
            [
                'partidos_jugados' => 0, 'partidos_ganados' => 0, 'partidos_empatados' => 0,
                'partidos_perdidos' => 0, 'goles_favor' => 0, 'goles_contra' => 0,
                'diferencia_goles' => 0, 'puntos' => 0
            ]
        );

        // Incrementar partidos jugados
        $estadisticas_local->partidos_jugados++;
        $estadisticas_visitante->partidos_jugados++;

        // Sumar goles
        $estadisticas_local->goles_favor += $partido->goles_local;
        $estadisticas_local->goles_contra += $partido->goles_visitante;
        $estadisticas_visitante->goles_favor += $partido->goles_visitante;
        $estadisticas_visitante->goles_contra += $partido->goles_local;

        // Calcular diferencia
        $estadisticas_local->diferencia_goles = $estadisticas_local->goles_favor - $estadisticas_local->goles_contra;
        $estadisticas_visitante->diferencia_goles = $estadisticas_visitante->goles_favor - $estadisticas_visitante->goles_contra;

        // Aplicar puntos según resultado
        if ($partido->goles_local > $partido->goles_visitante) {
            // ✅ Local GANA: +3 puntos
            $estadisticas_local->partidos_ganados++;
            $estadisticas_local->puntos += 3;
            // ✅ Visitante PIERDE: +0 puntos
            $estadisticas_visitante->partidos_perdidos++;
        } elseif ($partido->goles_local < $partido->goles_visitante) {
            // ✅ Visitante GANA: +3 puntos
            $estadisticas_visitante->partidos_ganados++;
            $estadisticas_visitante->puntos += 3;
            // ✅ Local PIERDE: +0 puntos
            $estadisticas_local->partidos_perdidos++;
        } else {
            // ✅ EMPATE: +1 punto cada uno
            $estadisticas_local->partidos_empatados++;
            $estadisticas_visitante->partidos_empatados++;
            $estadisticas_local->puntos += 1;
            $estadisticas_visitante->puntos += 1;
        }

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
        
        // Solo revertir si el partido tenía resultado
        if ($partido->goles_local !== null && $partido->goles_visitante !== null) {
            $this->revertirEstadisticasEquipos($partido, $partido->goles_local, $partido->goles_visitante);
            
            // Para jugadores, necesitarías saber sus estadísticas en este partido
            // Sin tabla intermedia, no es posible revertir con precisión
        }
        
        $partido->delete();

        return back()->with('success', 'Partido eliminado correctamente.');
    }

    /**
     * ==========================================
     * LIMPIA TODOS LOS PARTIDOS DE UNA TEMPORADA
     * ==========================================
     */
    public function limpiarPartidos($temporadaId)
    {
        $partidos = Partido::where('temporada_id', $temporadaId)->get();

        foreach ($partidos as $partido) {
            if ($partido->goles_local !== null && $partido->goles_visitante !== null) {
                $this->revertirEstadisticasEquipos($partido, $partido->goles_local, $partido->goles_visitante);
            }
            $partido->delete();
        }

        // Resetear todas las estadísticas de jugadores de la temporada
        JugadorEstadisticaTemporada::where('temporada_id', $temporadaId)->delete();
        EquipoEstadisticaTemporada::where('temporada_id', $temporadaId)->delete();

        return back()->with('success', 'Todos los partidos de la temporada han sido eliminados correctamente.');
    }
}
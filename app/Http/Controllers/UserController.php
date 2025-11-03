<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Jugador;
use App\Models\Equipo;
use App\Models\Temporada;
use App\Models\EquipoEstadisticaTemporada;

class UserController extends Controller
{
    // ============================================================
    // DASHBOARD PRINCIPAL DEL JUGADOR
    // ============================================================
    public function dashboard(Request $request)
    {
        $section = $request->get('section', 'mi-carnet');
        $user = Auth::user();

        // Jugador y equipo del usuario
        $jugador = Jugador::where('user_jugadores', $user->id)->first();
        $equipo  = $jugador ? Equipo::find($jugador->equipo_id) : null;

        // ============================================================
        // ACUMULADO DEL JUGADOR (todas las temporadas) + persistencia
        // ============================================================
        $totPJ = 0; $totG = 0; $totA = 0;

        if ($jugador) {
            $acum = DB::table('jugador_estadisticas_temporada')
                ->where('jugador_id', $jugador->id)
                ->selectRaw('
                    COALESCE(SUM(partidos_jugados),0) AS pj,
                    COALESCE(SUM(goles),0)             AS g,
                    COALESCE(SUM(asistencias),0)       AS a
                ')
                ->first();

            $totPJ = (int)($acum->pj ?? 0);
            $totG  = (int)($acum->g  ?? 0);
            $totA  = (int)($acum->a  ?? 0);

            // Persistimos en jugadores para que siempre esté al día
            $jugador->partidos_jugados = $totPJ;
            $jugador->goles            = $totG;
            $jugador->asistencias      = $totA;
            $jugador->save();
        }

        $datosJugador = [
            'dni'          => $user->dni ?? '00000000',
            'nombre'       => $user->name ?? 'N/A',
            'pj'           => $totPJ,
            'goles'        => $totG,
            'asistencias'  => $totA,
            'foto_jugador' => $jugador->foto_jugador ?? null,
        ];

        $datosEquipo = [
            'nombre'     => $equipo?->NombreEquipos ?? 'Sin equipo',
            'id'         => $equipo?->id,
            'FotoEquipo' => $equipo?->FotoEquipo ?? null,
        ];

        $mediaGoleadora = $totPJ > 0 ? ($totG / $totPJ) : 0;

        // ============================================================
        // SECCIÓN: TEMPORADA ACTUAL (ranking)
        // ============================================================
        if ($section === 'temporada-actual') {
            $temporadas      = Temporada::all();
            $temporadaId     = $request->query('temporada');
            $temporadaActual = $temporadaId ? Temporada::find($temporadaId)
                                            : Temporada::latest()->first();

            $equipos = collect();
            $tablaPosiciones = collect();

            if ($temporadaActual) {
                $equipos = $temporadaActual->equipos ?? collect();
                $tablaPosiciones = EquipoEstadisticaTemporada::where('temporada_id', $temporadaActual->id)
                    ->with('equipo')
                    ->orderBy('puntos', 'DESC')
                    ->orderBy('diferencia_goles', 'DESC')
                    ->orderBy('goles_favor', 'DESC')
                    ->get();
            }

            return view('user.temporada-actual', [
                'equipo'          => $datosEquipo,
                'jugador'         => $datosJugador,
                'mediaGoleadora'  => $mediaGoleadora,
                'section'         => $section,
                'temporadas'      => $temporadas,
                'temporadaActual' => $temporadaActual,
                'equipos'         => $equipos,
                'tablaPosiciones' => $tablaPosiciones,
            ]);
        }

        // ============================================================
        // SECCIÓN: HISTÓRICO DEL CLUB (totales acumulados del equipo)
        // ============================================================
        if ($section === 'historico') {
            if ($equipo) {
                $acum = DB::table('equipo_estadisticas_temporada')
                    ->where('equipo_id', $equipo->id)
                    ->selectRaw('
                        COALESCE(SUM(partidos_jugados), 0)   AS pj,
                        COALESCE(SUM(partidos_ganados), 0)   AS pg,
                        COALESCE(SUM(partidos_empatados), 0) AS pe,
                        COALESCE(SUM(partidos_perdidos), 0)  AS pp,
                        COALESCE(SUM(goles_favor), 0)        AS gf,
                        COALESCE(SUM(goles_contra), 0)       AS gc,
                        COALESCE(SUM(puntos), 0)             AS pts
                    ')
                    ->first();

                $pj  = (int)($acum->pj  ?? 0);
                $pg  = (int)($acum->pg  ?? 0);
                $pe  = (int)($acum->pe  ?? 0);
                $pp  = (int)($acum->pp  ?? 0);
                $gf  = (int)($acum->gf  ?? 0);
                $gc  = (int)($acum->gc  ?? 0);
                $pts = (int)($acum->pts ?? 0);

                if ($pj === 0) {
                    $pj = $pg + $pe + $pp; // por si no guardás PJ
                }

                // Si no hay puntos almacenados, calcular por 3-1-0
                $ptsCalculados = ($pg * 3) + ($pe * 1);
                if ($pts === 0 && ($pg > 0 || $pe > 0)) {
                    $pts = $ptsCalculados;
                }

                $promVictorias = $pj > 0 ? round($pg / $pj, 3) : 0;

                return view('user.historico', [
                    'equipo' => $equipo,
                    'tot' => [
                        'pj' => $pj,
                        'pg' => $pg,
                        'pe' => $pe,
                        'pp' => $pp,
                        'gf' => $gf,
                        'gc' => $gc,
                        'pts' => $pts,
                        'prom_victorias' => $promVictorias,
                    ],
                ]);
            } else {
                return view('user.historico', [
                    'equipo' => null,
                    'tot' => [
                        'pj' => 0, 'pg' => 0, 'pe' => 0, 'pp' => 0,
                        'gf' => 0, 'gc' => 0, 'pts' => 0, 'prom_victorias' => 0,
                    ],
                ]);
            }
        }

        // ============================================================
        // SECCIÓN: RANKING HISTÓRICO (todas las temporadas) + GUARDAR EN `equipos`
        // ============================================================
        if ($section === 'ranking-historico') {

            // 1) Agregar totales por equipo de TODAS las temporadas
            $tablaHistorica = DB::table('equipo_estadisticas_temporada as eet')
                ->join('equipos as e', 'e.id', '=', 'eet.equipo_id')
                ->selectRaw("
                    e.id,
                    e.NombreEquipos,
                    e.FotoEquipo,
                    COALESCE(SUM(eet.partidos_ganados), 0)   as pg,
                    COALESCE(SUM(eet.partidos_empatados), 0) as pe,
                    COALESCE(SUM(eet.partidos_perdidos), 0)  as pp,
                    COALESCE(SUM(eet.goles_favor), 0)        as gf,
                    COALESCE(SUM(eet.goles_contra), 0)       as gc,
                    COALESCE(SUM(eet.partidos_jugados), 0)   as pj,
                    COALESCE(SUM(eet.puntos), 0)             as pts_sum
                ")
                ->groupBy('e.id', 'e.NombreEquipos', 'e.FotoEquipo')
                ->get()
                ->map(function ($row) {
                    // Derivar PJ si no existe
                    if ((int)$row->pj === 0) {
                        $row->pj = (int)$row->pg + (int)$row->pe + (int)$row->pp;
                    }
                    // Puntos para ordenar: usar sumados si existen; si no, calcular 3-1-0
                    $calc   = (int)$row->pg * 3 + (int)$row->pe;
                    $row->pts = (int)$row->pts_sum > 0 ? (int)$row->pts_sum : $calc;

                    // Derivados
                    $row->dg        = (int)$row->gf - (int)$row->gc;
                    $row->prom_vict = (int)$row->pj > 0 ? round(((int)$row->pg) / ((int)$row->pj), 3) : 0;

                    return $row;
                });

            // 2) ORDEN para mostrar
            $tablaHistorica = $tablaHistorica
                ->sortByDesc(fn($r) => [$r->pts, $r->dg, $r->gf])
                ->values();

            // 3) PERSISTIR en tabla `equipos` usando tus columnas reales:
            //    partidos_totales, goles_totales_favor, goles_totales_contra,
            //    partidos_ganados, partidos_empatados, partidos_perdidos
            foreach ($tablaHistorica as $fila) {
                DB::table('equipos')
                    ->where('id', $fila->id)
                    ->update([
                        'partidos_totales'      => (int)$fila->pj,
                        'goles_totales_favor'   => (int)$fila->gf,
                        'goles_totales_contra'  => (int)$fila->gc,
                        'partidos_ganados'      => (int)$fila->pg,
                        'partidos_empatados'    => (int)$fila->pe,
                        'partidos_perdidos'     => (int)$fila->pp,
                    ]);
            }

            return view('user.ranking-historico', [
                'tablaHistorica' => $tablaHistorica,
            ]);
        }

        // ============================================================
        // SECCIÓN: MÁXIMOS GOLEADORES (por temporada)
        // ============================================================
        if ($section === 'maximos-goleadores') {
            return $this->maximosGoleadores($request);
        }

        // ============================================================
        // SECCIÓN: MÁXIMOS ASISTENTES (por temporada)
        // ============================================================
        if ($section === 'maximos-asistentes') {
            return $this->maximosAsistentes($request);
        }

        // ============================================================
        // SECCIÓN DEFAULT: MI CARNET
        // ============================================================
        return view('user.mi-carnet', [
            'equipo'         => $datosEquipo,
            'jugador'        => $datosJugador,
            'mediaGoleadora' => $mediaGoleadora,
        ]);
    }

    // ============================================================
    // MÁXIMOS GOLEADORES (por temporada)
    // ============================================================
    public function maximosGoleadores(Request $request)
    {
        $temporadaId     = $request->query('temporada');
        $temporadaActual = $temporadaId ? Temporada::find($temporadaId) : null;

        $goleadores = collect();
        if ($temporadaId) {
            $goleadores = DB::table('jugador_estadisticas_temporada AS jet')
                ->join('jugadores AS j', 'j.id', '=', 'jet.jugador_id')
                ->leftJoin('equipos AS e', 'e.id', '=', 'j.equipo_id')
                ->leftJoin('users  AS u', 'u.id', '=', 'j.user_jugadores')
                ->where('jet.temporada_id', $temporadaId)
                ->selectRaw('u.name AS jugador, e.NombreEquipos AS equipo, e.FotoEquipo AS equipo_logo,
                             jet.goles, jet.partidos_jugados AS partidos')
                ->orderByDesc('jet.goles')
                ->limit(50)
                ->get()
                ->map(fn($r) => [
                    'jugador'     => $r->jugador ?? '—',
                    'equipo'      => $r->equipo  ?? 'Sin equipo',
                    'equipo_logo' => $r->equipo_logo,
                    'goles'       => (int)$r->goles,
                    'partidos'    => (int)$r->partidos,
                ]);
        }

        return view('user.maximos-goleadores', compact('temporadaId', 'temporadaActual', 'goleadores'));
    }

    // ============================================================
    // MÁXIMOS ASISTENTES (por temporada)
    // ============================================================
    public function maximosAsistentes(Request $request)
    {
        $temporadaId     = $request->query('temporada');
        $temporadaActual = $temporadaId ? Temporada::find($temporadaId) : null;

        $asistentes = collect();
        if ($temporadaId) {
            $asistentes = DB::table('jugador_estadisticas_temporada AS jet')
                ->join('jugadores AS j', 'j.id', '=', 'jet.jugador_id')
                ->leftJoin('equipos AS e', 'e.id', '=', 'j.equipo_id')
                ->leftJoin('users  AS u', 'u.id', '=', 'j.user_jugadores')
                ->where('jet.temporada_id', $temporadaId)
                ->selectRaw('u.name AS jugador, e.NombreEquipos AS equipo, e.FotoEquipo,
                             jet.asistencias, jet.partidos_jugados AS partidos')
                ->orderByDesc('jet.asistencias')
                ->limit(50)
                ->get()
                ->map(fn($r) => [
                    'jugador'     => $r->jugador ?? '—',
                    'equipo'      => $r->equipo  ?? 'Sin equipo',
                    'FotoEquipo'  => $r->FotoEquipo,
                    'asistencias' => (int)$r->asistencias,
                    'partidos'    => (int)$r->partidos,
                ]);
        }

        return view('user.maximos-asistentes', compact('temporadaId', 'temporadaActual', 'asistentes'));
    }

    // ============================================================
    // MÁXIMOS GOLEADORES HISTÓRICOS (todas las temporadas)
    // ============================================================
    public function maximosGoleadoresHistorico()
    {
        $goleadores = DB::table('jugador_estadisticas_temporada as jet')
            ->join('jugadores as j', 'j.id', '=', 'jet.jugador_id')
            ->leftJoin('users as u', 'u.id', '=', 'j.user_jugadores')
            ->leftJoin('equipos as e', 'e.id', '=', 'j.equipo_id')
            ->selectRaw("
                j.id as jugador_id,
                COALESCE(u.name, '—') as jugador,
                COALESCE(e.NombreEquipos, 'Sin equipo') as equipo,
                e.FotoEquipo as equipo_logo,
                COALESCE(SUM(jet.goles), 0) as goles,
                COALESCE(SUM(jet.partidos_jugados), 0) as partidos
            ")
            ->groupBy('j.id', 'u.name', 'e.NombreEquipos', 'e.FotoEquipo')
            ->orderByDesc('goles')
            ->limit(100)
            ->get();

        return view('user.maximos-goleadores-historico', [
            'temporadaId'     => null,
            'temporadaActual' => null,
            'goleadores'      => $goleadores,
            'esHistorico'     => true,
        ]);
    }

    // ============================================================
    // MÁXIMOS ASISTENTES HISTÓRICOS (todas las temporadas)
    // ============================================================
    public function maximosAsistentesHistorico()
    {
        $asistentes = DB::table('jugador_estadisticas_temporada as jet')
            ->join('jugadores as j', 'j.id', '=', 'jet.jugador_id')
            ->leftJoin('users as u', 'u.id', '=', 'j.user_jugadores')
            ->leftJoin('equipos as e', 'e.id', '=', 'j.equipo_id')
            ->selectRaw("
                j.id as jugador_id,
                COALESCE(u.name, '—') as jugador,
                COALESCE(e.NombreEquipos, 'Sin equipo') as equipo,
                e.FotoEquipo as FotoEquipo,
                COALESCE(SUM(jet.asistencias), 0) as asistencias,
                COALESCE(SUM(jet.partidos_jugados), 0) as partidos
            ")
            ->groupBy('j.id', 'u.name', 'e.NombreEquipos', 'e.FotoEquipo')
            ->orderByDesc('asistencias')
            ->limit(100)
            ->get();

        return view('user.maximos-asistentes-historico', [
            'temporadaId'     => null,
            'temporadaActual' => null,
            'asistentes'      => $asistentes,
            'esHistorico'     => true,
        ]);
    }
}

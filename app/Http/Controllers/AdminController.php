<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Temporada;
use App\Models\Equipo;
use App\Models\EquipoEstadisticaTemporada;
use App\Models\JugadorEstadisticaTemporada; // 👈 Agregar esta línea

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $section = $request->input('section');

        $validSections = [
            'jugadores',
            'equipos',
            'temporada',
            'resultados',
            'ranking',
            'temporadacargadas'
        ];

        if (!in_array($section, $validSections)) {
            $section = null;
        }

        $temporadas = collect();
        $equipos = collect();
        $temporadaActual = null;
        $tablaPosiciones = collect();

        if ($section === 'temporadacargadas') {
            $temporadas = Temporada::orderBy('created_at', 'desc')->get();
        }

        if ($section === 'temporada') {
            $temporadas = Temporada::with('equipos')->orderBy('created_at', 'desc')->get();
            $equipos = Equipo::orderBy('NombreEquipos')->get();
        }

        if ($section === 'ranking') {
            $temporadas = Temporada::all();
            $temporadaId = $request->query('temporada');
            $temporadaActual = $temporadaId ? Temporada::find($temporadaId) : Temporada::latest()->first();

            if ($temporadaActual) {
                $equipos = $temporadaActual->equipos;
                $tablaPosiciones = $this->calcularTablaPosiciones($temporadaActual->id);
            } else {
                $equipos = collect();
                $tablaPosiciones = collect();
            }

            return view('admin.ranking', compact(
                'section',
                'temporadas',
                'equipos',
                'temporadaActual',
                'tablaPosiciones'
            ));
        }

        return view('admin.dashboard', compact(
            'section',
            'temporadas',
            'equipos'
        ));
    }

    // ✅ MÉTODO ACTUALIZADO CON CONSULTA REAL A LA BASE DE DATOS
    public function maximosGoleadores(Request $request)
    {
        $temporadaId = $request->query('temporada');
        
        $temporadaActual = null;
        $goleadores = collect();
        
        if ($temporadaId) {
            $temporadaActual = Temporada::find($temporadaId);
            
            // Consulta real a la base de datos con relaciones
            $goleadores = JugadorEstadisticaTemporada::where('temporada_id', $temporadaId)
                ->with(['jugador.user', 'jugador.equipo']) // Carga user y equipo
                ->orderBy('goles', 'DESC')
                ->take(20) // Top 20 goleadores
                ->get()
                ->filter(function($estadistica) {
                    // Filtrar solo los que tienen jugador y user válidos
                    return $estadistica->jugador && $estadistica->jugador->user;
                })
                ->map(function($estadistica) {
                    return [
                        'jugador' => $estadistica->jugador->user->name,
                        'equipo' => $estadistica->jugador->equipo->NombreEquipos ?? 'Sin equipo',
                        'equipo_logo' => $estadistica->jugador->equipo->FotoEquipo ?? null,
                        'goles' => $estadistica->goles,
                        'partidos' => $estadistica->partidos_jugados,
                        // Para debug
                        'jugador_id' => $estadistica->jugador_id,
                        'user_id' => $estadistica->jugador->user_jugadores ?? null,
                    ];
                });
        }
        
        return view('admin.maximos_goleadores', compact(
            'temporadaId', 
            'temporadaActual',
            'goleadores'
        ));
    }

    // ✅ MÉTODO ACTUALIZADO PARA ASISTENTES
public function maximosAsistentes(Request $request)
{
    $temporadaId = $request->query('temporada');
    
    $temporadaActual = null;
    $asistentes = collect();
    
    if ($temporadaId) {
        $temporadaActual = Temporada::find($temporadaId);
        
        // Consulta real a la base de datos con relaciones
        $asistentes = JugadorEstadisticaTemporada::where('temporada_id', $temporadaId)
            ->with(['jugador.user', 'jugador.equipo'])
            ->orderBy('asistencias', 'DESC')
            ->take(20) // Top 20 asistentes
            ->get()
            ->filter(function($estadistica) {
                // Filtrar solo los que tienen jugador y user válidos
                return $estadistica->jugador 
                    && $estadistica->jugador->user 
                    && $estadistica->jugador->equipo;
            })
            ->map(function($estadistica) {
                return [
                    'jugador' => $estadistica->jugador->user->name,
                    'equipo' => $estadistica->jugador->equipo->NombreEquipos ?? 'Sin equipo',
                    'FotoEquipo' => $estadistica->jugador->equipo->FotoEquipo ?? null,
                    'asistencias' => $estadistica->asistencias,
                    'partidos' => $estadistica->partidos_jugados,
                ];
            });
    }
    
    return view('admin.maximos_asistentes', compact(
        'temporadaId', 
        'temporadaActual',
        'asistentes'
    ));
}
    private function calcularTablaPosiciones($temporadaId)
    {
        return EquipoEstadisticaTemporada::where('temporada_id', $temporadaId)
            ->with('equipo')
            ->orderBy('puntos', 'DESC')
            ->orderBy('diferencia_goles', 'DESC')
            ->orderBy('goles_favor', 'DESC')
            ->get();
    }

    public function storeTemporadaDesdeDashboard(Request $request)
    {
        $request->validate([
            'nombretemporada' => 'required|string|max:255|unique:temporadas,NombreTemporada',
            'equipos' => 'required|array|min:1',
            'equipos.*' => 'exists:equipos,id',
        ]);

        try {
            DB::beginTransaction();

            $temporada = new Temporada();
            $temporada->NombreTemporada = $request->input('nombretemporada');
            $temporada->save();

            $temporada->equipos()->attach($request->input('equipos'));

            DB::commit();

            return redirect()
                ->route('admin.dashboard', ['section' => 'temporada'])
                ->with('success', 'Temporada creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.dashboard', ['section' => 'temporada'])
                ->with('error', 'Ocurrió un error al crear la temporada: ' . $e->getMessage());
        }
    }
}
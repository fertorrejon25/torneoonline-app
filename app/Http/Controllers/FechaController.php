<?php

namespace App\Http\Controllers;

use App\Models\Fecha;
use App\Models\Temporada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FechaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'temporada_id' => 'required|exists:temporadas,id',
            'nombre' => 'required|string|max:255',
            'dia' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            Fecha::create($request->only('temporada_id', 'nombre', 'dia'));

            DB::commit();

            return back()->with('success', 'Fecha creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la fecha: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar una fecha y todos sus partidos asociados
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $fecha = Fecha::with('temporada')->findOrFail($id);
            $temporadaId = $fecha->temporada_id;
            $nombreFecha = $fecha->nombre;
            
            // Eliminar todos los partidos asociados
            $fecha->partidos()->delete(); 
            
            // Eliminar la fecha
            $fecha->delete();

            DB::commit();

            return redirect()
                ->route('admin.temporada.show', $temporadaId)
                ->with('success', "Fecha '{$nombreFecha}' eliminada correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Error al eliminar la fecha: ' . $e->getMessage());
        }
    }

    /**
     * Método para corregir fechas sin temporada_id
     */
    public function fixMissingTemporadaIds()
    {
        try {
            DB::beginTransaction();

            $temporada_activa = Temporada::where('activa', true)->first();
            
            if (!$temporada_activa) {
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

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al corregir fechas: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Temporada;
use App\Models\Equipo;
use Illuminate\Support\Facades\DB;

class TemporadaController extends Controller
{
    /**
     * ============================================================
     * Muestra formulario de creación y listado de temporadas
     * ============================================================
     */
    public function create()
    {
        $temporadas = Temporada::with('equipos')->orderBy('created_at', 'desc')->get();
        $equipos = Equipo::orderBy('NombreEquipos')->get();

        return view('admin.temporada_nueva', compact('temporadas', 'equipos'));
    }

    /**
     * ============================================================
     * Guarda nueva temporada con equipos
     * ============================================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombretemporada' => 'required|string|max:255|unique:temporadas,NombreTemporada',
            'equipos' => 'required|array|min:1',
            'equipos.*' => 'exists:equipos,id',
        ], [
            'nombretemporada.required' => 'El nombre de la temporada es obligatorio.',
            'nombretemporada.unique' => 'Ya existe una temporada con ese nombre.',
            'equipos.required' => 'Debes seleccionar al menos un equipo.',
            'equipos.*.exists' => 'Uno de los equipos seleccionados no es válido.',
        ]);

        try {
            DB::beginTransaction();

            $temporada = Temporada::create([
                'NombreTemporada' => $request->nombretemporada,
            ]);

            $temporada->equipos()->attach($request->equipos);

            DB::commit();

            return redirect()->back()->with('success', 'Temporada creada correctamente con ' . count($request->equipos) . ' equipo(s).');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al guardar la temporada: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * Muestra listado general de temporadas
     * ============================================================
     */
    public function index()
    {
        $temporadas = Temporada::with('equipos')->orderBy('created_at', 'desc')->get();
        return view('admin.temporada_nueva', compact('temporadas'));
    }

    /**
     * ============================================================
     * Muestra detalle completo de una temporada
     * ============================================================
     */
    public function show($id)
    {
        $temporada = Temporada::with('equipos', 'fechas.partidos')->findOrFail($id);
        $equipos = Equipo::orderBy('NombreEquipos')->get();

        return view('admin.temporada_detalle', compact('temporada', 'equipos'));
    }

    /**
     * ============================================================
     * Muestra formulario de edición de temporada
     * ============================================================
     */
    public function edit($id)
    {
        $temporada = Temporada::with('equipos')->findOrFail($id);
        $equipos = Equipo::orderBy('NombreEquipos')->get();
        $temporadas = Temporada::orderBy('created_at', 'desc')->get();

        return view('admin.temporada_nueva', compact('temporada', 'equipos', 'temporadas'));
    }

    /**
     * ============================================================
     * Actualiza nombre y equipos de una temporada
     * ============================================================
     */
    public function update(Request $request, $id)
    {
        $temporada = Temporada::findOrFail($id);

        $request->validate([
            'nombretemporada' => 'required|string|max:255|unique:temporadas,NombreTemporada,' . $id,
            'equipos' => 'required|array|min:1',
            'equipos.*' => 'exists:equipos,id',
        ], [
            'nombretemporada.required' => 'El nombre de la temporada es obligatorio.',
            'nombretemporada.unique' => 'Ya existe otra temporada con ese nombre.',
            'equipos.required' => 'Debes seleccionar al menos un equipo.',
            'equipos.*.exists' => 'Uno de los equipos seleccionados no es válido.',
        ]);

        try {
            DB::beginTransaction();

            // Actualizar nombre
            $temporada->update([
                'NombreTemporada' => $request->nombretemporada,
            ]);

            // Actualizar equipos asociados
            $temporada->equipos()->sync($request->equipos);

            DB::commit();

            return redirect()
                ->route('temporada.create')
                ->with('success', 'Temporada actualizada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Error al actualizar la temporada: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * Elimina temporada (AJAX o normal)
     * ============================================================
     */
    public function destroy(Request $request, $id)
    {
        $temporada = Temporada::findOrFail($id);

        try {
            DB::beginTransaction();

            // Desasociar equipos primero
            $temporada->equipos()->detach();

            // Eliminar la temporada (fechas y partidos por cascada)
            $temporada->delete();

            DB::commit();

            // ✅ Si es AJAX (fetch), devolvemos JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'id' => (int)$id], 200);
            }

            // ✅ Si no, redirigir con mensaje normal
            return redirect()
                ->route('temporada.create')
                ->with('success', 'Temporada eliminada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la temporada: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al eliminar la temporada: ' . $e->getMessage());
        }
    }
}

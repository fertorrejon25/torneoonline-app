<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Temporada;
use App\Models\Equipo;

class TemporadaController extends Controller
{
    // Mostrar listado de temporadas y formulario para crear
    public function create()
    {
        $temporadas = Temporada::with('equipos')->orderBy('created_at', 'desc')->get();
        $equipos = Equipo::orderBy('NombreEquipos')->get();

        return view('admin.temporada_nueva', compact('temporadas', 'equipos'));
    }

    // Guardar nueva temporada con equipos
    public function store(Request $request)
    {
        $request->validate([
            'nombretemporada' => 'required|string|max:255|unique:temporadas,NombreTemporada',
            'equipos' => 'required|array|min:1',
            'equipos.*' => 'exists:equipos,id'
        ], [
            'nombretemporada.required' => 'El nombre de la temporada es obligatorio.',
            'nombretemporada.unique' => 'Ya existe una temporada con ese nombre.',
            'equipos.required' => 'Debes seleccionar al menos un equipo.',
            'equipos.*.exists' => 'Uno de los equipos seleccionados no es válido.'
        ]);

        try {
            // Crear la temporada
            $temporada = Temporada::create([
                'NombreTemporada' => $request->nombretemporada
            ]);

            // Asociar equipos a la temporada
            $temporada->equipos()->attach($request->equipos);

            return redirect()->back()->with('success', 'Temporada creada correctamente con ' . count($request->equipos) . ' equipo(s).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar la temporada: ' . $e->getMessage());
        }
    }

    // Mostrar listado de temporadas
    public function index()
    {
        $temporadas = Temporada::with('equipos')->orderBy('created_at', 'desc')->get();
        return view('admin.temporada_nueva', compact('temporadas'));
    }

    // Mostrar detalle de temporada con opción de editar
    public function show($id)
    {
        $temporada = Temporada::with('equipos', 'fechas.partidos')->findOrFail($id);
        $equipos = Equipo::orderBy('NombreEquipos')->get();
        
        return view('admin.temporada_detalle', compact('temporada', 'equipos'));
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $temporada = Temporada::with('equipos')->findOrFail($id);
        $equipos = Equipo::orderBy('NombreEquipos')->get();
        $temporadas = Temporada::orderBy('created_at', 'desc')->get();

        return view('admin.temporada_nueva', compact('temporada', 'equipos', 'temporadas'));
    }

    // Actualizar temporada y sus equipos
    public function update(Request $request, $id)
    {
        $temporada = Temporada::findOrFail($id);

        $request->validate([
            'nombretemporada' => 'required|string|max:255|unique:temporadas,NombreTemporada,' . $id,
            'equipos' => 'required|array|min:1',
            'equipos.*' => 'exists:equipos,id'
        ], [
            'nombretemporada.required' => 'El nombre de la temporada es obligatorio.',
            'nombretemporada.unique' => 'Ya existe otra temporada con ese nombre.',
            'equipos.required' => 'Debes seleccionar al menos un equipo.',
            'equipos.*.exists' => 'Uno de los equipos seleccionados no es válido.'
        ]);

        try {
            // Actualizar nombre
            $temporada->update([
                'NombreTemporada' => $request->nombretemporada
            ]);

            // Actualizar equipos (sync reemplaza la relación completa)
            $temporada->equipos()->sync($request->equipos);

            return redirect()->route('temporada.show', $temporada->id)
                           ->with('success', 'Temporada actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar la temporada: ' . $e->getMessage());
        }
    }

    // Eliminar temporada
    public function destroy($id)
    {
        $temporada = Temporada::findOrFail($id);

        try {
            // Primero desasociar los equipos
            $temporada->equipos()->detach();
            
            // Luego eliminar la temporada (las fechas y partidos se eliminarán por cascada)
            $temporada->delete();

            return redirect()->route('temporada.create')
                           ->with('success', 'Temporada eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar la temporada: ' . $e->getMessage());
        }
    }
}
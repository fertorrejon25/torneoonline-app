<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;

class EquipoController extends Controller
{
    public function create()
    {
        $equipos = Equipo::orderBy('NombreEquipos')->get();
        return view('admin.ranking', compact('equipos'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:equipos,NombreEquipos',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp,webm|max:2048'
        ], [
            'nombre.unique' => 'El equipo ya existe en el sistema.',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('equipos', 'public');
        }

        Equipo::create([
            'NombreEquipos' => $request->nombre,
            'FotoEquipo' => $fotoPath,
        ]);

        return back()->with('success', 'Equipo guardado correctamente.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Fecha;
use App\Models\Temporada;
use Illuminate\Http\Request;

class FechaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'temporada_id' => 'required|exists:temporadas,id',
            'nombre' => 'required|string|max:255',
            'dia' => 'nullable|date',
        ]);

        Fecha::create($request->only('temporada_id','nombre','dia'));

        return back()->with('success', 'Fecha creada correctamente.');
    }
}



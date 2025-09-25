<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Temporada;

class TemporadaController extends Controller
{
    public function create()
    {
        return view('admin.temporada_nueva'); // Muestra el formulario
    }

    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'nombretemporada' => 'required|string|max:255',
        ]);
        // Insertar en la base de datos
        DB::table('temporadas')->insert([
            'NombreTemporada' => $request->input('nombretemporada'),
        ]);
        //
        Temporada::create([
            'nombretemporada' => $request->nombretemporada
        ]);


        // Redireccionar con mensaje
        return redirect()->back()->with('success', 'Temporada guardada correctamente.');
    }
    //**para en admin temporada mostrar las temp cargada */
    public function index()
    {
        $temporadas = Temporada::orderBy('created_at', 'desc')->get();
        return view('admin.temporada_nueva', compact('temporadas'));
    }
   
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Temporada;

class TemporadaController extends Controller
{
   

    public function create()
    {
        // Traer todas las temporadas para mostrarlas en la vista
        $temporadas = \App\Models\Temporada::orderBy('created_at','desc')->get();

        return view('admin.temporada_nueva', compact('temporadas'));
    }

    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'nombretemporada' => 'required|string|max:255',
        ]);
        // Insertar en la base de datos
        
        Temporada::create([
            'NombreTemporada' => $request->nombretemporada
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
    //****************para la carga de temporada****************** */
    public function show($id)
    {
        // Cargamos la temporada con sus fechas y partidos
        $temporada = Temporada::with('fechas.partidos')->findOrFail($id);

        $equipos = \App\Models\Equipo::orderBy('NombreEquipos')->get();

        return view('admin.temporada_detalle', compact('temporada', 'equipos'));
    }
   
}

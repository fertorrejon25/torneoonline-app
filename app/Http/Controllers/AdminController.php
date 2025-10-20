<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Temporada;
use App\Models\Equipo;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // Obtenemos la sección desde el parámetro GET (?section=)
        $section = $request->input('section');

        // Validamos que la sección sea una de las permitidas
        $validSections = [
            'jugadores',
            'equipos',
            'temporada',
            'resultados',
            'ranking',
            'temporadacargadas'
        ];

        if (!in_array($section, $validSections)) {
            $section = null; // Si no es válida, se muestra la pantalla principal
        }

        // Inicializamos el array
        $temporadas = [];
        $equipos = [];

        // Si la sección es "temporadacargadas", traemos las temporadas ordenadas
        if ($section === 'temporadacargadas') {
            $temporadas = Temporada::orderBy('created_at', 'desc')->get();
        }

        // Si la sección es "temporada", traemos temporadas y equipos
        if ($section === 'temporada') {
            $temporadas = Temporada::with('equipos')->orderBy('created_at', 'desc')->get();
            $equipos = Equipo::orderBy('NombreEquipos')->get();
        }

        // Retornamos la vista con los datos
        return view('admin.dashboard', compact('section', 'temporadas', 'equipos'));
    }
}
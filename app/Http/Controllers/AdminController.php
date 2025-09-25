<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Temporada; // 👈 Importar el modelo

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $section = $request->input('section');
        $temporadas = [];

        // Si la sección es "administrartemporada", traemos las temporadas
        if ($section === 'temporadacargadas') {
            $temporadas = Temporada::all();
        }

        return view('admin.dashboard', compact('section', 'temporadas'));
    }
}


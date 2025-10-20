<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResultadoController extends Controller
{
    public function index()
    {
        // Más adelante podés traer resultados reales de la BD
        return view('admin.ranking'); // Usa la misma vista de ranking
    }
}

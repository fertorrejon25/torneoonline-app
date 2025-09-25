<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Temporada; // <-- agregar esto

class temporacargadas extends Controller
{
    public function dashboard($section = null)
    {
        $temporadas = collect(); // colección vacía por defecto

        if ($section === 'temporada') {
            // Trae todas las temporadas (las más nuevas primero)
            $temporadas = Temporada::orderBy('created_at', 'desc')->get();
        }

        return view('temporadacargadas.dashboard', compact('section', 'temporadas'));
    }
}
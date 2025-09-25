<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Equipo;
use App\Models\Jugador;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Jugador con su equipo
        $jugador = Jugador::where('user_jugadores', $user->id)->first();
        $equipo = $jugador ? Equipo::find($jugador->equipo_id) : null;

        $datosJugador = [
            'dni' => $user->dni,
            'nombre' => $user->name,
            'pj' => $jugador->partidos_jugados ?? 0,
            'goles' => $jugador->goles ?? 0,
            'asistencias' => $jugador->asistencias ?? 0,
            'foto_jugador' => $jugador->foto_jugador ?? null,
        ];

        $datosEquipo = [
            'nombre' => $equipo?->NombreEquipos ?? 'Sin equipo',
            'id' => $equipo?->id,
        ];

        $mediaGoleadora = $datosJugador['pj'] > 0
            ? ($datosJugador['goles'] / $datosJugador['pj'])
            : 0;

        return view('user.dashboard', [
            'equipo' => $datosEquipo,
            'jugador' => $datosJugador,
            'mediaGoleadora' => $mediaGoleadora,
            "foto_jugador" => $datosJugador['foto_jugador'] ?? null
        ]);
    }
}
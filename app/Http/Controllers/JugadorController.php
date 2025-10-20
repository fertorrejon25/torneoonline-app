<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class JugadorController extends Controller
{
    public function create()
    {
        $equipos = Equipo::orderBy('NombreEquipos')->get();

        return view('admin.altajugadores', compact('equipos'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:50|unique:users,dni',
            'equipo_id' => 'required|exists:equipos,id',
            'mail' => 'required|email|unique:users,email',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'dni.unique'   => 'El DNI ya está registrado.',
            'mail.unique'  => 'El mail ya está registrado.',
            'dni.digits'   => 'El DNI debe tener exactamente 8 números.',
        ]);

        // 1) Crear el user
        $user = User::create([
            'name' => $request->nombre,
            'dni' => $request->dni,
            'email' => $request->mail,
            'password' => Hash::make('password123'), // 👈 acá se encripta
            'role' => 'user',
        ]);

        // 2) Guardar foto (si hay)
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('jugadores', 'public');
        }

        // 3) Crear el jugador enlazado
        Jugador::create([
            'equipo_id' => $request->equipo_id,
            'user_jugadores' => $user->id,  
            'partidos_jugados' => 0,
            'goles' => 0,
            'asistencias' => 0,
            'foto_jugador' => $fotoPath,

        ]);

        return back()->with('success', 'Jugador creado correctamente');
    }
}


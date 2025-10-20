<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Si ya hay una sesión activa, cerrarla

        // Validación del formulario
        $request->validate([
            'dni' => 'required|string',
            'password' => 'required|string',
        ]);

        // Intento de autenticación
        if (Auth::attempt(['dni' => $request->dni, 'password' => $request->password])) {
            $request->session()->regenerate();

            return redirect()->route('redirigir.por.rol');
        }

        // Si falló el login
        return back()->withErrors([
            'dni' => 'El DNI o la contraseña son incorrectos.',
        ]);
    }

    /**regenera la sesion */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

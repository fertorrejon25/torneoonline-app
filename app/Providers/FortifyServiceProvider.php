<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Login con campo DNI
        Fortify::authenticateUsing(function (Request $request) {
            $user = \App\Models\User::where('dni', $request->dni)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            // Lanzar error de validación para que Laravel lo muestre
            throw ValidationException::withMessages([
                'dni' => ['DNI o contraseña incorrectos.'],
            ]);
        });
    }
}

<?php

namespace App\Providers;

use App\Models\Equipo;   // 👈 ESTA LÍNEA FALTABA
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Inyectar $equipos en la vista de alta de jugadores
        View::composer('admin.altajugadores', function ($view) {
            $view->with('equipos', Equipo::orderBy('NombreEquipos')->get());
        });
    }
}

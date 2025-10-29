<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\JugadorController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TemporadaController;
use App\Http\Controllers\PartidosController;
use App\Http\Controllers\FechaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RankingController;

// Ruta principal - Login
Route::get('/', function () {
    return view('auth.login');
});

// ==========================================
// RUTAS PÚBLICAS
// ==========================================
Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');
Route::get('/ranking/{temporada}', [RankingController::class, 'show'])->name('ranking.show');

// ==========================================
// RUTAS DE AUTENTICACIÓN
// ==========================================
Route::middleware('auth')->group(function () {
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Redirección por rol
    Route::get('/redirigir-por-rol', function () {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    })->name('redirigir.por.rol');

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ==========================================
// RUTAS DE USUARIO NORMAL
// ==========================================
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
});

// ==========================================
// RUTAS DE ADMINISTRADOR
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    // Dashboard y estadísticas
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/maximos-goleadores', [AdminController::class, 'maximosGoleadores'])->name('admin.maximos_goleadores');
    Route::get('/maximos-asistentes', [AdminController::class, 'maximosAsistentes'])->name('admin.maximos_asistentes');
    Route::get('/ranking', [RankingController::class, 'index'])->name('admin.ranking');
    Route::get('/temporadas', [AdminController::class, 'administrarTemporada'])->name('admin.temporadas');

    // Temporadas
    Route::get('/temporada/nueva', [TemporadaController::class, 'create'])->name('temporada.create');
    Route::post('/temporada/nueva', [TemporadaController::class, 'store'])->name('temporada.store');
    Route::post('/temporada/store', [AdminController::class, 'storeTemporadaDesdeDashboard'])->name('admin.temporada.store');
    Route::get('/temporada/{temporada}', [TemporadaController::class, 'show'])->name('admin.temporada.show');
    Route::resource('temporada', TemporadaController::class)->except(['create', 'store', 'show']);

    // Jugadores
    Route::get('/jugadores/crear', [JugadorController::class, 'create'])->name('jugadores.create');
    Route::post('/jugadores', [JugadorController::class, 'store'])->name('jugadores.store');
    Route::resource('jugadores', JugadorController::class);

    // Equipos
    Route::post('/equipos', [EquipoController::class, 'store'])->name('equipos.store');

    // Fechas
    Route::post('/fechas/store', [FechaController::class, 'store'])->name('fechas.store');
    Route::delete('/fechas/{id}', [FechaController::class, 'destroy'])->name('fechas.destroy');

    // ==========================================
    // RUTAS DE PARTIDOS - ORDEN CORRECTO
    // ==========================================
    Route::prefix('partidos')->group(function () {
        // Crear partido
        Route::post('/store', [PartidosController::class, 'store'])->name('partidos.store');

        // Edición detallada - ESTA ES LA RUTA QUE DEBES USAR PARA VER EL FORMULARIO
        Route::get('/{partido}/edit_detailed', [PartidosController::class, 'editDetailed'])
            ->name('admin.partidos.edit_detailed');

        // Actualización detallada - ESTA RUTA SOLO ACEPTA PUT PARA ENVIAR EL FORMULARIO
        Route::put('/{partido}/update_detailed', [PartidosController::class, 'updateDetailed'])
            ->name('admin.partidos.update_detailed');

        // Eliminar partido
        Route::delete('/{partido}', [PartidosController::class, 'destroy'])
            ->name('partidos.destroy');

        // Limpiar partidos
        Route::delete('/limpiar/{temporadaId}', [PartidosController::class, 'limpiarPartidos'])
            ->name('partidos.limpiar');
    });

    // ==========================================
    // OTRAS RUTAS ESPECÍFICAS (agrega aquí cualquier otra ruta específica)
    // ==========================================

    // ⚠️ RUTA COMODÍN - DEBE SER SIEMPRE LA ÚLTIMA
    Route::get('/{section?}', [AdminController::class, 'dashboard'])->name('admin.catchall');
});

require __DIR__ . '/auth.php';
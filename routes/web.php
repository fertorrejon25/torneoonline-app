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
        return $user->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    })->name('redirigir.por.rol');

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ==========================================
// RUTAS DE USUARIO NORMAL (JUGADOR)
// ==========================================
Route::middleware(['auth', 'role:user'])->prefix('user')->group(function () {

    // Dashboard principal con parámetro section
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

    // Atajos semánticos (opcionales)
    Route::get('/mi-carnet', fn() => redirect()->route('user.dashboard', ['section' => 'mi-carnet']))->name('user.mi-carnet');
    Route::get('/historico', fn() => redirect()->route('user.dashboard', ['section' => 'historico']))->name('user.historico');
    Route::get('/temporada-actual', fn() => redirect()->route('user.dashboard', ['section' => 'temporada-actual']))->name('user.temporada-actual');
    Route::get('/ranking-historico', fn() => redirect()->route('user.dashboard', ['section' => 'ranking-historico']))->name('user.ranking-historico');

    // Máximos (usuario) - vistas dedicadas
    Route::get('/maximos-goleadores', [UserController::class, 'maximosGoleadores'])
        ->name('user.maximos_goleadores');
    Route::get('/maximos-asistentes', [UserController::class, 'maximosAsistentes'])
        ->name('user.maximos_asistentes');


    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/ranking-historico', fn() => redirect()->route('user.dashboard', ['section' => 'ranking-historico']))->name('user.ranking-historico');

    Route::get('/maximos-goleadores', [UserController::class, 'maximosGoleadores'])->name('user.maximos_goleadores');
    Route::get('/maximos-asistentes', [UserController::class, 'maximosAsistentes'])->name('user.maximos_asistentes');

    Route::get('/maximos-goleadores-historico', [UserController::class, 'maximosGoleadoresHistorico'])->name('user.maximos_goleadores_historico');
    Route::get('/maximos-asistentes-historico', [UserController::class, 'maximosAsistentesHistorico'])->name('user.maximos_asistentes_historico');
}); // <-- CIERRE DEL GRUPO DE USUARIO

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

    // Partidos (orden correcto)
    Route::prefix('partidos')->group(function () {
        Route::post('/store', [PartidosController::class, 'store'])->name('partidos.store');

        Route::get('/{partido}/edit_detailed', [PartidosController::class, 'editDetailed'])
            ->name('admin.partidos.edit_detailed');

        Route::put('/{partido}/update_detailed', [PartidosController::class, 'updateDetailed'])
            ->name('admin.partidos.update_detailed');

        Route::delete('/{partido}', [PartidosController::class, 'destroy'])
            ->name('partidos.destroy');

        Route::delete('/limpiar/{temporadaId}', [PartidosController::class, 'limpiarPartidos'])
            ->name('partidos.limpiar');
    });

    // ⚠️ RUTA COMODÍN - DEBE SER SIEMPRE LA ÚLTIMA
    Route::get('/{section?}', [AdminController::class, 'dashboard'])->name('admin.catchall');
});

require __DIR__ . '/auth.php';

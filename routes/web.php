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
use App\Http\Controllers\ResultadoController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
/*** ruta y vista para el panel de admin y user*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::resource('jugadores', JugadorController::class);

    // Rutas para crear nueva temporada
    Route::get('/admin/temporada/nueva', [TemporadaController::class, 'create'])->name('temporada.create');
    Route::post('/admin/temporada/nueva', [TemporadaController::class, 'store'])->name('temporada.store');
    // para mostras las temporadas creadas
    Route::get('/admin/{section?}', [AdminController::class, 'dashboard'])->name('temporadacargadas.dashboard');
    //para la carga de temporada
    Route::get('/admin/temporada/{id}', [TemporadaController::class, 'show'])->name('admin.temporada.show')->middleware('auth');

});
//para la vista de las temporadas 
Route::resource('temporada', TemporadaController::class);


// Otras vistas del menú
// Route::get('/admin/ranking/', function () {
// return view('admin.ranking_dashboard'); // creás este archivo si lo necesitás
// })->name('ranking.dashboard');
// /});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
});
/*********para la tabla equipos */


Route::post('/equipos', [EquipoController::class, 'store'])->name('equipos.store');
/********************************************************************************************** */
Route::get('/redirigir-por-rol', function () {
    $user = Auth::user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('user.dashboard');
})->name('redirigir.por.rol');

/* esto deve enviar un post ** */
/* Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout'); */
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/admin/jugadores/crear', [JugadorController::class, 'create'])->name('jugadores.create');
Route::post('/admin/jugadores', [JugadorController::class, 'store'])->name('jugadores.store');
/****** para armar fixture de temporadas*************************************/
Route::prefix('temporadas')->group(function () {
    Route::get('{id}/fixture', [PartidosController::class, 'index'])->name('fixture.index');
    Route::post('{id}/fixture/generar', [PartidosController::class, 'generar'])->name('fixture.generar');
    Route::post('{id}/fixture/fechas', [PartidosController::class, 'updateFechas'])->name('fixture.updateFechas');
});

// ********para fechas*************//
Route::post('/fechas/store', [FechaController::class, 'store'])
    ->name('fechas.store');

// ********para partidos ************/
Route::post('/partidos/store', [PartidosController::class, 'store'])
    ->name('partidos.store');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::resource('jugadores', JugadorController::class);

    // Rutas para crear nueva temporada
    Route::get('/admin/temporada/nueva', [TemporadaController::class, 'create'])->name('temporada.create');
    Route::post('/admin/temporada/nueva', [TemporadaController::class, 'store'])->name('temporada.store');

    // Para mostrar las temporadas creadas
    Route::get('/admin/{section?}', [AdminController::class, 'dashboard'])->name('temporadacargadas.dashboard');

    // Para ver una temporada específica
    Route::get('/admin/temporada/{id}', [TemporadaController::class, 'show'])
        ->name('admin.temporada.show')
        ->middleware('auth');

    // 🔹 NUEVA RUTA PARA RESULTADOS
    Route::get('/admin/resultados', [ResultadoController::class, 'index'])->name('resultados.index');
});

require __DIR__.'/auth.php';


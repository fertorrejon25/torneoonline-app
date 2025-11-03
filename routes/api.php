<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Equipo;
use App\Http\Controllers\Api\EquipoController;
use App\Http\Controllers\Api\RankingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

/**** para la api equipos y ranking */
Route::get('/equipos', [EquipoController::class, 'index']);



Route::get('/ranking', [RankingController::class, 'index']);
Route::get('/ranking/{temporadaId}', [RankingController::class, 'show']);
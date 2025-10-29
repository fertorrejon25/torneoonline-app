<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Equipo;
use App\Http\Controllers\Api\EquipoController;
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


/***api para mostrar el escudo de los equipos */
/*Route::get('/api/equipos', function () {
    return response()->json(Equipo::all());
});*/



Route::get('/equipos', [EquipoController::class, 'index']);
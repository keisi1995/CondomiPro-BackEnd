<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('usuario', [App\Http\Controllers\UsuarioController::class, 'store']);
Route::post('autenticar', [App\Http\Controllers\AutenticarController::class, 'autenticar']);
Route::post('verificar_token', [App\Http\Controllers\AutenticarController::class, 'verificar_token']);
Route::post('refresh', [App\Http\Controllers\AutenticarController::class, 'refresh']);
Route::post('logout', [App\Http\Controllers\AutenticarController::class, 'logout']);

Route::post('new_token', [App\Http\Controllers\AutenticarController::class, 'new_token']);
Route::post('decode_token', [App\Http\Controllers\AutenticarController::class, 'decode_token']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('usuario', [App\Http\Controllers\UsuarioController::class, 'index']);
    Route::get('departamento', [App\Http\Controllers\DepartamentoController::class, 'index']);
    Route::get('provincia/{id_departamento}', [App\Http\Controllers\ProvinciaController::class, 'index']);
    Route::get('distrito/{id_provincia}/{id_departamento}', [App\Http\Controllers\DistritoController::class, 'index']);
    Route::get('tipodocumento', [App\Http\Controllers\TipoDocumentoController::class, 'index']);
    Route::get('tipopersona', [App\Http\Controllers\TipoPersonaController::class, 'index']);
    Route::apiResource('socio', App\Http\Controllers\SocioController::class);
    Route::apiResource('persona', App\Http\Controllers\PersonaController::class);
});
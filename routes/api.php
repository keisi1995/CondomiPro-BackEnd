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
    // Route::get('usuario', [App\Http\Controllers\UsuarioController::class, 'index']);
    Route::get('departamento', [App\Http\Controllers\DepartamentoController::class, 'index']);
    Route::get('provincia/{id_departamento}', [App\Http\Controllers\ProvinciaController::class, 'index']);
    Route::get('distrito/{id_provincia}/{id_departamento}', [App\Http\Controllers\DistritoController::class, 'index']);        
   
    Route::apiResource('usuarios', App\Http\Controllers\UsuarioController::class);
    Route::get('usuarios/{id_usuario}/permisos', [App\Http\Controllers\PermisoController::class, 'permissionByUser']);
    
    Route::apiResource('socio', App\Http\Controllers\SocioController::class);
    Route::apiResource('persona', App\Http\Controllers\PersonaController::class);    
    Route::apiResource('tipoconcepto', App\Http\Controllers\TipoConceptoController::class);
    Route::apiResource('concepto', App\Http\Controllers\ConceptoController::class);
    
    Route::apiResource('edificacion', App\Http\Controllers\EdificacionController::class);
    Route::apiResource('propiedad', App\Http\Controllers\PropiedadController::class);
    Route::get('propiedad/list_propiedad/{id_edificacion}', [App\Http\Controllers\PropiedadController::class, 'listPropiedad']);
    
    Route::apiResource('parentesco', App\Http\Controllers\ParentescoController::class);
    Route::apiResource('ddjj', App\Http\Controllers\DeclaracionJuradaController::class);

    // Detalle Declaracion Jurada
    Route::apiResource('ddjjdetalle', App\Http\Controllers\DeclaracionJuradaDetalleController::class);
    Route::get('ddjjdetalle/{id_concepto}/ctaporcobrar', [App\Http\Controllers\DeclaracionJuradaDetalleController::class, 'listCtaporCobrar']);
    // fin

    
    Route::apiResource('comprobante', App\Http\Controllers\ComprobanteController::class);
    Route::apiResource('servicio', App\Http\Controllers\ServicioController::class);
    Route::apiResource('ctaporcobrar', App\Http\Controllers\CtaPorCobrarController::class);
    Route::apiResource('detallecomprobante', App\Http\Controllers\DetalleComprobanteController::class);
    Route::apiResource('inquilino', App\Http\Controllers\InquilinoController::class);
    Route::apiResource('motivo', App\Http\Controllers\MotivoController::class);
    Route::apiResource('visitante', App\Http\Controllers\VisitanteController::class);
    Route::apiResource('visita', App\Http\Controllers\VisitaController::class);
    Route::apiResource('recepcionvisita', App\Http\Controllers\RecepcionVisitaController::class);
    Route::apiResource('trackingvisita', App\Http\Controllers\TrackingVisitaController::class);
    Route::apiResource('modulo', App\Http\Controllers\ModuloController::class);
    Route::apiResource('menu', App\Http\Controllers\MenuController::class);
    Route::get('modulo_tree', [App\Http\Controllers\ModuloController::class, 'modulo_tree']);

    Route::apiResource('tipodocumento', App\Http\Controllers\TipoDocumentoController::class);
    Route::apiResource('tipopersona', App\Http\Controllers\TipoPersonaController::class);
    Route::apiResource('tipousuario', App\Http\Controllers\TipoUsuarioController::class);
    Route::apiResource('tipocomprobante', App\Http\Controllers\TipoComprobanteController::class);
});
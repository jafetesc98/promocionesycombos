<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [App\Http\Controllers\UserController::class, 'apiLogin']);

Route::get('/art', [App\Http\Controllers\ArticuloController::class, 'getPreciosXSucursal']);

Route::get('/prov', [App\Http\Controllers\ArticuloController::class, 'getProveedores']);

Route::get('/acuerdos', [App\Http\Controllers\ArticuloController::class, 'getAcuerdos']);

Route::post('/crea-prm', [App\Http\Controllers\PromocionController::class, 'crearPrePromocion']);

Route::post('/upd-prm', [App\Http\Controllers\PromocionController::class, 'editarPrePromocion']);

Route::post('/delete-prm', [App\Http\Controllers\PromocionController::class, 'softDeletePromocion']);

Route::get('/prm-usr', [App\Http\Controllers\PromocionController::class, 'getPromocionesXComprador']);

Route::get('/prom', [App\Http\Controllers\PromocionController::class, 'getAllPromociones']);


Route::get('/promocion', [App\Http\Controllers\PromocionController::class, 'getDetallePromocion']);

Route::get('/prom-aut', [App\Http\Controllers\PromocionController::class, 'getPromAut']);


//Autorizar
Route::get('/promocion-aut', [App\Http\Controllers\PromocionController::class, 'getDetallePromocionAut']);

Route::post('/aut-prom', [App\Http\Controllers\PromocionController::class, 'creaPromoMks']);


Route::post('/rechazar-prom', [App\Http\Controllers\PromocionController::class, 'denegarProm']);

//Autorizadas
Route::get('/autorizadas', [App\Http\Controllers\PromocionController::class, 'getAutorizadas']);
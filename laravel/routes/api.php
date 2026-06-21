<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ChaquetaApiController;
use App\Http\Controllers\Api\CategoriaApiController;
use App\Http\Controllers\Api\MaterialApiController;
use App\Http\Controllers\Api\StockApiController;
use App\Http\Controllers\Api\UsuarioApiController;
use App\Http\Controllers\Api\ReporteApiController;

/*
|--------------------------------------------------------------------------
| API Authentication Routes
|--------------------------------------------------------------------------
| Públicas - sin autenticación requerida
*/
Route::post('/auth/register', [AuthApiController::class, 'register']);
Route::post('/auth/login', [AuthApiController::class, 'login']);

/*
|--------------------------------------------------------------------------
| API Protected Routes - Require Authentication (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);
    Route::get('/auth/profile', [AuthApiController::class, 'profile']);
    Route::put('/auth/profile', [AuthApiController::class, 'updateProfile']);

    // Chaquetas CRUD (Resource routes)
    Route::apiResource('chaquetas', ChaquetaApiController::class);

    // Categorías CRUD
    Route::apiResource('categorias', CategoriaApiController::class);

    // Materiales CRUD
    Route::apiResource('materiales', MaterialApiController::class);

    // Stock CRUD
    Route::apiResource('stock', StockApiController::class);

    // Usuarios CRUD (solo Admin)
    Route::apiResource('usuarios', UsuarioApiController::class);

    // Reportes
    Route::get('/reportes/ventas', [ReporteApiController::class, 'ventasPorMes']);
    Route::get('/reportes/stock', [ReporteApiController::class, 'stockDisponible']);
    Route::get('/reportes/usuarios', [ReporteApiController::class, 'usuariosPorRol']);
    Route::get('/reportes/productos-mas-vendidos', [ReporteApiController::class, 'productosMasVendidos']);
    Route::get('/reportes/exportar/chaquetas', [ReporteApiController::class, 'exportarChaquetas']);
});
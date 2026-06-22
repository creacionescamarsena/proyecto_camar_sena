<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChaquetaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MaterialController;

Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/materiales', [MaterialController::class, 'index']);
Route::post('/chaquetas', [ChaquetaController::class, 'store']);
Route::get('/chaquetas', [ChaquetaController::class, 'index']);
<?php

use Illuminate\Support\Facades\Response;

use App\Http\Controllers\AdminCategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AdminProductoController;
use Illuminate\Support\Facades\Route;

Route::resource('productos', ProductoController::class);

Route::get('/', function () {
    return view('welcome');
});

Route::resource('categorias', AdminCategoriaController::class);

Route::resource('productos', AdminProductoController::class);
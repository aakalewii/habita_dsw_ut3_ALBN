<?php

use Illuminate\Support\Facades\Response;

use App\Http\Controllers\AdminCategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AdminProductoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductoController::class, 'galeria'])->name('productos.galeria');
Route::get('/catalogo/{producto}', [ProductoController::class, 'show'])
    ->name('user.productos.show');

Route::resource('categorias', AdminCategoriaController::class);

Route::resource('productos', AdminProductoController::class);

<?php

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AdminCategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AdminProductoController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Login de usuario
Route::get('/', function(){
    if(!Auth::check()){
        return redirect()->route('login');
    } else {
        return redirect()->route('productos.galeria');
    }
});

// Login de usuario
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Registro
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/', [ProductoController::class, 'galeria'])->name('productos.galeria');
Route::get('/catalogo/{producto}', [ProductoController::class, 'show'])->name('user.productos.show');

Route::resource('categorias', AdminCategoriaController::class);

Route::resource('productos', AdminProductoController::class);

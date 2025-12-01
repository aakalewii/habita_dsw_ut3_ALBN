<?php

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AdminCategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AdminProductoController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Galería de productos (acceso público sin login)
Route::get('/', [ProductoController::class, 'galeria'])->name('productos.galeria');
Route::get('/catalogo/{producto}', [ProductoController::class, 'show'])->name('user.productos.show');

// Login de usuario
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Registro
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', function () {
        $user = Auth::user();

        if (!$user || $user->role?->nombre !== 'Administrador') {
            abort(403);
        }

        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('categorias/buscar', [AdminCategoriaController::class, 'buscar'])
        ->name('categorias.buscar');

    Route::get('productos/buscar', [AdminProductoController::class, 'buscar'])
        ->name('productos.buscar');
    
    Route::resource('categorias', AdminCategoriaController::class);
    Route::resource('productos', AdminProductoController::class);
});

// Rutas del Carrito (Requieren autenticación)
use App\Http\Controllers\CarritoController;

Route::middleware(['auth'])->group(function () {
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/add/{producto}', [CarritoController::class, 'add'])->name('carrito.add');
    Route::put('/carrito/update/{item}', [CarritoController::class, 'update'])->name('carrito.update');
    Route::delete('/carrito/remove/{item}', [CarritoController::class, 'remove'])->name('carrito.remove');
    Route::post('/carrito/clear', [CarritoController::class, 'clear'])->name('carrito.clear');
    Route::post('/carrito/comprar', [CarritoController::class, 'comprar'])->name('carrito.comprar');
});


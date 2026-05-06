<?php

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AdminCategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AdminProductoController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\PreferenciasController;

// Galería de productos (acceso público sin login)
Route::get('/', [ProductoController::class, 'galeria'])->name('productos.galeria');
Route::get('/catalogo/{producto}', [ProductoController::class, 'show'])->name('user.productos.show');

// Login de usuario
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Registro
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
// Ver usuario
Route::get('/perfil', [AuthController::class, 'perfil'])->name('perfil.index');
// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// preferencias
Route::get('/preferencias', [PreferenciasController::class, 'edit'])->name('preferencias.edit');
Route::post('/preferencias', [PreferenciasController::class, 'update'])->name('preferencias.update');


// Rutas protegidas (requieren autenticación)
Route::middleware(['auth'])->group(function () {
    // Ruta que muestra el panel del administrador
    Route::get('/admin', function () {
        $user = Auth::user();

        if (!$user || $user->role?->nombre !== 'Administrador') {
            abort(403);
        }

        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Ruta que busca categorias segun el texto indicado
    Route::get('categorias/buscar', [AdminCategoriaController::class, 'buscar'])
        ->name('categorias.buscar');

    // Ruta que busca productos segun el texto indicado
    Route::get('productos/buscar', [AdminProductoController::class, 'buscar'])
        ->name('productos.buscar');

    // Rutas que listan, crean, editan, muestran y borran categorias
    Route::resource('categorias', AdminCategoriaController::class);
    // Rutas que listan, crean, editan, muestran y borran productos
    Route::resource('productos', AdminProductoController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/add/{producto}', [CarritoController::class, 'add'])->name('carrito.add');
    Route::put('/carrito/update/{item}', [CarritoController::class, 'update'])->name('carrito.update');
    Route::delete('/carrito/remove/{item}', [CarritoController::class, 'remove'])->name('carrito.remove');
    Route::post('/carrito/clear', [CarritoController::class, 'clear'])->name('carrito.clear');
    Route::post('/carrito/comprar', [CarritoController::class, 'comprar'])->name('carrito.comprar');
});

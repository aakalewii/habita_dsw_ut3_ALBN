<?php

use Illuminate\Support\Facades\Response;

use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::resource('productos', ProductoController::class);

Route::get('/', function () {
    return view('welcome');
});

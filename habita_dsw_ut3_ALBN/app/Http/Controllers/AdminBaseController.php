<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

abstract class AdminBaseController extends Controller
{
    protected function ensureAdmin(): void
    {
        if (!Session::get('autorizacion_usuario') || Session::get('user_rol') !== 'Administrador') {
            abort(403);
        }
    }
}

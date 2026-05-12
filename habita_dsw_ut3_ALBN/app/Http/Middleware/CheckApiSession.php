<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckApiSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::get('autorizacion_usuario')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}

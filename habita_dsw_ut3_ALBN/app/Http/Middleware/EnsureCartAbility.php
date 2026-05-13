<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * UT4: el carrito requiere sesión API y ability carrito.gestionar (cliente),
 * o permisos de administración que implican uso completo de la tienda.
 */
class EnsureCartAbility
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Session::get('autorizacion_usuario')) {
            return redirect()->route('login');
        }

        $abilities = Session::get('user_abilities', []);
        if (is_string($abilities)) {
            $decoded = json_decode($abilities, true);
            $abilities = is_array($decoded) ? $decoded : [];
        }
        if (! is_array($abilities)) {
            $abilities = [];
        }

        $rol = Session::get('user_rol');

        if ($rol === 'Administrador'
            || in_array('admin.panel', $abilities, true)
            || in_array('carrito.gestionar', $abilities, true)) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para usar el carrito (se requiere carrito.gestionar).');
    }
}

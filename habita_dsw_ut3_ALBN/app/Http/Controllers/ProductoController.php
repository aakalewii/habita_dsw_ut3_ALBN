<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Services\MuebleApiService;

class ProductoController extends Controller
{
    protected MuebleApiService $muebleApi;

    public function __construct(MuebleApiService $muebleApi)
    {
        $this->muebleApi = $muebleApi;
    }

    /**
     * Galería pública de productos.
     * Consume la API de Muebles (puerto 8002) en lugar de la BD local.
     */
    public function galeria(Request $request)
    {
        // Construir filtros para enviar a la API de Muebles
        $filtros = [];

        if ($request->filled('buscar')) {
            $filtros['buscar'] = $request->buscar;
        }
        if ($request->filled('precio_min')) {
            $filtros['precio_min'] = $request->precio_min;
        }
        if ($request->filled('precio_max')) {
            $filtros['precio_max'] = $request->precio_max;
        }
        if ($request->filled('categoria_id')) {
            $filtros['categoria'] = $request->categoria_id;
        }
        if ($request->filled('color_principal')) {
            $filtros['color'] = $request->color_principal;
        }
        if ($request->filled('orden')) {
            $filtros['orden'] = $request->orden;
        }
        if ($request->filled('page')) {
            $filtros['page'] = $request->page;
        }

        $token = Session::get('api_token');

        // Obtener muebles desde la API (devuelve un LengthAwarePaginator)
        $listaProductos = $this->muebleApi->listarMuebles($filtros, $token);

        // Obtener categorías desde la API para el filtro del formulario
        $categorias = collect($this->muebleApi->listarCategorias($token));

        // Obtener colores únicos de los muebles de la página actual
        $colores = $listaProductos->pluck('color_principal')->filter()->unique()->sort()->values();

        return view('User/principal', compact('listaProductos', 'categorias', 'colores'));
    }

    /**
     * Detalle de un producto.
     * Consume la API de Muebles para obtener un mueble específico.
     */
    public function show($id)
    {
        $token = Session::get('api_token');
        $producto = $this->muebleApi->obtenerMueble((int) $id, $token);

        if (!$producto) {
            abort(404, 'Producto no encontrado');
        }

        return view('User.show', compact('producto'));
    }
}

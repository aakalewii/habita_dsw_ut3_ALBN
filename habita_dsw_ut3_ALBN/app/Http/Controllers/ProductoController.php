<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\ApiMueblesService;

class ProductoController extends Controller
{
    protected ApiMueblesService $apiMuebles;

    public function __construct(ApiMueblesService $apiMuebles)
    {
        $this->apiMuebles = $apiMuebles;
    }

    public function galeria(Request $request)
    {
        $filtros = array_filter([
            'search'      => $request->buscar,
            'precio_min'  => $request->precio_min,
            'precio_max'  => $request->precio_max,
            'categoria_id'=> $request->categoria_id,
            'color'       => $request->color_principal,
            'sort'        => $request->orden,
            'per_page'    => Cookie::get('preferencia_paginacion', 12),
        ]);

        $baseUrl = $this->apiMuebles->baseUrl;

        $responses = Http::pool(fn ($pool) => [
            $pool->as('muebles')->get("{$baseUrl}/muebles", $filtros),
            $pool->as('categorias')->get("{$baseUrl}/categorias"),
        ]);

        if (!$responses['muebles']->successful()) {
            $listaProductos = new LengthAwarePaginator([], 0, 12, 1, ['path' => request()->url()]);
            return view('User/principal', [
                'listaProductos' => $listaProductos,
                'categorias'     => collect(),
                'colores'        => collect(),
                'error'          => 'No se pudo conectar con el servicio de muebles.',
            ]);
        }

        $json        = $responses['muebles']->json();
        $items       = collect($json['data'] ?? $json);
        $currentPage = $json['current_page'] ?? 1;
        $total       = $json['total'] ?? $items->count();
        $perPage     = $json['per_page'] ?? 12;

        $listaProductos = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $categorias = collect($responses['categorias']->successful()
            ? $this->extractData($responses['categorias'])
            : []);
        $colores = $items->pluck('color')->filter()->unique()->sort()->values();

        return view('User/principal', compact('listaProductos', 'categorias', 'colores'));
    }

    public function show(int $id)
    {
        $respuesta = $this->apiMuebles->verMueble($id);

        if (!$respuesta->successful()) {
            abort(404);
        }

        $producto = $this->extractData($respuesta);

        return view('User.show', compact('producto'));
    }
}

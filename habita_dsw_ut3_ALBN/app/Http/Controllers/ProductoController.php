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
        $perPagePref = (int) Cookie::get('preferencia_paginacion', 12);
        if ($perPagePref < 1) {
            $perPagePref = 12;
        }
        if ($perPagePref > 100) {
            $perPagePref = 100;
        }

        // Parámetros alineados con api_muebles (MuebleController@index)
        $filtros = array_filter([
            'buscar'     => $request->buscar,
            'precio_min' => $request->precio_min,
            'precio_max' => $request->precio_max,
            'categoria'  => $request->categoria_id,
            'color'      => $request->color_principal,
            'orden'      => $request->orden,
            'per_page'   => $perPagePref,
        ], static fn ($v) => $v !== null && $v !== '');

        $baseUrl = $this->apiMuebles->baseUrl;

        $responses = Http::pool(fn ($pool) => [
            $pool->as('muebles')->acceptJson()->get("{$baseUrl}/muebles", $filtros),
            $pool->as('categorias')->acceptJson()->get("{$baseUrl}/categorias"),
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

        $json  = $responses['muebles']->json() ?? [];
        $meta  = is_array($json) ? ($json['meta'] ?? []) : [];
        $items = collect(is_array($json) ? ($json['data'] ?? []) : []);

        $currentPage = (int) ($meta['current_page'] ?? $json['current_page'] ?? 1);
        $total       = (int) ($meta['total'] ?? $json['total'] ?? $items->count());
        $perPage     = (int) ($meta['per_page'] ?? $json['per_page'] ?? $perPagePref);

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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\ApiMueblesService;

class AdminProductoController extends AdminBaseController
{
    protected ApiMueblesService $apiMuebles;

    public function __construct(ApiMueblesService $apiMuebles)
    {
        $this->apiMuebles = $apiMuebles;
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $filtros = $request->filled('buscar') ? ['buscar' => $request->buscar] : [];
        $respuesta = $this->apiMuebles->listarMuebles($filtros);
        $listaProductos = collect($respuesta->successful() ? $this->extractData($respuesta) : []);

        return view('admin.productos.index', compact('listaProductos'));
    }

    public function create()
    {
        $this->ensureAdmin();

        $respuesta = $this->apiMuebles->listarCategorias();
        $listaCategorias = collect($respuesta->successful() ? $this->extractData($respuesta) : []);

        return view('admin.productos.create', compact('listaCategorias'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'required|string',
            'precio'          => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'material'        => 'required|string|max:100',
            'color'           => 'required|string|max:100',
            'imagen_principal'=> 'nullable|image|max:2048',
            'categoria_id'    => 'required|integer',
        ]);

        $imagenUrl = null;
        if ($request->hasFile('imagen_principal')) {
            $imagenUrl = asset(Storage::url(
                $request->file('imagen_principal')->store('productos', 'public')
            ));
        }

        $respuesta = $this->apiMuebles->crearMueble([
            'nombre'       => $request->nombre,
            'descripcion'  => $request->descripcion,
            'precio'       => $request->precio,
            'stock'        => $request->stock,
            'material'     => $request->material,
            'color'        => $request->color,
            'categoria_id' => $request->categoria_id,
            'imagen_url'   => $imagenUrl,
        ]);

        if (!$respuesta->successful()) {
            return back()->withErrors(['api' => 'Error al crear el mueble: ' . $respuesta->body()]);
        }

        return redirect()->route('productos.index')->with('success', 'Mueble creado correctamente.');
    }

    public function show(int $id)
    {
        $this->ensureAdmin();

        $respuesta = $this->apiMuebles->verMueble($id);
        if (!$respuesta->successful()) {
            abort(404);
        }

        $producto = $this->extractData($respuesta);
        return view('admin.productos.show', compact('producto'));
    }

    public function edit(int $id)
    {
        $this->ensureAdmin();

        $responses = Http::pool(fn ($pool) => [
            $pool->as('producto')->acceptJson()->get("{$this->apiMuebles->baseUrl}/muebles/{$id}"),
            $pool->as('categorias')->acceptJson()->get("{$this->apiMuebles->baseUrl}/categorias"),
        ]);

        if (!$responses['producto']->successful()) {
            abort(404);
        }

        $producto        = $this->extractData($responses['producto']);
        $listaCategorias = collect($responses['categorias']->successful()
            ? $this->extractData($responses['categorias'])
            : []);

        return view('admin.productos.edit', compact('producto', 'listaCategorias'));
    }

    public function update(Request $request, int $id)
    {
        $this->ensureAdmin();
        $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'required|string',
            'precio'          => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'material'        => 'required|string|max:100',
            'color'           => 'required|string|max:100',
            'imagen_principal'=> 'nullable|image|max:2048',
            'categoria_id'    => 'required|integer',
        ]);

        $datos = [
            'nombre'       => $request->nombre,
            'descripcion'  => $request->descripcion,
            'precio'       => $request->precio,
            'stock'        => $request->stock,
            'material'     => $request->material,
            'color'        => $request->color,
            'categoria_id' => $request->categoria_id,
        ];

        if ($request->hasFile('imagen_principal')) {
            $datos['imagen_url'] = asset(Storage::url(
                $request->file('imagen_principal')->store('productos', 'public')
            ));
        }

        $respuesta = $this->apiMuebles->actualizarMueble($id, $datos);

        if (!$respuesta->successful()) {
            return back()->withErrors(['api' => 'Error al actualizar el mueble: ' . $respuesta->body()]);
        }

        return redirect()->route('productos.index')->with('success', 'Mueble actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        $this->ensureAdmin();

        $respuesta = $this->apiMuebles->eliminarMueble($id);

        if (!$respuesta->successful()) {
            return back()->withErrors(['api' => 'Error al eliminar el mueble.']);
        }

        return redirect()->route('productos.index')->with('success', 'Mueble eliminado correctamente.');
    }

    public function buscar(Request $request)
    {
        return $this->index($request);
    }
}

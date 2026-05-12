<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiMueblesService;

class AdminCategoriaController extends AdminBaseController
{
    protected ApiMueblesService $apiMuebles;

    public function __construct(ApiMueblesService $apiMuebles)
    {
        $this->apiMuebles = $apiMuebles;
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $filtros = $request->filled('buscar') ? ['search' => $request->buscar] : [];
        $respuesta = $this->apiMuebles->listarCategorias($filtros);
        $listaCategorias = collect($respuesta->successful() ? $this->extractData($respuesta) : []);

        return view('admin.categorias.index', compact('listaCategorias'));
    }

    public function create()
    {
        $this->ensureAdmin();
        return view('admin.categorias.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        $respuesta = $this->apiMuebles->crearCategoria([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        if (!$respuesta->successful()) {
            return back()->withErrors(['api' => 'Error al crear la categoría: ' . $respuesta->body()]);
        }

        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }

    public function show(int $id)
    {
        $this->ensureAdmin();

        $respuesta = $this->apiMuebles->verCategoria($id);
        if (!$respuesta->successful()) {
            abort(404);
        }

        $categoria = $this->extractData($respuesta);
        return view('admin.categorias.show', compact('categoria'));
    }

    public function edit(int $id)
    {
        $this->ensureAdmin();

        $respuesta = $this->apiMuebles->verCategoria($id);
        if (!$respuesta->successful()) {
            abort(404);
        }

        $categoria = $this->extractData($respuesta);
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, int $id)
    {
        $this->ensureAdmin();
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        $respuesta = $this->apiMuebles->actualizarCategoria($id, [
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        if (!$respuesta->successful()) {
            return back()->withErrors(['api' => 'Error al actualizar la categoría: ' . $respuesta->body()]);
        }

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        $this->ensureAdmin();

        $respuesta = $this->apiMuebles->eliminarCategoria($id);

        if (!$respuesta->successful()) {
            return back()->withErrors(['api' => 'Error al eliminar la categoría.']);
        }

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }

    public function buscar(Request $request)
    {
        return $this->index($request);
    }
}

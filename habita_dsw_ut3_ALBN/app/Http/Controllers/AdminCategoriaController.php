<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Services\MuebleApiService;

class AdminCategoriaController extends Controller
{
    protected MuebleApiService $muebleApi;

    public function __construct(MuebleApiService $muebleApi)
    {
        $this->muebleApi = $muebleApi;
    }

    private function ensureAdmin(): void
    {
        if (!Auth::check() || Auth::user()->role?->nombre !== 'Administrador') {
            abort(403);
        }
    }

    private function getToken(): string
    {
        return Session::get('api_token', '');
    }

    /**
     * Muestra todas las categorías (desde la API de Muebles).
     */
    public function index(Request $request)
    {
        $this->ensureAdmin();
        $token = $this->getToken();

        $listaCategorias = collect($this->muebleApi->listarCategorias($token));

        // Filtrar por búsqueda localmente
        if ($request->filled('buscar')) {
            $buscar = strtolower($request->buscar);
            $listaCategorias = $listaCategorias->filter(function ($cat) use ($buscar) {
                return str_contains(strtolower($cat->nombre), $buscar);
            });
        }

        return view('admin.categorias.index', compact('listaCategorias'));
    }

    /**
     * Formulario para crear una categoría nueva.
     */
    public function create()
    {
        $this->ensureAdmin();
        return view('admin.categorias.create');
    }

    /**
     * Guarda una categoría enviándola a la API de Muebles.
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'nombre'      => 'required',
            'descripcion' => 'required',
        ]);

        $token = $this->getToken();
        $result = $this->muebleApi->crearCategoria([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
        ], $token);

        if ($result['status'] === 201) {
            return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
        }

        return back()->withErrors(['error' => $result['body']['message'] ?? 'Error al crear la categoría.'])->withInput();
    }

    /**
     * Muestra una categoría concreta (desde la API).
     */
    public function show(int $id)
    {
        $this->ensureAdmin();
        $token = $this->getToken();
        $categoria = $this->muebleApi->obtenerCategoria($id, $token);

        if (!$categoria) abort(404);

        return view('admin.categorias.show', compact('categoria'));
    }

    /**
     * Formulario para editar una categoría existente.
     */
    public function edit(int $id)
    {
        $this->ensureAdmin();
        $token = $this->getToken();
        $categoria = $this->muebleApi->obtenerCategoria($id, $token);

        if (!$categoria) abort(404);

        return view('admin.categorias.edit', compact('categoria'));
    }

    /**
     * Actualiza la categoría vía la API.
     */
    public function update(Request $request, int $id)
    {
        $this->ensureAdmin();
        $token = $this->getToken();

        $result = $this->muebleApi->actualizarCategoria($id, [
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
        ], $token);

        if ($result['status'] === 200) {
            return redirect()->route('categorias.index')->with('success', 'Categoría actualizada.');
        }

        return back()->withErrors(['error' => 'Error al actualizar la categoría.']);
    }

    /**
     * Elimina la categoría vía la API.
     */
    public function destroy(int $id)
    {
        $this->ensureAdmin();
        $token = $this->getToken();

        $result = $this->muebleApi->eliminarCategoria($id, $token);

        if ($result['status'] === 200) {
            return redirect()->route('categorias.index')->with('success', 'Categoría eliminada.');
        }

        return back()->withErrors(['error' => 'Error al eliminar la categoría.']);
    }

    /**
     * Busca categorías.
     */
    public function buscar(Request $request)
    {
        return $this->index($request);
    }
}

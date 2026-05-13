<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Services\MuebleApiService;

class AdminProductoController extends Controller
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
     * Muestra todos los productos (desde la API de Muebles).
     */
    public function index(Request $request)
    {
        $this->ensureAdmin();

        $filtros = [];
        if ($request->filled('buscar')) {
            $filtros['buscar'] = $request->buscar;
        }

        $token = $this->getToken();

        // Obtener muebles desde la API
        $paginator = $this->muebleApi->listarMuebles($filtros, $token);
        $listaProductos = $paginator->items();

        return view('admin.productos.index', compact('listaProductos'));
    }

    /**
     * Formulario para crear un producto nuevo.
     * Las categorías se obtienen de la API de Muebles.
     */
    public function create()
    {
        $this->ensureAdmin();
        $token = $this->getToken();
        $listaCategorias = collect($this->muebleApi->listarCategorias($token));
        return view('admin.productos.create', compact('listaCategorias'));
    }

    /**
     * Guarda un producto nuevo enviándolo a la API de Muebles.
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'nombre'          => 'required',
            'descripcion'     => 'required',
            'precio'          => 'required|numeric',
            'stock'           => 'required|integer',
            'color_principal' => 'nullable|string',
            'materiales'      => 'nullable|string',
            'categoria_id'    => 'nullable|array',
        ]);

        $token = $this->getToken();

        // Mapear campos del formulario UT3 a los campos de la API de Muebles
        $datos = [
            'nombre'       => $request->nombre,
            'descripcion'  => $request->descripcion,
            'precio'       => $request->precio,
            'stock'        => $request->stock,
            'color'        => $request->color_principal,
            'material'     => $request->materiales,
            'categoria_id' => $request->categoria_id[0] ?? 1, // La API usa una sola categoría
        ];

        $result = $this->muebleApi->crearMueble($datos, $token);

        if ($result['status'] === 201) {
            return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
        }

        return back()->withErrors(['error' => $result['body']['message'] ?? 'Error al crear el producto.'])->withInput();
    }

    /**
     * Muestra un producto concreto (desde la API).
     */
    public function show(int $id)
    {
        $this->ensureAdmin();
        $token = $this->getToken();
        $producto = $this->muebleApi->obtenerMueble($id, $token);

        if (!$producto) abort(404);

        return view('admin.productos.show', compact('producto'));
    }

    /**
     * Formulario para editar un producto existente.
     */
    public function edit(int $id)
    {
        $this->ensureAdmin();
        $token = $this->getToken();
        $producto = $this->muebleApi->obtenerMueble($id, $token);
        $listaCategorias = collect($this->muebleApi->listarCategorias($token));

        if (!$producto) abort(404);

        return view('admin.productos.edit', compact('producto', 'listaCategorias'));
    }

    /**
     * Actualiza el producto enviando los cambios a la API.
     */
    public function update(Request $request, int $id)
    {
        $this->ensureAdmin();

        $request->validate([
            'nombre'          => 'required',
            'descripcion'     => 'required',
            'precio'          => 'required|numeric',
            'stock'           => 'required|integer',
            'color_principal' => 'nullable|string',
            'materiales'      => 'nullable|string',
            'categoria_id'    => 'nullable|array',
        ]);

        $token = $this->getToken();

        $datos = [
            'nombre'       => $request->nombre,
            'descripcion'  => $request->descripcion,
            'precio'       => $request->precio,
            'stock'        => $request->stock,
            'color'        => $request->color_principal,
            'material'     => $request->materiales,
            'categoria_id' => $request->categoria_id[0] ?? 1,
        ];

        $result = $this->muebleApi->actualizarMueble($id, $datos, $token);

        if ($result['status'] === 200) {
            return redirect()->route('productos.index')->with('success', 'Producto actualizado.');
        }

        return back()->withErrors(['error' => $result['body']['message'] ?? 'Error al actualizar.'])->withInput();
    }

    /**
     * Elimina un producto vía la API.
     */
    public function destroy(int $id)
    {
        $this->ensureAdmin();
        $token = $this->getToken();

        $result = $this->muebleApi->eliminarMueble($id, $token);

        if ($result['status'] === 200) {
            return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
        }

        return back()->withErrors(['error' => 'Error al eliminar el producto.']);
    }

    /**
     * Busca productos usando el mismo filtro que el listado.
     */
    public function buscar(Request $request)
    {
        return $this->index($request);
    }
}

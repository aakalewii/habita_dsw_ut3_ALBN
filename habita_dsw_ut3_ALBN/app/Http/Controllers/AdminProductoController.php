<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminProductoController extends Controller
{
    private function ensureAdmin(): void
    {
        if (!Auth::check() || Auth::user()->role?->nombre !== 'Administrador') {
            abort(403);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->ensureAdmin();
        if ($request->filled('buscar')) {
            $listaProductos = Producto::where('nombre', 'like', '%'.$request->buscar.'%')->get();
        } else {
            $listaProductos = Producto::all();
        }
        return view('admin.productos.index', compact('listaProductos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->ensureAdmin();
        $listaCategorias = Categoria::all();

        return view('admin.productos.create', compact('listaCategorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required',
            'stock' => 'required',
            'materiales' => 'required',
            'dimensiones' => 'required',
            'color_principal'=> 'required',
            'imagen_principal'=> 'nullable|array',
            'imagen_principal.*'=> 'image|max:2048',
            'destacado' => 'required',
            'categoria_id'=> 'nullable|array'
        ]);

        $producto = new Producto();
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->materiales = $request->materiales;
        $producto->dimensiones = $request->dimensiones;
        $producto->color_principal = $request->color_principal;
        $producto->destacado = $request->destacado;

        if ($request->hasFile('imagen_principal')) {
            $paths = [];
            foreach ($request->file('imagen_principal') as $file) {
                $paths[] = $file->store('productos', 'public');
            }
            $producto->imagen_principal = $paths;
        }

        $producto->save();
        $resultado = $producto->categorias()->sync($request->categoria_id);

        return redirect()->route('productos.index', compact('resultado'));

    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $this->ensureAdmin();
        $producto = Producto::find($id);
        return view('admin.productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $this->ensureAdmin();
        $producto = Producto::find($id);
        $listaCategorias = Categoria::all();
        return view('admin.productos.edit', compact('producto'), compact('listaCategorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->ensureAdmin();
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required',
            'stock',
            'materiales' => 'required',
            'dimensiones' => 'required',
            'color_principal'=> 'required',
            'imagen_principal'=> 'nullable|array',
            'imagen_principal.*'=> 'image|max:2048',
            'destacado' => 'required',
            'categoria_id'=> 'required|array'
        ]);

        $producto= Producto::find($id);
        $imagenesActuales = $producto->imagen_principal ?? [];
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->materiales = $request->materiales;
        $producto->dimensiones = $request->dimensiones;
        $producto->color_principal = $request->color_principal;
        $producto->destacado = $request->destacado;

        if ($request->boolean('eliminar_imagenes')) {
            foreach ($imagenesActuales as $rutaBorrar) {
                Storage::disk('public')->delete($rutaBorrar);
            }
            $imagenesFiltradas = [];
        } else {
            $imagenesFiltradas = $imagenesActuales;
        }

        if ($request->hasFile('imagen_principal')) {
            foreach ($request->file('imagen_principal') as $file) {
                $imagenesFiltradas[] = $file->store('productos', 'public');
            }
        }

        $producto->imagen_principal = $imagenesFiltradas;

        $producto->update();
        $resultado = $producto->categorias()->sync($request->categoria_id);

        return redirect()->route('productos.index', compact('resultado'));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->ensureAdmin();
        $producto= Producto::find($id);
        $producto->categorias()->sync([]);

        $resultado = $producto->delete();
        return redirect()->route('productos.index', Compact('resultado'));
    }

    public function buscar(Request $request){
        return $this->index($request);
    }
}

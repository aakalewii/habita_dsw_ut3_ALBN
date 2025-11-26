<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class AdminProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $listaProductos = Producto::all();
        return view('admin.productos.index', compact('listaProductos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $listaCategorias = Categoria::all();

        return view('admin.productos.create', compact('listaCategorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required',
            'stock',
            'materiales' => 'required',
            'dimensiones' => 'required',
            'color_principal'=> 'required',
            'imagen_principal'=> 'nullable|image|max:2048',
            'destacado' => 'required',
            'categoria_id'=> 'required|array'
        ]);

        $producto = new Producto();
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->materiales = $request->materiales;
        $producto->dimensiones = $request->dimensiones;
        $producto->color_principal = $request->color_principal;
        $producto->imagen_principal = $request->imagen_principal;
        $producto->destacado = $request->destacado;
        $producto->nombre = $request->nombre;

        if ($request->hasFile('imagen_principal')) {
            $producto['imagen_principal'] = $request->file('imagen_principal')->store('productos', 'public');
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
        $producto = Producto::find($id);
        return view('admin.productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $producto = Producto::find($id);
        $listaCategorias = Categoria::all();
        return view('admin.productos.edit', compact('producto'), compact('listaCategorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required',
            'stock',
            'materiales' => 'required',
            'dimensiones' => 'required',
            'color_principal'=> 'required',
            'imagen_principal'=> 'nullable|image|max:2048',
            'destacado' => 'required',
            'categoria_id'=> 'required|array'
        ]);

        $producto= Producto::find($id);
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->materiales = $request->materiales;
        $producto->dimensiones = $request->dimensiones;
        $producto->color_principal = $request->color_principal;
        $producto->imagen_principal = $request->imagen_principal;
        $producto->destacado = $request->destacado;
        $producto->nombre = $request->nombre;

        if ($request->hasFile('imagen_principal')) {
            $producto['imagen_principal'] = $request->file('imagen_principal')->store('productos', 'public');
        }

        $producto->update();
        $resultado = $producto->categorias()->sync($request->categoria_id);

        return redirect()->route('productos.index', compact('resultado'));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $producto= Producto::find($id);
        $producto->categorias()->sync([]);

        $resultado = $producto->delete();
        return redirect()->route('productos.index', Compact('resultado'));
    }
}

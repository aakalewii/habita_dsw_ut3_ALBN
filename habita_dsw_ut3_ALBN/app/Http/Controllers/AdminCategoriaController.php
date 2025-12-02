<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCategoriaController extends Controller
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
            $listaCategorias = Categoria::where('nombre', 'like', '%'.$request->buscar.'%')->get();
        } else {
            $listaCategorias = Categoria::all();
        }
        return view('admin.categorias.index', compact('listaCategorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->ensureAdmin();
        return view('admin.categorias.create');
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
        ]);

        $categoria = new Categoria();
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;

        $resultado = $categoria->save();
        return redirect()->route('categorias.index', compact('resultado'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        $this->ensureAdmin();
        return view('admin.categorias.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        $this->ensureAdmin();
        return view('admin.categorias.edit', Compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->ensureAdmin();
        $categoria = Categoria::find($id);
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;

        $resultado = $categoria->update();

        return redirect()->route('categorias.index', Compact('resultado'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->ensureAdmin();
        $categoria = Categoria::find($id);
        $resultado = $categoria->delete();
        return redirect()->route('categorias.index', Compact('resultado'));
    }

    public function buscar(Request $request){
        return $this->index($request);
    }
}

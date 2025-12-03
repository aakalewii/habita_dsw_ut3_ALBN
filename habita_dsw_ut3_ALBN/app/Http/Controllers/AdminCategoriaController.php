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

    // Muestra todas las categorias y permite filtrarlas por nombre
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

    // Enseña el formulario para crear una categoria nueva
    public function create()
    {
        $this->ensureAdmin();
        return view('admin.categorias.create');
    }

    // Guarda una categoria recien creada con sus datos
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

    // Muestra una categoria concreta por su id
    public function show(Categoria $categoria)
    {
        $this->ensureAdmin();
        return view('admin.categorias.show', compact('categoria'));
    }

    // Enseña el formulario para editar una categoria existente
    public function edit(Categoria $categoria)
    {
        $this->ensureAdmin();
        return view('admin.categorias.edit', Compact('categoria'));
    }

    // Actualiza la categoria indicada con los datos del formulario
    public function update(Request $request, string $id)
    {
        $this->ensureAdmin();
        $categoria = Categoria::find($id);
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;

        $resultado = $categoria->update();

        return redirect()->route('categorias.index', Compact('resultado'));
    }

    // Borra la categoria seleccionada
    public function destroy(string $id)
    {
        $this->ensureAdmin();
        $categoria = Categoria::find($id);
        $resultado = $categoria->delete();
        return redirect()->route('categorias.index', Compact('resultado'));
    }

    // Busca categorias usando el mismo filtro que el listado
    public function buscar(Request $request){
        return $this->index($request);
    }
}

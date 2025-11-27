<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function galeria(Request $request)
    {
        // Trae los productos y las categorías en una única consulta
        $query = Producto::with('categorias');

        // Búsqueda por nombre
        // filled('buscar') verifica si el campo de búsqueda no está vacío.
        // where('nombre', 'like', '%texto%') Filtra productos cuyo nombre contenga el texto introducido.
        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        //Busqueda por descripcion
        if ($request->filled('descripcion')) {
            $query->where('descripcion', 'like', '%' . $request->descripcion . '%');
        }

        //Rango precio
        

        // Filtro por categoría
        // Si el usuario selecciona una categoría, se añade un filtro a la consulta.
        // Solo se mostrarán los productos que pertenezcan a esa categoría.
        if ($request->filled('categoria_id')) {
            $query->whereHas('categorias', function ($categoriaQuery) use ($request) {
                $categoriaQuery->where('categorias.id', $request->categoria_id);
            });
        }

        //Filtro por color
        if ($request->filled('color_principal')) {
            $query->where('color_principal', $request->color_principal);
        }

        // Ejecuta la consulta final con todos los filtros aplicados.
        // Divide el resultado en páginas de 12 productos por página.
        $listaProductos = $query->paginate(12);
        $categorias = Categoria::all();
        $colores = Producto::query()
            ->whereNotNull('color_principal')
            ->select('color_principal')
            ->distinct()
            ->orderBy('color_principal')
            ->pluck('color_principal');

        return view('User/principal', compact('listaProductos', 'categorias', 'colores'));
    }

    public function show(Producto $producto)
{
    return view('User.show', compact('producto'));
}
}

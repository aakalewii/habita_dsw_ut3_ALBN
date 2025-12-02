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

        // Búsqueda por nombre o descripción desde la misma barra
        if ($request->filled('buscar')) {
            $query->where(function ($searchQuery) use ($request) {
                $searchQuery->where('nombre', 'like', '%' . $request->buscar . '%')
                    ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            });
        }

        // Filtro por rango de precios
        // Si el usuario especifica un precio mínimo, se añaden a la consulta los productos con precio mayor o igual.
        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', $request->precio_min);
        }
        // Si el usuario especifica un precio máximo, se añaden los productos con precio menor o igual.
        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', $request->precio_max);
        }

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

        // Orden por nombre
        if ($request->filled('orden')) {
            if ($request->orden === 'nombre_asc') {
                $query->orderBy('nombre', 'asc');
            } elseif ($request->orden === 'nombre_desc') {
                $query->orderBy('nombre', 'desc');
            }
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

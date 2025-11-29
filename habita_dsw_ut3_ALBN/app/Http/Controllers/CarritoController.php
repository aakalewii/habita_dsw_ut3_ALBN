<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CarritoController extends Controller
{
    /**
     * Obtiene o crea el carrito activo para la sesión actual.
     */
    private function getCarritoActivo()
    {
        $sessionId = Session::getId();
        $userId = Auth::id();

        // REQUISITO: "Un usuario puede tener carritos simultáneos (uno por pestaña/navegador)"
        // Para cumplir esto estrictamente, el carrito debe buscarse ÚNICAMENTE por sesion_id.
        // Si buscáramos por user_id, podríamos recuperar un carrito de otra pestaña (Chrome) en esta (Firefox),
        // lo cual violaría la separación de "uno por pestaña".
        // El user_id se guarda solo para historial/referencia.

        $carrito = Carrito::where('sesion_id', $sessionId)
            ->activo()
            ->first();

        if (!$carrito) {
            $carrito = Carrito::create([
                'sesion_id' => $sessionId,
                'user_id' => $userId,
                'estado' => 'activo',
                'total' => 0
            ]);
        } else {
            // Si el usuario se acaba de loguear, actualizamos el user_id del carrito actual
            if ($userId && !$carrito->user_id) {
                $carrito->user_id = $userId;
                $carrito->save();
            }
        }

        return $carrito;
    }

    public function index()
    {
        $carrito = $this->getCarritoActivo();
        $items = $carrito->items()->with('producto')->get();

        $subtotal = $items->sum(function ($item) {
            return $item->precio_unitario * $item->cantidad;
        });

        $impuestos = $subtotal * 0.10; // 10% impuestos simulados
        $total = $subtotal + $impuestos;

        // Actualizamos el total en la BD
        $carrito->total = $total;
        $carrito->save();

        return view('carrito.index', compact('carrito', 'items', 'subtotal', 'impuestos', 'total'));
    }

    public function add(Request $request, $productoId)
    {
        $producto = Producto::findOrFail($productoId);
        $carrito = $this->getCarritoActivo();
        $cantidadAAnadir = $request->input('cantidad', 1);

        // Verificar si ya existe en el carrito
        $item = $carrito->items()->where('producto_id', $productoId)->first();
        $cantidadActualEnCarrito = $item ? $item->cantidad : 0;
        $nuevaCantidadTotal = $cantidadActualEnCarrito + $cantidadAAnadir;

        // Validación de Stock
        if ($nuevaCantidadTotal > $producto->stock) {
            return back()->with('error', 'No hay suficiente stock. Stock disponible: ' . $producto->stock);
        }

        if ($item) {
            $item->cantidad = $nuevaCantidadTotal;
            $item->save();
        } else {
            $carrito->items()->create([
                'producto_id' => $productoId,
                'cantidad' => $cantidadAAnadir,
                'precio_unitario' => $producto->precio,
            ]);
        }

        return redirect()->route('carrito.index')->with('success', 'Producto añadido al carrito.');
    }

    public function update(Request $request, $itemId)
    {
        $request->validate(['cantidad' => 'required|integer|min:1']);
        $cantidadNueva = $request->input('cantidad');

        $item = CarritoItem::findOrFail($itemId);
        $producto = $item->producto;

        // Validación de Stock
        if ($cantidadNueva > $producto->stock) {
            return back()->with('error', 'No hay suficiente stock. Máximo: ' . $producto->stock);
        }

        $item->cantidad = $cantidadNueva;
        $item->save();

        return back()->with('success', 'Cantidad actualizada.');
    }

    public function remove($itemId)
    {
        $item = CarritoItem::findOrFail($itemId);
        $item->delete();

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    public function clear()
    {
        $carrito = $this->getCarritoActivo();
        $carrito->items()->delete();

        return back()->with('success', 'Carrito vaciado.');
    }

    public function comprar()
    {
        $carrito = $this->getCarritoActivo();

        if ($carrito->items()->count() == 0) {
            return back()->with('error', 'El carrito está vacío.');
        }

        // REQUISITO: "Al comprar el carrito, no eliminamos la relación de productos de la base de datos. 
        // Dejamos almacenados los datos y vaciamos el carrito actual del usuario."

        // Para cumplir esto, marcamos el carrito actual como 'completado'
        // Esto preserva los datos en la BD pero "vacía" el carrito activo para el usuario (ya que getCarritoActivo buscará uno 'activo')

        $carrito->estado = 'completado';
        $carrito->save();

        // Opcional: Restar stock real de los productos (si se requiere persistencia de stock real)
        // El requisito dice "Validación de stock", y "Si no hay stock: mostrar error".
        // No especifica explícitamente descontar del stock de la tabla productos al comprar, 
        // pero es lo lógico en un sistema real. Lo haré para consistencia.
        foreach ($carrito->items as $item) {
            $producto = $item->producto;
            $producto->stock -= $item->cantidad;
            $producto->save();
        }

        return redirect()->route('productos.galeria')->with('success', 'Compra realizada con éxito. ¡Gracias!');
    }
}

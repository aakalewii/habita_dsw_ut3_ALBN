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

        // 1. Buscar carrito de la sesión actual
        $carrito = Carrito::where('sesionId', $sessionId)
            ->activo()
            ->first();

        // 2. Lógica de recuperación de carrito de usuario
        if ($userId) {
            // Buscar si el usuario tiene OTRO carrito activo en la BD (ej. de una sesión anterior o seeded)
            $oldCart = Carrito::where('user_id', $userId)
                ->where('id', '!=', $carrito?->id) // Que no sea el que acabamos de encontrar
                ->activo()
                ->latest()
                ->first();

            if ($oldCart) {
                // Si encontramos un carrito antiguo del usuario...
                // Y el carrito actual de la sesión no existe O está vacío...
                if (!$carrito || $carrito->items()->count() == 0) {
                    // ... Descartamos el carrito vacío actual (si existe)
                    if ($carrito) {
                        $carrito->delete();
                    }

                    // ... Y recuperamos el antiguo asignándole la sesión actual
                    $carrito = $oldCart;
                    $carrito->sesionId = $sessionId;
                    $carrito->save();
                }
            }
        }

        // 3. Si todavía no tenemos carrito, crear uno nuevo
        if (!$carrito) {
            $carrito = Carrito::create([
                'sesionId' => $sessionId,
                'user_id' => $userId,
                'estado' => 'activo',
                'total' => 0
            ]);
        } else {
            // Si el carrito existe y es anónimo, asignarlo al usuario logueado
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

        $carrito->estado = 'completado';
        $carrito->save();

        foreach ($carrito->items as $item) {
            $producto = $item->producto;
            $producto->stock -= $item->cantidad;
            $producto->save();
        }

        return redirect()->route('productos.galeria')->with('success', 'Compra realizada con éxito. ¡Gracias!');
    }
}

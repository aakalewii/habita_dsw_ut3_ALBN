<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Services\ApiMueblesService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CarritoController extends Controller
{
    public function __construct(
        protected ApiMueblesService $apiMuebles
    ) {
    }

    /**
     * Datos del mueble en la API externa (id del catálogo remoto).
     */
    private function muebleRemoto(int $muebleId): ?array
    {
        $respuesta = $this->apiMuebles->verMueble($muebleId);
        if (!$respuesta->successful()) {
            return null;
        }

        return $this->extractData($respuesta);
    }

    /**
     * Obtiene o crea el carrito activo para la sesión actual.
     */
    private function getCarritoActivo(): Carrito
    {
        $sessionId = Session::getId();
        $userId = Auth::id() ?? Session::get('usuario_id');

        $carrito = Carrito::where('sesionId', $sessionId)
            ->activo()
            ->first();

        if ($userId) {
            $oldCart = Carrito::where('user_id', $userId)
                ->where('id', '!=', $carrito?->id)
                ->activo()
                ->latest()
                ->first();

            if ($oldCart) {
                if (!$carrito || $carrito->items()->count() == 0) {
                    if ($carrito) {
                        $carrito->delete();
                    }

                    $carrito = $oldCart;
                    $carrito->sesionId = $sessionId;
                    $carrito->save();
                }
            }
        }

        if (!$carrito) {
            $carrito = Carrito::create([
                'sesionId' => $sessionId,
                'user_id' => $userId,
                'estado' => 'activo',
                'total' => 0,
            ]);
        } elseif ($userId && !$carrito->user_id) {
            $carrito->user_id = $userId;
            $carrito->save();
        }

        return $carrito;
    }

    public function index()
    {
        $carrito = $this->getCarritoActivo();
        $items = $carrito->items()->get();

        $subtotal = $items->sum(function ($item) {
            return $item->precio_unitario * $item->cantidad;
        });

        $impuestos = $subtotal * 0.10;
        $total = $subtotal + $impuestos;

        $carrito->total = $total;
        $carrito->save();

        return view('carrito.index', compact('carrito', 'items', 'subtotal', 'impuestos', 'total'));
    }

    public function add(Request $request, int $productoId)
    {
        $mueble = $this->muebleRemoto($productoId);
        if (!$mueble) {
            return back()->with('error', 'No se encontró el mueble en el catálogo.');
        }

        $carrito = $this->getCarritoActivo();
        $cantidadAAnadir = (int) $request->input('cantidad', 1);
        if ($cantidadAAnadir < 1) {
            $cantidadAAnadir = 1;
        }

        $stock = (int) ($mueble['stock'] ?? 0);
        $item = $carrito->items()->where('producto_id', $productoId)->first();
        $cantidadActualEnCarrito = $item ? (int) $item->cantidad : 0;
        $nuevaCantidadTotal = $cantidadActualEnCarrito + $cantidadAAnadir;

        if ($nuevaCantidadTotal > $stock) {
            return back()->with('error', 'No hay suficiente stock. Stock disponible: ' . $stock);
        }

        if ($item) {
            $item->cantidad = $nuevaCantidadTotal;
            $item->nombre = $mueble['nombre'] ?? $item->nombre;
            $item->precio_unitario = $mueble['precio'] ?? $item->precio_unitario;
            $item->save();
        } else {
            $carrito->items()->create([
                'producto_id' => $productoId,
                'nombre' => $mueble['nombre'] ?? 'Mueble #' . $productoId,
                'cantidad' => $cantidadAAnadir,
                'precio_unitario' => $mueble['precio'] ?? 0,
            ]);
        }

        return redirect()->route('carrito.index')->with('success', 'Producto añadido al carrito.');
    }

    public function update(Request $request, int $itemId)
    {
        $request->validate(['cantidad' => 'required|integer|min:1']);
        $cantidadNueva = (int) $request->input('cantidad');

        $carrito = $this->getCarritoActivo();
        $item = $carrito->items()->where('id', $itemId)->firstOrFail();

        $mueble = $this->muebleRemoto((int) $item->producto_id);
        if (!$mueble) {
            return back()->with('error', 'No se pudo comprobar el stock del mueble.');
        }

        $stock = (int) ($mueble['stock'] ?? 0);
        if ($cantidadNueva > $stock) {
            return back()->with('error', 'No hay suficiente stock. Máximo: ' . $stock);
        }

        $item->cantidad = $cantidadNueva;
        $item->save();

        return back()->with('success', 'Cantidad actualizada.');
    }

    public function remove(int $itemId)
    {
        $carrito = $this->getCarritoActivo();
        $carrito->items()->where('id', $itemId)->delete();

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

        foreach ($carrito->items as $item) {
            $mueble = $this->muebleRemoto((int) $item->producto_id);
            if (!$mueble) {
                return back()->with('error', 'No se pudo validar el mueble: ' . $item->nombre);
            }
            $stock = (int) ($mueble['stock'] ?? 0);
            if ($stock < (int) $item->cantidad) {
                return back()->with(
                    'error',
                    'Stock insuficiente para "' . $item->nombre . '". Disponible: ' . $stock
                );
            }
        }

        $carrito->estado = 'completado';
        $carrito->save();

        return redirect()->route('productos.galeria')->with('success', 'Compra realizada con éxito. ¡Gracias!');
    }
}

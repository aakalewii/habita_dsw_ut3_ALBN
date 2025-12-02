<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Producto;
use App\Models\Carrito;
use App\Models\CarritoItem;
use Illuminate\Support\Facades\Session;

class CarritoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Asegurar que hay productos
        if (Producto::count() == 0) {
            Producto::factory()->create(['stock' => 10, 'precio' => 100]);
        }
    }

    public function test_carrito_flow_completo()
    {
        // 1. Obtener un producto con stock
        $producto = Producto::where('stock', '>', 0)->first();
        $this->assertNotNull($producto, 'Debe haber productos con stock para probar.');

        // 2. Añadir al carrito (Simulando sesión)
        $response = $this->post(route('carrito.add', $producto->id), [
            'cantidad' => 1
        ]);

        $response->assertRedirect(route('carrito.index'));
        $response->assertSessionHas('success');

        // Verificar BD
        $this->assertDatabaseHas('carritos', [
            'estado' => 'activo'
        ]);
        $carrito = Carrito::activo()->first();
        $this->assertNotNull($carrito);
        $this->assertEquals(Session::getId(), $carrito->sesion_id);

        $this->assertDatabaseHas('carrito_items', [
            'carrito_id' => $carrito->id,
            'producto_id' => $producto->id,
            'cantidad' => 1
        ]);

        // 3. Ver carrito y cálculos
        $response = $this->get(route('carrito.index'));
        $response->assertStatus(200);
        $response->assertSee($producto->nombre);

        // Verificar cálculos en el modelo/controlador (se actualizan al ver index)
        $carrito->refresh();
        $expectedSubtotal = $producto->precio * 1;
        $expectedImpuestos = $expectedSubtotal * 0.10;
        $expectedTotal = $expectedSubtotal + $expectedImpuestos;

        $this->assertEquals($expectedTotal, $carrito->total);

        // 4. Actualizar cantidad
        $item = $carrito->items()->first();
        $response = $this->put(route('carrito.update', $item->id), [
            'cantidad' => 2
        ]);
        $response->assertSessionHas('success');

        $item->refresh();
        $this->assertEquals(2, $item->cantidad);

        // 5. Validar Stock (Intentar añadir más del stock)
        $stockExcedido = $producto->stock + 5;
        $response = $this->put(route('carrito.update', $item->id), [
            'cantidad' => $stockExcedido
        ]);
        $response->assertSessionHas('error'); // Debe dar error

        $item->refresh();
        $this->assertNotEquals($stockExcedido, $item->cantidad); // No debe haber cambiado

        // 6. Simular otra pestaña (Nueva Sesión)
        Session::flush(); // Limpiar sesión actual
        Session::start(); // Iniciar nueva
        $newSessionId = Session::getId();

        $this->assertNotEquals($carrito->sesion_id, $newSessionId);

        // Al entrar al index con nueva sesión, debe estar vacío o crear uno nuevo vacío
        $response = $this->get(route('carrito.index'));
        $carritoNuevo = Carrito::where('sesion_id', $newSessionId)->activo()->first();

        $this->assertNotNull($carritoNuevo);
        $this->assertNotEquals($carrito->id, $carritoNuevo->id); // Son carritos diferentes
        $this->assertEquals(0, $carritoNuevo->items()->count());

        // 7. Comprar (Carrito Nuevo)

        // Añadir algo al nuevo carrito
        $this->post(route('carrito.add', $producto->id), ['cantidad' => 1]);
        $carritoNuevo->refresh();

        $stockAntes = $producto->fresh()->stock;

        $response = $this->post(route('carrito.comprar'));
        $response->assertRedirect(route('productos.galeria'));
        $response->assertSessionHas('success');

        $carritoNuevo->refresh();
        $this->assertEquals('completado', $carritoNuevo->estado);

        // Verificar stock descontado
        $this->assertEquals($stockAntes - 1, $producto->fresh()->stock);

        // Verificar que si entro de nuevo, tengo un carrito nuevo vacío
        $response = $this->get(route('carrito.index'));
        $carritoPostCompra = Carrito::where('sesion_id', $newSessionId)->activo()->first();
        $this->assertNotNull($carritoPostCompra);
        $this->assertNotEquals($carritoNuevo->id, $carritoPostCompra->id);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Carrito;
use App\Models\CarritoItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CarritoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.api_muebles.url' => 'http://localhost:5502/api/v1']);

        Http::fake(function ($request) {
            if (! str_contains($request->url(), '/muebles/')) {
                return Http::response(['message' => 'not found'], 404);
            }

            return Http::response([
                'data' => [
                    'id' => 1,
                    'nombre' => 'Mueble de prueba',
                    'stock' => 10,
                    'precio' => 100,
                    'descripcion' => 'Test',
                    'color' => 'Blanco',
                    'material' => 'Madera',
                ],
            ], 200);
        });
    }

    private function actingAsApiCliente(): self
    {
        Session::put('autorizacion_usuario', true);
        Session::put('usuario_id', 1);
        Session::put('user_rol', 'Cliente');
        Session::put('user_abilities', ['perfil.ver', 'muebles.ver', 'carrito.gestionar', 'pedidos.crear']);

        return $this;
    }

    public function test_carrito_flujo_basico(): void
    {
        $this->actingAsApiCliente();

        $this->post(route('carrito.add', 1), ['cantidad' => 1])
            ->assertRedirect(route('carrito.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('carritos', ['estado' => 'activo']);

        $carrito = Carrito::activo()->first();
        $this->assertNotNull($carrito);
        $this->assertEquals(Session::getId(), $carrito->sesion_id);

        $this->assertDatabaseHas('carrito_items', [
            'carrito_id' => $carrito->id,
            'producto_id' => 1,
            'cantidad' => 1,
        ]);

        $this->get(route('carrito.index'))->assertStatus(200)->assertSee('Mueble de prueba');

        $carrito->refresh();
        $expectedSubtotal = 100.0;
        $expectedTotal = $expectedSubtotal * 1.10;
        $this->assertEquals($expectedTotal, (float) $carrito->total);

        $item = $carrito->items()->first();
        $this->put(route('carrito.update', $item->id), ['cantidad' => 2])
            ->assertSessionHas('success');

        $item->refresh();
        $this->assertEquals(2, $item->cantidad);

        $this->put(route('carrito.update', $item->id), ['cantidad' => 50])
            ->assertSessionHas('error');

        $item->refresh();
        $this->assertEquals(2, $item->cantidad);

        $this->post(route('carrito.comprar'))
            ->assertRedirect(route('productos.galeria'))
            ->assertSessionHas('success');

        $carrito->refresh();
        $this->assertEquals('completado', $carrito->estado);
    }

    public function test_carrito_remove_item(): void
    {
        $this->actingAsApiCliente();

        $this->post(route('carrito.add', 1), ['cantidad' => 1]);
        $carrito = Carrito::activo()->first();
        $item = $carrito->items()->first();

        $this->delete(route('carrito.remove', $item->id))
            ->assertSessionHas('success');

        $this->assertNull(CarritoItem::find($item->id));
    }
}

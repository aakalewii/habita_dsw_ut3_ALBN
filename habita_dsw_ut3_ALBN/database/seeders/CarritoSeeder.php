<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\User;
use App\Models\Producto;

class CarritoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener usuarios específicos
        $barry = User::where('email', 'barry@gmail.com')->first();
        $cliente2 = User::where('email', 'cliente2@gmail.com')->first();

        // Si no existen, no podemos crear sus carritos (aunque deberían existir por UserSeeder)
        if (!$barry || !$cliente2) {
            $this->command->info('Usuarios Barry o Cliente2 no encontrados. Asegúrate de correr UserSeeder primero.');
            return;
        }

        // --- Carritos para Barry ---

        // 1. Carrito Activo para Barry
        $carritoBarryActivo = Carrito::factory()->create([
            'user_id' => $barry->id,
            'estado' => 'activo',
            'total' => 0, // Se recalculará
        ]);

        $this->addItemsToCart($carritoBarryActivo, 3);

        // 2. Carrito Completado para Barry
        $carritoBarryCompletado = Carrito::factory()->create([
            'user_id' => $barry->id,
            'estado' => 'completado',
            'total' => 0, // Se recalculará
        ]);

        $this->addItemsToCart($carritoBarryCompletado, 2);


        // --- Carritos para Cliente2 ---

        // 1. Carrito Activo para Cliente2
        $carritoCliente2Activo = Carrito::factory()->create([
            'user_id' => $cliente2->id,
            'estado' => 'activo',
            'total' => 0, // Se recalculará
        ]);

        $this->addItemsToCart($carritoCliente2Activo, 4);

        // Crear algunos carritos anónimos/otros usuarios para rellenar
        Carrito::factory()
            ->count(5)
            ->create()
            ->each(function ($carrito) {
                $this->addItemsToCart($carrito, rand(1, 3));
            });
    }

    private function addItemsToCart(Carrito $carrito, int $count): void
    {
        CarritoItem::factory()
            ->count($count)
            ->state(function (array $attributes) use ($carrito) {
                return [
                    'carrito_id' => $carrito->id,
                    'producto_id' => Producto::inRandomOrder()->first()?->id ?? Producto::factory(),
                ];
            })
            ->create();

        // Recalcular total
        $total = $carrito->items->sum(function ($item) {
            return $item->cantidad * $item->precio_unitario;
        });

        $carrito->update(['total' => $total]);
    }
}

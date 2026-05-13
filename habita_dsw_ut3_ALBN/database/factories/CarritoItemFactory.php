<?php

namespace Database\Factories;

use App\Models\CarritoItem;
use App\Models\Carrito;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarritoItemFactory extends Factory
{
    protected $model = CarritoItem::class;

    public function definition(): array
    {
        return [
            'carrito_id' => Carrito::factory(),
            'producto_id' => $this->faker->numberBetween(1, 9999),
            'nombre' => $this->faker->words(3, true),
            'cantidad' => $this->faker->numberBetween(1, 5),
            'precio_unitario' => $this->faker->randomFloat(2, 5, 200),
        ];
    }
}

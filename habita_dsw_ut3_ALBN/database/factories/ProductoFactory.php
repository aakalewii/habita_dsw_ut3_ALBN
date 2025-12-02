<?php

namespace Database\Factories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        $ancho = $this->faker->numberBetween(20, 200);
        $alto = $this->faker->numberBetween(20, 200);
        $fondo = $this->faker->numberBetween(20, 200);

        return [
            'nombre' => $this->faker->unique()->words(3, true),
            'descripcion' => $this->faker->sentence(12),
            'precio' => $this->faker->randomFloat(2, 5, 2000),
            'stock' => $this->faker->numberBetween(0, 500),
            'materiales' => $this->faker->randomElement(['Madera', 'Metal', 'Plastico', 'Vidrio', 'Tela']),
            'dimensiones' => "{$ancho}x{$alto}x{$fondo} cm",
            'color_principal' => $this->faker->safeColorName(),
            'imagen_principal' => [],
            'destacado' => $this->faker->boolean(20),
        ];
    }

    public function destacado(): static
    {
        return $this->state(fn () => ['destacado' => true]);
    }
}

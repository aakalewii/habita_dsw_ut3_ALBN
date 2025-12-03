<?php

namespace Database\Factories;

use App\Models\Carrito;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CarritoFactory extends Factory
{
    protected $model = Carrito::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(function () {
                return [
                    'apellidos' => 'Apellido',
                    'role_id' => \App\Models\Role::where('nombre', 'Cliente')->value('id') ?? 1,
                ];
            }),
            'sesionId' => Str::uuid()->toString(),
            'total' => $this->faker->randomFloat(2, 10, 500),
            'estado' => $this->faker->randomElement(['activo', 'completado']),
        ];
    }
}

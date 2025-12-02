<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categorias')->insert([
            [
                'nombre' => 'Muebles',
                'descripcion' => 'Muebles para el hogar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Decoracion',
                'descripcion' => 'Articulos decorativos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Iluminacion',
                'descripcion' => 'Lamparas y luminarias',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Textiles',
                'descripcion' => 'Alfombras y cojines',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Exterior',
                'descripcion' => 'Muebles y decoracion para exteriores',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

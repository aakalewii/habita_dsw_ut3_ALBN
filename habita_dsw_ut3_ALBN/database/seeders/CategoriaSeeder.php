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
                'nombre' => 'Decoración',
                'descripcion' => 'Artículos decorativos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

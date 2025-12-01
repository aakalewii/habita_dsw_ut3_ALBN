<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $mueblesId = DB::table('categorias')->where('nombre', 'Muebles')->value('id');
        $decoracionId = DB::table('categorias')->where('nombre', 'Decoración')->value('id');

        $prod1 = DB::table('productos')->insertGetId([
            'nombre' => 'Silla de Madera',
            'descripcion' => 'Silla cómoda y resistente.',
            'precio' => 45.50,
            'stock' => 10,
            'materiales' => 'Madera',
            'dimensiones' => '40x40x90',
            'color_principal' => 'Marrón',
            'imagen_principal' => null,
            'destacado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $prod2 = DB::table('productos')->insertGetId([
            'nombre' => 'Lámpara de Mesa',
            'descripcion' => 'Lámpara moderna.',
            'precio' => 25.00,
            'stock' => 5,
            'materiales' => 'Metal',
            'dimensiones' => '20x20x40',
            'color_principal' => 'Negro',
            'imagen_principal' => null,
            'destacado' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Link to categories
        if ($mueblesId) {
            DB::table('categoria_producto')->insert([
                'productos_id' => $prod1,
                'categorias_id' => $mueblesId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($decoracionId) {
            DB::table('categoria_producto')->insert([
                'productos_id' => $prod2,
                'categorias_id' => $decoracionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = DB::table('categorias')->pluck('id', 'nombre');

        $productos = [
            [
                'data' => [
                    'nombre' => 'Silla de Madera',
                    'descripcion' => 'Silla comoda y resistente.',
                    'precio' => 45.50,
                    'stock' => 10,
                    'materiales' => 'Madera',
                    'dimensiones' => '40x40x90',
                    'color_principal' => 'Marron',
                    'imagen_principal' => null,
                    'destacado' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'categorias' => ['Muebles'],
            ],
            [
                'data' => [
                    'nombre' => 'Sofa Modular',
                    'descripcion' => 'Sofa de tres plazas con chaise longue.',
                    'precio' => 320.00,
                    'stock' => 4,
                    'materiales' => 'Tela y madera',
                    'dimensiones' => '250x90x95',
                    'color_principal' => 'Gris',
                    'imagen_principal' => null,
                    'destacado' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'categorias' => ['Muebles'],
            ],
            [
                'data' => [
                    'nombre' => 'Mesa de Centro',
                    'descripcion' => 'Mesa de centro de roble macizo.',
                    'precio' => 120.00,
                    'stock' => 8,
                    'materiales' => 'Madera de roble',
                    'dimensiones' => '110x60x45',
                    'color_principal' => 'Natural',
                    'imagen_principal' => null,
                    'destacado' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'categorias' => ['Muebles', 'Decoracion'],
            ],
            [
                'data' => [
                    'nombre' => 'Estanteria Metalica',
                    'descripcion' => 'Estanteria de 5 niveles para almacenaje.',
                    'precio' => 75.00,
                    'stock' => 12,
                    'materiales' => 'Acero',
                    'dimensiones' => '80x30x180',
                    'color_principal' => 'Negro',
                    'imagen_principal' => null,
                    'destacado' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'categorias' => ['Muebles'],
            ],
            [
                'data' => [
                    'nombre' => 'Lampara de Pie',
                    'descripcion' => 'Lampara alta con pantalla de lino.',
                    'precio' => 68.00,
                    'stock' => 7,
                    'materiales' => 'Metal y lino',
                    'dimensiones' => '40x40x165',
                    'color_principal' => 'Blanco',
                    'imagen_principal' => null,
                    'destacado' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'categorias' => ['Iluminacion'],
            ],
            [
                'data' => [
                    'nombre' => 'Lampara de Mesa',
                    'descripcion' => 'Lampara moderna para escritorio.',
                    'precio' => 25.00,
                    'stock' => 15,
                    'materiales' => 'Metal',
                    'dimensiones' => '20x20x40',
                    'color_principal' => 'Negro',
                    'imagen_principal' => null,
                    'destacado' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'categorias' => ['Iluminacion'],
            ],
            [
                'data' => [
                    'nombre' => 'Alfombra Gris',
                    'descripcion' => 'Alfombra suave de tejido plano.',
                    'precio' => 59.90,
                    'stock' => 20,
                    'materiales' => 'Polipropileno',
                    'dimensiones' => '160x230',
                    'color_principal' => 'Gris',
                    'imagen_principal' => null,
                    'destacado' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'categorias' => ['Textiles', 'Decoracion'],
            ],
            [
                'data' => [
                    'nombre' => 'Cojin Azul',
                    'descripcion' => 'Cojin decorativo con relleno incluido.',
                    'precio' => 12.50,
                    'stock' => 30,
                    'materiales' => 'Algodon',
                    'dimensiones' => '45x45',
                    'color_principal' => 'Azul',
                    'imagen_principal' => null,
                    'destacado' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'categorias' => ['Textiles', 'Decoracion'],
            ],
            [
                'data' => [
                    'nombre' => 'Set Jardin',
                    'descripcion' => 'Mesa y cuatro sillas para exterior.',
                    'precio' => 210.00,
                    'stock' => 5,
                    'materiales' => 'Aluminio y textileno',
                    'dimensiones' => 'Mesa 140x80x74',
                    'color_principal' => 'Antracita',
                    'imagen_principal' => null,
                    'destacado' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'categorias' => ['Exterior'],
            ],
            [
                'data' => [
                    'nombre' => 'Silla Exterior',
                    'descripcion' => 'Silla apilable resistente al clima.',
                    'precio' => 35.00,
                    'stock' => 18,
                    'materiales' => 'Polipropileno',
                    'dimensiones' => '55x55x85',
                    'color_principal' => 'Verde oliva',
                    'imagen_principal' => null,
                    'destacado' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'categorias' => ['Exterior'],
            ],
            [
                'data' => [
                    'nombre' => 'Espejo Decorativo',
                    'descripcion' => 'Espejo redondo con marco de metal.',
                    'precio' => 48.00,
                    'stock' => 9,
                    'materiales' => 'Cristal y metal',
                    'dimensiones' => '80x80',
                    'color_principal' => 'Dorado',
                    'imagen_principal' => null,
                    'destacado' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'categorias' => ['Decoracion'],
            ],
        ];

        foreach ($productos as $producto) {
            $prodId = DB::table('productos')->insertGetId($producto['data']);

            foreach ($producto['categorias'] as $catNombre) {
                if (isset($categorias[$catNombre])) {
                    DB::table('categoria_producto')->insert([
                        'productos_id' => $prodId,
                        'categorias_id' => $categorias[$catNombre],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}

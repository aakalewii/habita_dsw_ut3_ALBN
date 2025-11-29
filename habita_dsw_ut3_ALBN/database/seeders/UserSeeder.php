<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primero, crear los roles si no existen
        $adminRole = Role::firstOrCreate(['nombre' => 'Administrador']);
        $gestorRole = Role::firstOrCreate(['nombre' => 'Gestor']);
        $clienteRole = Role::firstOrCreate(['nombre' => 'Cliente']);

        // Crear usuario Administrador
        User::create([
            'name' => 'Administrador',
            'apellidos' => 'Sistema',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'role_id' => $adminRole->id,
        ]);

        // Crear usuario Gestor 1
        User::create([
            'name' => 'Lenny',
            'apellidos' => 'Moran',
            'email' => 'lenny@gmail.com',
            'password' => Hash::make('123456'),
            'role_id' => $gestorRole->id,
        ]);

        // Crear usuario Gestor 2
        User::create([
            'name' => 'Alvaro',
            'apellidos' => 'Apellido',
            'email' => 'alvaro@gmail.com',
            'password' => Hash::make('123456'),
            'role_id' => $gestorRole->id,
        ]);

        // Crear usuario Cliente 1
        User::create([
            'name' => 'Barry',
            'apellidos' => 'Apellido',
            'email' => 'barry@gmail.com',
            'password' => Hash::make('123456'),
            'role_id' => $clienteRole->id,
        ]);

        // Crear usuario Cliente 2
        User::create([
            'name' => 'cliente2',
            'apellidos' => 'cliente2',
            'email' => 'cliente2@gmail.com',
            'password' => Hash::make('123456'),
            'role_id' => $clienteRole->id,
        ]);
    }
}

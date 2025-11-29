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
            'email' => 'admin@habita.com',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
        ]);

        // Crear usuario Gestor 1
        User::create([
            'name' => 'Juan Gestor',
            'email' => 'gestor1@habita.com',
            'password' => Hash::make('gestor123'),
            'role_id' => $gestorRole->id,
        ]);

        // Crear usuario Gestor 2
        User::create([
            'name' => 'María Gestora',
            'email' => 'gestor2@habita.com',
            'password' => Hash::make('gestor123'),
            'role_id' => $gestorRole->id,
        ]);

        // Crear usuario Cliente 1
        User::create([
            'name' => 'Carlos Cliente',
            'email' => 'cliente1@habita.com',
            'password' => Hash::make('cliente123'),
            'role_id' => $clienteRole->id,
        ]);

        // Crear usuario Cliente 2
        User::create([
            'name' => 'Ana Cliente',
            'email' => 'cliente2@habita.com',
            'password' => Hash::make('cliente123'),
            'role_id' => $clienteRole->id,
        ]);
    }
}

<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Llamar al Seeder de Roles (Crea Roles Admin, Empleado, Cliente y Permisos)
        $this->call(RoleSeeder::class);

        // 2. Crear Categorías y Productos dummy
        // 2. Crear Categorías y Productos dummy (COMENTADO PARA INICIO LIMPIO)
        // \App\Models\Categories::factory(10)
        // ->hasProductos(2)
        // ->create();

        // 3. Crear Usuario Admin
        \App\Models\User::factory()->create([
            'name' => 'prueba',
            'email' => 'i@prueba.com', // CAMBIO: Email solicitado por el usuario
            'password' => Hash::make('password'),
        ])->assignRole('Admin'); // IMPORTANTE: 'Admin' con mayúscula

        // 4. Crear Clientes dummy
        \App\Models\User::factory()
        ->count(9)
        ->create()
        ->each(function ($user) {
            $user->assignRole('Cliente'); // IMPORTANTE: 'Cliente' con mayúscula
        });

    }
}

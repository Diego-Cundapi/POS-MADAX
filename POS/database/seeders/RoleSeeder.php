<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Resetear caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Crear Permisos (Operaciones Granulares)
        // Dashboard / Inventario
        Permission::firstOrCreate(['name' => 'ver_inventario']); 
        
        // Ventas y Cotizaciones
        Permission::firstOrCreate(['name' => 'gestionar_ventas']); 
        Permission::firstOrCreate(['name' => 'gestionar_cotizaciones']);
        
        // Administrativo Estricto
        Permission::firstOrCreate(['name' => 'ver_crm']); // Tablero de ganancias
        Permission::firstOrCreate(['name' => 'gestionar_usuarios']); // CRUD Usuarios

        // 3. Crear Roles y Asignar Permisos
        
        // A) ROLE ADMIN: Tiene todo
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
        $roleAdmin->syncPermissions(Permission::all()); // Sync para asegurar que tenga todos

        // B) ROLE EMPLEADO: Acceso limitado
        $roleEmpleado = Role::firstOrCreate(['name' => 'Empleado']);
        $roleEmpleado->syncPermissions([
            'ver_inventario',
            'gestionar_ventas',
            'gestionar_cotizaciones'
        ]);

        // C) ROLE CLIENTE
        $roleCliente = Role::firstOrCreate(['name' => 'Cliente']);
    }
}

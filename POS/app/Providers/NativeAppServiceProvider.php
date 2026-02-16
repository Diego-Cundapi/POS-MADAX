<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        // 1. Lógica de "Seed" Manual para Producción (Sin validación de Faker/Dev dependencies)
        // 1. Lógica de "Seed" Manual para Producción (Sin validación de Faker/Dev dependencies)
        try {
            // FORZAR MIGRACIÓN AL INICIO (Vital para producción)
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            
            if (\Illuminate\Support\Facades\Schema::hasTable('users') && \App\Models\User::count() === 0) {
                
                // A) Crear Permisos
                $perms = [
                    'ver_inventario', 
                    'gestionar_ventas', 
                    'gestionar_cotizaciones',
                    'ver_crm',
                    'gestionar_usuarios'
                ];

                foreach ($perms as $permName) {
                    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
                }

                // B) Crear Roles
                $roleAdmin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
                $roleAdmin->syncPermissions(\Spatie\Permission\Models\Permission::all());

                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Empleado', 'guard_name' => 'web']);
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Cliente', 'guard_name' => 'web']);

                // C) Crear Usuario Admin
                $user = \App\Models\User::create([
                    'name' => 'prueba',
                    'email' => 'i@prueba.com',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'email_verified_at' => now(),
                ]);

                // D) Asignar Rol
                $user->assignRole($roleAdmin);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('NativeBoot Error: ' . $e->getMessage());
        }

        Window::open()
            ->title('Refaccionaria MADAX')
            ->showDevTools(false);
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '512M',
            'display_errors' => 'On',
            'error_reporting' => 'E_ALL',
            // Habilitar extensiones necesarias para Excel (PhpSpreadsheet)
            // Nota: NativePHP puede requerir que las extensiones estén disponibles en el binario.
            // Intentamos asegurar que se carguen si están disponibles.
            'extension' => [
                'fileinfo',
                'xmlwriter',
                'zip',
                'gd',
                'openssl',
                'mbstring',
            ],
        ];
    }
}

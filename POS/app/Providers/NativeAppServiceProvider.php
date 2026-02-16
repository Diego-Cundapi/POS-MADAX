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
        try {
            // FORZAR MIGRACIÓN AL INICIO (Vital para producción)
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            
            // A) Crear Permisos (SIEMPRE - firstOrCreate es idempotente)
            if (\Illuminate\Support\Facades\Schema::hasTable('permissions')) {
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

                // B) Crear Roles (SIEMPRE - firstOrCreate es idempotente)
                $roleAdmin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
                $roleAdmin->syncPermissions(\Spatie\Permission\Models\Permission::all());

                $roleEmpleado = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Empleado', 'guard_name' => 'web']);
                $roleEmpleado->syncPermissions([
                    'ver_inventario',
                    'gestionar_ventas',
                    'gestionar_cotizaciones',
                ]);

                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Cliente', 'guard_name' => 'web']);

                // Limpiar caché de permisos
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            }

            // C) Crear Usuario Admin SOLO si no hay usuarios
            if (\Illuminate\Support\Facades\Schema::hasTable('users') && \App\Models\User::count() === 0) {
                $user = \App\Models\User::create([
                    'name' => 'prueba',
                    'email' => 'i@prueba.com',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'email_verified_at' => now(),
                ]);
                $user->assignRole('Admin');
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

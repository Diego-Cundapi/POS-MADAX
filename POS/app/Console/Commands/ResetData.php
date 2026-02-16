<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-data {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina todos los datos de negocio excepto roles, permisos y configuraciones clave.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('¿Estás seguro de que quieres borrar todos los datos de negocio? Esta acción es irreversible.')) {
            return;
        }

        $this->info('Iniciando limpieza de datos...');

        Schema::disableForeignKeyConstraints();

        // Lista de tablas a vaciar (TRUNCATE)
        // Se preservan: roles, permissions, role_has_permissions, app_settings, migrations
        $tablesToTruncate = [
            'detalle_cotizaciones',
            'cotizaciones',
            'detalles',
            'pedidos',
            'productos',
            'categories',
            'users',                 // Eliminamos usuarios para limpiar asociaciones, luego recreamos el Admin
            'model_has_roles',       // Relación usuario-rol (se limpia porque se borran usuarios)
            'model_has_permissions', // Relación usuario-permiso (se limpia porque se borran usuarios)
            'personal_access_tokens',
            'password_resets',
            'failed_jobs',
            'jobs'
        ];

        foreach ($tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->info("Tabla '$table' truncada.");
            }
        }

        Schema::enableForeignKeyConstraints();

        $this->info('Datos eliminados. Recreando usuario administrador por defecto...');
        
        // Recrear el usuario Admin inicial
        // Ajusta estos credenciales si es necesario o tómalos de .env
        $user = User::create([
            'name' => 'prueba',
            'email' => 'i@prueba.com',
            'password' => Hash::make('password'),
        ]);
        
        // Asignar rol Admin (El rol ya existe en la BD, no se borró)
        $user->assignRole('Admin');
        
        // Opcional: Si quieres recrear los clientes de prueba del seeder:
        // \App\Models\User::factory()->count(9)->create()->each(function ($u) { $u->assignRole('Cliente'); });

        $this->info('¡Proceso completado! El sistema ha sido reseteado manteniendo roles y configuración.');
    }
}
